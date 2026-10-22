<?php

namespace App\Services\Accounting;

use App\Models\Coa;
use App\Models\JurnalDetail;
use App\Models\JurnalUmum;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CashTransferService
{
    public function balances(): array
    {
        return JurnalDetail::query()->whereHas('jurnalUmum')->whereHas('coa', fn ($q) => $q->whereIn('kode_akun', ['1101', '1102']))
            ->with('coa')->get()->reduce(function ($result, $line) {
                $fund = $line->jenis_dana === 'infak' ? 'infak_sedekah' : ($line->jenis_dana ?: 'amil');
                $code = $line->coa->kode_akun;
                $result[$fund][$code] = ($result[$fund][$code] ?? 0) + (float) $line->debit - (float) $line->credit;
                return $result;
            }, []);
    }

    public function assertAvailable(Coa $account, string $fund, int $amount): void
    {
        $balance = $this->balances()[$fund][$account->kode_akun] ?? 0;
        $total = app(Psak109PostingService::class)->getSaldoKasBank($account);
        if ($amount > $balance || $amount > $total) {
            throw ValidationException::withMessages(['coa_kredit_id' => 'Saldo golongan pada '.$account->nama_akun.' tidak mencukupi (Rp '.number_format(min($balance, $total), 0, ',', '.').'). Pilih sumber lain atau pindahkan saldo Kas–Bank.']);
        }
    }

    public function transfer(array $data): JurnalUmum
    {
        return DB::transaction(function () use ($data) {
            // Urutan kunci selalu sama agar pemindahan bersamaan tidak memakai saldo yang sama.
            $accounts = Coa::whereIn('kode_akun', ['1101', '1102'])->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            $source = $accounts->get($data['source']);
            $target = $accounts->get($data['target']);
            if (! $source || ! $target || $source->id === $target->id) {
                throw ValidationException::withMessages(['source' => 'Pilih akun Kas/Bank organisasi ini dengan sumber dan tujuan berbeda.']);
            }
            $this->assertAvailable($source, $data['fund'], $data['amount']);
            $journal = JurnalUmum::create([
                'tgl' => $data['date'], 'tanggal' => $data['date'],
                'no_referensi' => 'MUT-'.str()->ulid(), 'sumber_tabel' => 'kas_bank_transfer',
                'deskripsi' => $data['description'], 'keterangan' => $data['description'],
                'created_by' => auth('admin')->id(),
            ]);
            foreach ([$target, $source] as $account) {
                $journal->detail()->create(['coa_id' => $account->id, 'jenis_dana' => $data['fund'],
                    'debit' => $account->id === $target->id ? $data['amount'] : 0,
                    'credit' => $account->id === $source->id ? $data['amount'] : 0,
                    'deskripsi' => $data['description']]);
            }
            return $journal;
        });
    }

    public function cancel(int $id): void
    {
        DB::transaction(function () use ($id) {
            Coa::whereIn('kode_akun', ['1101', '1102'])->orderBy('id')->lockForUpdate()->get();
            $journal = JurnalUmum::where('sumber_tabel', 'kas_bank_transfer')->lockForUpdate()->findOrFail($id);
            if (JurnalUmum::where('sumber_tabel', 'kas_bank_reversal')->where('sumber_id', $id)->exists()) {
                throw ValidationException::withMessages(['transfer' => 'Pemindahan ini sudah dibatalkan.']);
            }
            $lines = $journal->detail()->with('coa')->get();
            foreach ($lines as $line) {
                if ($line->debit > 0) $this->assertAvailable($line->coa, $line->jenis_dana, (int) $line->debit);
            }
            $reverse = JurnalUmum::create(['tgl' => today(), 'tanggal' => today(), 'no_referensi' => 'BATAL-'.$journal->id.'-'.str()->ulid(),
                'sumber_tabel' => 'kas_bank_reversal', 'sumber_id' => $id, 'deskripsi' => 'Pembatalan '.$journal->no_referensi, 'created_by' => auth('admin')->id()]);
            foreach ($lines as $line) $reverse->detail()->create(['coa_id' => $line->coa_id, 'jenis_dana' => $line->jenis_dana,
                'debit' => $line->credit, 'credit' => $line->debit, 'deskripsi' => 'Pembatalan pemindahan']);
        });
    }
}
