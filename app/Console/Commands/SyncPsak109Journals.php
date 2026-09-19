<?php

namespace App\Console\Commands;

use App\Models\Pengeluaran;
use App\Models\Penggajian;
use App\Models\ZiswafPenerimaan;
use App\Services\Accounting\Psak109PostingService;
use Illuminate\Console\Command;

class SyncPsak109Journals extends Command
{
    protected $signature = 'finus:sync-psak109-journals {--force : Jalankan ulang sinkronisasi untuk seluruh data}';
    protected $description = 'Sinkronisasi transaksi penerimaan, pengeluaran, dan penggajian ke jurnal permanen PSAK 109';

    public function handle(Psak109PostingService $postingService): int
    {
        $force = $this->option('force');
        $this->info('Memulai sinkronisasi jurnal PSAK 109...');

        // 1. Sinkronisasi Penerimaan ZISWAF
        $penerimaanQuery = ZiswafPenerimaan::where('status_verifikasi', 'diterima')
            ->where('nominal', '>', 0);

        if (!$force) {
            $penerimaanQuery->whereNull('jurnal_id');
        }

        $penerimaanList = $penerimaanQuery->get();
        $this->info("Menemukan {$penerimaanList->count()} transaksi penerimaan untuk diposting...");

        $barPenerimaan = $this->output->createProgressBar($penerimaanList->count());
        $penerimaanCount = 0;
        foreach ($penerimaanList as $p) {
            try {
                $postingService->postPenerimaan($p);
                $penerimaanCount++;
            } catch (\Throwable $e) {
                $this->error(" Gagal posting penerimaan ID {$p->id}: " . $e->getMessage());
            }
            $barPenerimaan->advance();
        }
        $barPenerimaan->finish();
        $this->newLine();

        // 2. Sinkronisasi Pengeluaran Operasional
        $pengeluaranQuery = Pengeluaran::where('status_verifikasi', 'diterima')
            ->where(function ($q) {
                $q->where('nominal', '>', 0)
                  ->orWhere('jumlah', '>', 0);
            });

        if (!$force) {
            $pengeluaranQuery->whereNull('jurnal_id');
        }

        $pengeluaranList = $pengeluaranQuery->get();
        $this->info("Menemukan {$pengeluaranList->count()} data pengeluaran untuk diposting...");

        $barPengeluaran = $this->output->createProgressBar($pengeluaranList->count());
        $pengeluaranCount = 0;
        foreach ($pengeluaranList as $p) {
            try {
                $postingService->postPengeluaran($p);
                $pengeluaranCount++;
            } catch (\Throwable $e) {
                $this->error(" Gagal posting pengeluaran ID {$p->id}: " . $e->getMessage());
            }
            $barPengeluaran->advance();
        }
        $barPengeluaran->finish();
        $this->newLine();

        // 3. Sinkronisasi Penggajian
        $penggajianQuery = Penggajian::where('status_penggajian', 'sudah_dibayar')
            ->whereNotNull('tanggal')
            ->where('total_gaji', '>', 0);

        if (!$force) {
            $penggajianQuery->whereNull('id_jurnal');
        }

        $penggajianList = $penggajianQuery->get();
        $this->info("Menemukan {$penggajianList->count()} data penggajian untuk diposting...");

        $barPenggajian = $this->output->createProgressBar($penggajianList->count());
        $penggajianCount = 0;
        foreach ($penggajianList as $p) {
            try {
                $postingService->postPenggajian($p);
                $penggajianCount++;
            } catch (\Throwable $e) {
                $this->error(" Gagal posting penggajian ID {$p->id}: " . $e->getMessage());
            }
            $barPenggajian->advance();
        }
        $barPenggajian->finish();
        $this->newLine();

        $this->info("Sinkronisasi selesai! Berhasil posting: {$penerimaanCount} penerimaan, {$pengeluaranCount} pengeluaran, {$penggajianCount} penggajian.");

        $saldo = $postingService->getSaldoDana();
        $this->table(
            ['Golongan Dana', 'Saldo'],
            [
                ['Dana Zakat', 'Rp ' . number_format($saldo['zakat'], 0, ',', '.')],
                ['Dana Infak / Sedekah', 'Rp ' . number_format($saldo['infak_sedekah'], 0, ',', '.')],
                ['Dana Amil', 'Rp ' . number_format($saldo['amil'], 0, ',', '.')],
                ['Dana Wakaf', 'Rp ' . number_format($saldo['wakaf'], 0, ',', '.')],
                ['Total Kas & Bank (1 Rekening)', 'Rp ' . number_format($saldo['total_bank'], 0, ',', '.')],
            ]
        );

        return Command::SUCCESS;
    }
}
