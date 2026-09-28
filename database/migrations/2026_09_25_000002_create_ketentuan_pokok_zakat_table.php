<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ketentuan Pokok Zakat — aturan syariat yang bersifat tetap.
     *
     * Kadar persentase zakat, jumlah asnaf, dan berat fitrah tidak berubah
     * karena bersumber dari Al-Qur'an dan Sunnah. Hanya boleh diubah oleh
     * pengguna dengan akses khusus, dan setiap perubahan tercatat di audit.
     *
     * Terpisah dari nilai acuan berkala (nisab rupiah, harga beras, dsb.)
     * yang berubah mengikuti kondisi ekonomi setiap tahun.
     */
    public function up(): void
    {
        Schema::create('ketentuan_pokok_zakat', function (Blueprint $table) {
            $table->id();

            // --- Identitas jenis zakat ---
            $table->string('kode', 30)->unique();                // e.g. ZAKAT-PENGHASILAN
            $table->string('nama', 150);                         // e.g. Zakat Penghasilan / Profesi
            $table->string('jenis', 30)->index();                // penghasilan, maal, fitrah, pertanian_berbiaya, pertanian_alami, peternakan
            $table->text('deskripsi')->nullable();

            // --- Kadar / persentase zakat ---
            $table->decimal('kadar_persentase', 6, 2);           // 2.50, 5.00, 10.00, 20.00
            $table->string('satuan_kadar', 20)->default('persen'); // persen, tabel (untuk peternakan)

            // --- Nisab pokok (non-rupiah) ---
            $table->string('nisab_pokok', 100)->nullable();      // e.g. "85 gram emas", "653 kg gabah"
            $table->string('haul', 30)->default('1 tahun');      // 1 tahun, saat panen, saat ditemukan

            // --- Khusus fitrah ---
            $table->decimal('berat_fitrah_kg', 5, 2)->nullable();    // 2.50
            $table->decimal('berat_fitrah_liter', 5, 2)->nullable(); // 3.50

            // --- Dasar hukum ---
            $table->text('dasar_hukum')->nullable();             // QS At-Taubah:60, dsb.
            $table->text('dasar_regulasi')->nullable();          // UU 23/2011, PMA 52/2014

            // --- Status dan audit ---
            $table->boolean('terkunci')->default(true);          // hanya super admin yang boleh ubah
            $table->boolean('aktif')->default(true)->index();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Delapan asnaf sebagai data referensi
        Schema::create('master_asnaf', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique();
            $table->string('nama', 80);
            $table->unsignedTinyInteger('urutan');                // 1-8 sesuai QS At-Taubah:60
            $table->text('definisi')->nullable();
            $table->string('dasar_hukum', 255)->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // Snapshot kolom pada transaksi penerimaan agar perubahan kebijakan
        // tidak memengaruhi riwayat transaksi yang sudah terjadi
        if (Schema::hasTable('ziswaf_penerimaan')) {
            Schema::table('ziswaf_penerimaan', function (Blueprint $table) {
                if (! Schema::hasColumn('ziswaf_penerimaan', 'kebijakan_version_id')) {
                    $table->foreignId('kebijakan_version_id')->nullable()
                        ->after('kebijakan_amil_id')
                        ->constrained('kebijakan_zakat_versions')
                        ->nullOnDelete();
                }
                if (! Schema::hasColumn('ziswaf_penerimaan', 'snapshot_kebijakan')) {
                    $table->json('snapshot_kebijakan')->nullable()
                        ->after('kebijakan_version_id')
                        ->comment('Snapshot lengkap: kadar, nisab, harga konversi, hak amil, versi yang digunakan');
                }
            });
        }

        // --- Seed ketentuan pokok ---
        $this->seedKetentuanPokok();
        $this->seedMasterAsnaf();
    }

    public function down(): void
    {
        if (Schema::hasTable('ziswaf_penerimaan')) {
            Schema::table('ziswaf_penerimaan', function (Blueprint $table) {
                if (Schema::hasColumn('ziswaf_penerimaan', 'snapshot_kebijakan')) {
                    $table->dropColumn('snapshot_kebijakan');
                }
                if (Schema::hasColumn('ziswaf_penerimaan', 'kebijakan_version_id')) {
                    $table->dropConstrainedForeignId('kebijakan_version_id');
                }
            });
        }

        Schema::dropIfExists('master_asnaf');
        Schema::dropIfExists('ketentuan_pokok_zakat');
    }

    private function seedKetentuanPokok(): void
    {
        $now = now();

        DB::table('ketentuan_pokok_zakat')->insert([
            [
                'kode' => 'ZAKAT-PENGHASILAN',
                'nama' => 'Zakat Penghasilan / Profesi',
                'jenis' => 'penghasilan',
                'deskripsi' => 'Zakat atas penghasilan (gaji, honorarium, pendapatan profesi) yang telah mencapai nisab setara 85 gram emas per tahun.',
                'kadar_persentase' => 2.50,
                'satuan_kadar' => 'persen',
                'nisab_pokok' => '85 gram emas',
                'haul' => '1 tahun',
                'berat_fitrah_kg' => null,
                'berat_fitrah_liter' => null,
                'dasar_hukum' => 'QS Al-Baqarah:267, QS At-Taubah:103',
                'dasar_regulasi' => 'PMA No. 52 Tahun 2014 Pasal 26; Fatwa MUI No. 3 Tahun 2003',
                'terkunci' => true,
                'aktif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'ZAKAT-MAAL',
                'nama' => 'Zakat Maal (Harta)',
                'jenis' => 'maal',
                'deskripsi' => 'Zakat atas harta yang dimiliki (tabungan, investasi, emas, perak) setelah mencapai nisab dan melewati haul.',
                'kadar_persentase' => 2.50,
                'satuan_kadar' => 'persen',
                'nisab_pokok' => '85 gram emas',
                'haul' => '1 tahun',
                'berat_fitrah_kg' => null,
                'berat_fitrah_liter' => null,
                'dasar_hukum' => 'QS At-Taubah:34-35',
                'dasar_regulasi' => 'PMA No. 52 Tahun 2014 Pasal 4',
                'terkunci' => true,
                'aktif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'ZAKAT-FITRAH',
                'nama' => 'Zakat Fitrah',
                'jenis' => 'fitrah',
                'deskripsi' => 'Zakat wajib yang dikeluarkan setiap jiwa menjelang Idul Fitri berupa makanan pokok seberat 2,5 kg atau 3,5 liter.',
                'kadar_persentase' => 0,
                'satuan_kadar' => 'berat',
                'nisab_pokok' => 'Tidak ada nisab — wajib setiap jiwa',
                'haul' => 'sebelum shalat Idul Fitri',
                'berat_fitrah_kg' => 2.50,
                'berat_fitrah_liter' => 3.50,
                'dasar_hukum' => 'HR Bukhari No. 1503, HR Muslim',
                'dasar_regulasi' => 'PMA No. 52 Tahun 2014 Pasal 30',
                'terkunci' => true,
                'aktif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'ZAKAT-PERTANIAN-BERBIAYA',
                'nama' => 'Zakat Pertanian (Irigasi / Berbiaya)',
                'jenis' => 'pertanian_berbiaya',
                'deskripsi' => 'Zakat hasil pertanian yang diairi menggunakan irigasi atau alat yang memerlukan biaya.',
                'kadar_persentase' => 5.00,
                'satuan_kadar' => 'persen',
                'nisab_pokok' => '653 kg gabah / 520 kg beras',
                'haul' => 'saat panen',
                'berat_fitrah_kg' => null,
                'berat_fitrah_liter' => null,
                'dasar_hukum' => 'QS Al-An\'am:141; HR Bukhari',
                'dasar_regulasi' => 'PMA No. 52 Tahun 2014 Pasal 14',
                'terkunci' => true,
                'aktif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'ZAKAT-PERTANIAN-ALAMI',
                'nama' => 'Zakat Pertanian (Tadah Hujan / Alami)',
                'jenis' => 'pertanian_alami',
                'deskripsi' => 'Zakat hasil pertanian yang diairi secara alami tanpa biaya, seperti tadah hujan atau mata air.',
                'kadar_persentase' => 10.00,
                'satuan_kadar' => 'persen',
                'nisab_pokok' => '653 kg gabah / 520 kg beras',
                'haul' => 'saat panen',
                'berat_fitrah_kg' => null,
                'berat_fitrah_liter' => null,
                'dasar_hukum' => 'QS Al-An\'am:141; HR Bukhari',
                'dasar_regulasi' => 'PMA No. 52 Tahun 2014 Pasal 14',
                'terkunci' => true,
                'aktif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    private function seedMasterAsnaf(): void
    {
        $now = now();

        DB::table('master_asnaf')->insert([
            [
                'kode' => 'fakir',
                'nama' => 'Fakir',
                'urutan' => 1,
                'definisi' => 'Orang yang tidak mempunyai harta dan usaha, atau mempunyai harta/usaha tetapi kurang dari separuh kebutuhan hidupnya.',
                'dasar_hukum' => 'QS At-Taubah:60',
                'aktif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'miskin',
                'nama' => 'Miskin',
                'urutan' => 2,
                'definisi' => 'Orang yang mempunyai harta/usaha yang hanya memenuhi sebagian kebutuhan hidupnya, tetapi masih tidak mencukupi.',
                'dasar_hukum' => 'QS At-Taubah:60',
                'aktif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'amil',
                'nama' => 'Amil',
                'urutan' => 3,
                'definisi' => 'Orang yang ditunjuk untuk mengumpulkan dan membagikan zakat.',
                'dasar_hukum' => 'QS At-Taubah:60',
                'aktif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'muallaf',
                'nama' => 'Muallaf',
                'urutan' => 4,
                'definisi' => 'Orang yang baru masuk Islam dan memerlukan bantuan untuk memperkuat iman.',
                'dasar_hukum' => 'QS At-Taubah:60',
                'aktif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'riqab',
                'nama' => 'Riqab',
                'urutan' => 5,
                'definisi' => 'Hamba sahaya atau budak yang ingin memerdekakan diri, pada konteks modern termasuk korban perdagangan manusia.',
                'dasar_hukum' => 'QS At-Taubah:60',
                'aktif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'gharim',
                'nama' => 'Gharim',
                'urutan' => 6,
                'definisi' => 'Orang yang berhutang untuk kebutuhan hidup dalam rangka mempertahankan jiwa dan izzahnya.',
                'dasar_hukum' => 'QS At-Taubah:60',
                'aktif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'fisabilillah',
                'nama' => 'Fisabilillah',
                'urutan' => 7,
                'definisi' => 'Orang yang berjuang di jalan Allah, termasuk dakwah, pendidikan, dan kegiatan sosial keagamaan.',
                'dasar_hukum' => 'QS At-Taubah:60',
                'aktif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'ibnu_sabil',
                'nama' => 'Ibnu Sabil',
                'urutan' => 8,
                'definisi' => 'Orang yang dalam perjalanan jauh dan kehabisan bekal dalam perjalanan yang tidak untuk kemaksiatan.',
                'dasar_hukum' => 'QS At-Taubah:60',
                'aktif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
};
