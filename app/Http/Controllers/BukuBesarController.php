<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\JurnalDetail;
use App\Models\JurnalUmum;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BukuBesarController extends Controller
{
    public static function funds(): array
    {
        return [
            'zakat' => 'Dana Zakat', 'infak_sedekah' => 'Infak/Sedekah',
            'operasional' => 'Operasional', 'wakaf' => 'Wakaf Permanen',
            'wakaf_temporer' => 'Wakaf Temporer', 'amil' => 'Amil',
            'nazhir' => 'Nazhir', 'non_halal' => 'Non-Halal',
        ];
    }

    public function index(Request $request)
    {
        $funds = self::funds();
        $isPrint = $request->routeIs('*.buku-besar.cetak');
        $filters = $request->validate([
            'coa_id' => ['nullable', 'integer'],
            'tanggal_dari' => ['nullable', 'date_format:Y-m-d'],
            'tanggal_sampai' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:tanggal_dari'],
            'dana' => ['nullable', Rule::in(array_keys($funds))],
        ]);
        $from = $filters['tanggal_dari'] ?? now()->startOfMonth()->toDateString();
        $to = $filters['tanggal_sampai'] ?? now()->toDateString();
        if ($to < $from) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'tanggal_sampai' => 'Tanggal akhir harus sama atau setelah tanggal awal.',
            ]);
        }
        $accounts = Coa::orderBy('kode_akun')->get();
        $account = isset($filters['coa_id'])
            ? Coa::findOrFail($filters['coa_id'])
            : ($accounts->firstWhere('kode_akun', '1102') ?? $accounts->first());
        // Scope both the account and journal headers to the active organization.
        $base = JurnalDetail::query()
            ->join('jurnal_umum as ledger_journal', 'ledger_journal.id', '=', 'jurnal_detail.jurnal_id')
            ->whereIn('ledger_journal.id', JurnalUmum::query()->select('id'))
            ->where('jurnal_detail.coa_id', $account?->id ?? 0);
        if (! empty($filters['dana'])) {
            $base->whereIn('jurnal_detail.jenis_dana', $filters['dana'] === 'infak_sedekah'
                ? ['infak', 'infak_sedekah'] : [$filters['dana']]);
        }
        $date = 'COALESCE(ledger_journal.tanggal, ledger_journal.tgl)';
        $net = fn ($query) => (float) $query->selectRaw('COALESCE(SUM(jurnal_detail.debit - jurnal_detail.credit), 0) as balance')->value('balance');
        $opening = $net((clone $base)->whereRaw("$date < ?", [$from]));
        $period = (clone $base)->whereRaw("$date BETWEEN ? AND ?", [$from, $to]);
        $totals = (clone $period)->selectRaw('COALESCE(SUM(jurnal_detail.debit), 0) as debit, COALESCE(SUM(jurnal_detail.credit), 0) as credit')->first();
        $closing = $opening + (float) $totals->debit - (float) $totals->credit;
        $entryQuery = (clone $period)->select('jurnal_detail.*')
            ->selectRaw("$date as ledger_date")
            ->with('jurnalUmum.detail.coa')
            ->orderByRaw("$date ASC")->orderBy('ledger_journal.id')->orderBy('jurnal_detail.id');
        $entries = $isPrint ? $entryQuery->get() : $entryQuery->paginate(25)->withQueryString();
        $running = $opening;
        if (! $isPrint && ($first = $entries->first())) {
            $running += $net((clone $period)->whereRaw(
                "($date, ledger_journal.id, jurnal_detail.id) < (?, ?, ?)",
                [$first->ledger_date, $first->jurnal_id, $first->id]
            ));
        }
        foreach ($entries as $entry) {
            $running += (float) $entry->debit - (float) $entry->credit;
            $entry->running_balance = $running;
        }
        $organization = $account?->organization;
        $printedAt = now()->timezone('Asia/Jakarta');
        return view($isPrint ? 'admin.laporan.buku-besar-cetak' : 'admin.laporan.buku-besar', compact(
            'accounts', 'account', 'funds', 'filters', 'from', 'to', 'opening', 'closing', 'totals', 'entries', 'organization', 'printedAt'
        ));
    }
}
