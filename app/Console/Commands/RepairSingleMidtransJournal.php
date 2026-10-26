<?php

namespace App\Console\Commands;

use App\Models\ZiswafPenerimaan;
use App\Services\Accounting\MidtransReceiptJournalService;
use Illuminate\Console\Command;
use Throwable;

class RepairSingleMidtransJournal extends Command
{
    protected $signature = 'finus:repair-midtrans-journal {--order-id= : Order ID satu transaksi Midtrans} {--apply : Izinkan pembaruan SATU transaksi (tanpa opsi ini hanya periksa)}';

    protected $description = 'Periksa/perbaiki jurnal satu penerimaan Midtrans historis tanpa mengubah transaksi lain';

    public function handle(MidtransReceiptJournalService $service): int
    {
        $orderId = trim((string) $this->option('order-id'));
        if ($orderId === '') {
            $this->error('Isi --order-id sesuai order_id yang tertera di database.');
            return self::FAILURE;
        }
        $matches = ZiswafPenerimaan::withoutGlobalScopes()->where('order_id', $orderId)->limit(2)->get();
        if ($matches->count() > 1) {
            $this->error('Terdapat beberapa transaksi dengan order_id yang sama; periksa manual.');
            return self::FAILURE;
        }
        $receipt = $matches->first();
        if (! $receipt) {
            $this->error('Transaksi dengan order_id tersebut tidak ditemukan.');
            return self::FAILURE;
        }

        try {
            $inspection = $service->inspect($receipt);
            $this->table(['Pemeriksaan', 'Nilai'], collect($inspection)
                ->map(fn ($value, $key) => [$key, (string) ($value ?? 'NULL')])->values()->all());
            if (! $this->option('apply')) {
                $this->warn('MODE PERIKSA SAJA: tidak ada data yang ditulis. Gunakan --apply hanya setelah bukti pembayaran cocok dan backup tersedia.');
                return self::SUCCESS;
            }
            if (! $this->confirm('Posting/tautkan jurnal untuk SATU order_id di atas?', false)) {
                $this->warn('Dibatalkan, tidak ada perubahan.');
                return self::SUCCESS;
            }
            $result = $service->repair($receipt->id);
            $this->info('Hasil: '.$result.' — jurnal dibuat/ditautkan hanya bila diperlukan.');
            return self::SUCCESS;
        } catch (Throwable $e) {
            report($e);
            $this->error('Perbaikan dibatalkan: '.$e->getMessage());
            return self::FAILURE;
        }
    }
}
