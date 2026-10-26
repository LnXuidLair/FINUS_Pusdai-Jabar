<?php

namespace App\Services\Accounting;

use App\Models\JurnalUmum;
use App\Models\ZiswafPenerimaan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

/**
 * Memastikan pembayaran Midtrans yang sudah lunas memiliki tepat satu jurnal penerimaan.
 * Semua operasi tulis diserialisasi dengan lock pada baris transaksi penerimaan.
 */
class MidtransReceiptJournalService
{
    public function __construct(private readonly Psak109PostingService $postingService) {}

    public function confirmAndPost(int $receiptId, array $confirmation): string
    {
        return DB::transaction(function () use ($receiptId, $confirmation): string {
            $receipt = ZiswafPenerimaan::query()->lockForUpdate()->findOrFail($receiptId);
            if ($receipt->payment_gateway !== 'midtrans') {
                throw new RuntimeException('Transaksi bukan pembayaran Midtrans.');
            }
            if (! in_array($confirmation['payment_status'] ?? null, ['settlement', 'capture'], true)) {
                throw new RuntimeException('Status konfirmasi Midtrans tidak menunjukkan pembayaran berhasil.');
            }
            if (($confirmation['payment_status'] ?? null) === 'capture'
                && ! in_array($confirmation['fraud_status'] ?? null, ['accept', null, ''], true)) {
                throw new RuntimeException('Status pemeriksaan fraud capture belum diterima.');
            }

            $receipt->fill($confirmation);
            $receipt->status_verifikasi = 'diterima';
            $receipt->paid_at = $receipt->paid_at ?? now();
            $receipt->verified_at = $receipt->verified_at ?? now();
            $receipt->save();

            return $this->repairLocked($receipt);
        });
    }

    /** Memperbaiki SATU penerimaan lama saja; aman dipanggil ulang. */
    public function repair(int $receiptId): string
    {
        return DB::transaction(function () use ($receiptId): string {
            $receipt = ZiswafPenerimaan::query()->lockForUpdate()->findOrFail($receiptId);
            return $this->repairLocked($receipt);
        });
    }

    /** Inspect read-only untuk perintah --dry-run. */
    public function inspect(ZiswafPenerimaan $receipt): array
    {
        $journal = $this->findPossibleJournal($receipt);
        return [
            'id' => $receipt->id,
            'order_id' => $receipt->order_id,
            'nominal' => $receipt->nominal,
            'payment_gateway' => $receipt->payment_gateway,
            'payment_status' => $receipt->payment_status,
            'status_verifikasi' => $receipt->status_verifikasi,
            'jurnal_id' => $receipt->jurnal_id,
            'existing_journal_id' => $journal?->id,
            'journal_detail_count' => $journal?->detail()->count() ?? 0,
        ];
    }

    private function repairLocked(ZiswafPenerimaan $receipt): string
    {
        if ($receipt->payment_gateway !== 'midtrans'
            || $receipt->status_verifikasi !== 'diterima'
            || ! in_array($receipt->payment_status, ['settlement', 'capture'], true)
            || ($receipt->payment_status === 'capture'
                && ! in_array($receipt->fraud_status, ['accept', null, ''], true))) {
            throw new RuntimeException('Penerimaan belum terbukti berstatus berhasil di Midtrans; jurnal tidak dibuat.');
        }

        if ((int) $receipt->nominal <= 0) {
            throw new RuntimeException('Nominal penerimaan tidak valid.');
        }

        $journal = $this->findPossibleJournal($receipt);
        if ($journal) {
            $details = $journal->detail;
            $debit = round((float) $details->sum('debit'), 2);
            $credit = round((float) $details->sum('credit'), 2);
            if ($details->count() < 2 || $debit < (float) $receipt->nominal
                || $credit < (float) $receipt->nominal || abs($debit - $credit) > 0.01) {
                throw new RuntimeException('Jurnal terkait sudah ada tetapi belum seimbang/lengkap; periksa manual, jangan posting ulang.');
            }
            if ((int) ($receipt->jurnal_id ?? 0) !== (int) $journal->id) {
                $receipt->jurnal_id = $journal->id;
                $receipt->saveQuietly();
                return 'linked_existing';
            }
            return 'already_posted';
        }

        if ($receipt->jurnal_id) {
            throw new RuntimeException('Transaksi memiliki jurnal_id yang tidak dapat ditemukan; periksa referensi sebelum memperbaiki.');
        }

        // Kode posting terbaru membutuhkan dua dimensi jurnal dari migration Oktober.
        // Gagal dengan pesan jelas tanpa menulis data jika skema belum kompatibel.
        foreach (['zakat_source', 'wakaf_purpose'] as $column) {
            if (! Schema::hasColumn('jurnal_detail', $column)) {
                throw new RuntimeException('Kolom jurnal_detail.'.$column.' belum tersedia. Periksa status migration dan backup sebelum memposting.');
            }
        }

        $result = $this->postingService->postPenerimaan($receipt);
        if (! $result) {
            throw new RuntimeException('Posting jurnal tidak menghasilkan jurnal.');
        }
        return 'created';
    }

    private function findPossibleJournal(ZiswafPenerimaan $receipt): ?JurnalUmum
    {
        $reference = 'JMASUK-'.str_pad((string) $receipt->id, 6, '0', STR_PAD_LEFT);
        // Deteksi jurnal yang dibuat sebelumnya meskipun jurnal_id transaksi masih NULL.
        $matches = JurnalUmum::withoutGlobalScopes()
            ->where(function ($query) use ($receipt, $reference) {
                $query->where(function ($source) use ($receipt) {
                    $source->where('sumber_tabel', 'ziswaf_penerimaan')
                        ->where('sumber_id', $receipt->id);
                })->orWhere('no_referensi', $reference);
                if ($receipt->jurnal_id) {
                    $query->orWhere('id', $receipt->jurnal_id);
                }
            })->get();

        if ($matches->count() > 1) {
            throw new RuntimeException('Ditemukan lebih dari satu kandidat jurnal untuk transaksi ini. Periksa secara manual.');
        }
        $journal = $matches->first();
        if ($journal) {
            if ($journal->sumber_tabel !== 'ziswaf_penerimaan'
                || (int) $journal->sumber_id !== (int) $receipt->id
                || ($journal->organization_id !== null && (int) $journal->organization_id !== (int) $receipt->organization_id)) {
                throw new RuntimeException('Kandidat jurnal terkait transaksi/organisasi lain. Posting otomatis dibatalkan.');
            }
        }
        return $journal;
    }
}
