<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Menghapus Zakat Perdagangan dan Zakat Rikaz dari ketentuan pokok zakat.
     *
     * Kedua jenis zakat ini tidak relevan untuk konteks jamaah masjid Pusdai
     * sehingga dihapus agar form zakat jamaah lebih sederhana dan fokus pada
     * golongan zakat yang umum dibayarkan.
     */
    public function up(): void
    {
        DB::table('ketentuan_pokok_zakat')
            ->whereIn('kode', ['ZAKAT-PERDAGANGAN', 'ZAKAT-RIKAZ'])
            ->delete();
    }

    public function down(): void
    {
        $now = now();

        DB::table('ketentuan_pokok_zakat')->insert([
            [
                'kode' => 'ZAKAT-PERDAGANGAN',
                'nama' => 'Zakat Perdagangan',
                'jenis' => 'perdagangan',
                'deskripsi' => 'Zakat atas barang dagangan yang telah mencapai nisab setara 85 gram emas dan melewati haul.',
                'kadar_persentase' => 2.50,
                'satuan_kadar' => 'persen',
                'nisab_pokok' => '85 gram emas',
                'haul' => '1 tahun',
                'berat_fitrah_kg' => null,
                'berat_fitrah_liter' => null,
                'dasar_hukum' => 'QS Al-Baqarah:267',
                'dasar_regulasi' => 'PMA No. 52 Tahun 2014 Pasal 17',
                'terkunci' => true,
                'aktif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'ZAKAT-RIKAZ',
                'nama' => 'Zakat Rikaz (Temuan)',
                'jenis' => 'rikaz',
                'deskripsi' => 'Zakat atas harta temuan (rikaz) yang dikeluarkan saat ditemukan tanpa haul.',
                'kadar_persentase' => 20.00,
                'satuan_kadar' => 'persen',
                'nisab_pokok' => 'Tidak ada nisab minimum',
                'haul' => 'saat ditemukan',
                'berat_fitrah_kg' => null,
                'berat_fitrah_liter' => null,
                'dasar_hukum' => 'HR Bukhari No. 1499, HR Muslim',
                'dasar_regulasi' => 'PMA No. 52 Tahun 2014 Pasal 24',
                'terkunci' => true,
                'aktif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
};
