<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        $this->moveParkingDetailsToIncome();

        foreach (['parkir_aktivitas', 'parkir_sesi'] as $table) {
            if (Schema::hasTable($table) && DB::table($table)->exists()) {
                throw new RuntimeException("Tabel {$table} masih berisi data. Kosongkan atau arsipkan sebelum migration dilanjutkan.");
            }
        }

        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('pemasukan_parkir_details');
        Schema::dropIfExists('parkir_aktivitas');
        Schema::dropIfExists('parkir_sesi');
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // Data parkir tetap berada di ziswaf_penerimaan.rincian_perhitungan.
    }

    private function moveParkingDetailsToIncome(): void
    {
        if (! Schema::hasTable('pemasukan_parkir_details')) {
            return;
        }

        DB::table('pemasukan_parkir_details')
            ->orderBy('id')
            ->each(function (object $detail): void {
                $pemasukan = DB::table('ziswaf_penerimaan')
                    ->where('id', $detail->ziswaf_penerimaan_id)
                    ->first(['rincian_perhitungan']);

                if (! $pemasukan) {
                    return;
                }

                $existing = json_decode((string) $pemasukan->rincian_perhitungan, true);
                $existing = is_array($existing) ? $existing : [];

                $parkingData = array_filter([
                    'shift' => $detail->shift,
                    'waktu_mulai' => substr((string) $detail->waktu_mulai, 0, 5),
                    'waktu_selesai' => substr((string) $detail->waktu_selesai, 0, 5),
                    'nomor_rekap' => $detail->nomor_rekap,
                    'catatan_serah_terima' => $detail->catatan_serah_terima,
                    'jumlah_motor' => (int) $detail->jumlah_motor ?: null,
                    'jumlah_mobil' => (int) $detail->jumlah_mobil ?: null,
                    'jumlah_lain' => (int) $detail->jumlah_lain ?: null,
                ], static fn ($value): bool => $value !== null && $value !== '');

                DB::table('ziswaf_penerimaan')
                    ->where('id', $detail->ziswaf_penerimaan_id)
                    ->update([
                        'rincian_perhitungan' => json_encode(
                            array_replace($existing, $parkingData),
                            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                        ),
                    ]);
            });
    }
};
