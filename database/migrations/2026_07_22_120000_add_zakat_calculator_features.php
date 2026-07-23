<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('zakat_settings')) {
            Schema::create('zakat_settings', function (Blueprint $table) {
                $table->id();
                $table->unsignedSmallInteger('tahun')->unique();
                $table->unsignedBigInteger('nisab_penghasilan_tahunan');
                $table->unsignedBigInteger('nisab_penghasilan_bulanan');
                $table->unsignedBigInteger('nisab_maal');
                $table->decimal('persentase_zakat', 5, 2)->default(2.50);
                $table->unsignedBigInteger('zakat_fitrah_per_jiwa');
                $table->decimal('beras_fitrah_kg', 5, 2)->default(2.50);
                $table->decimal('beras_fitrah_liter', 5, 2)->default(3.50);
                $table->text('sumber')->nullable();
                $table->date('berlaku_mulai');
                $table->date('berlaku_sampai')->nullable();
                $table->boolean('aktif')->default(false)->index();
                $table->timestamps();
            });
        }

        DB::table('zakat_settings')->updateOrInsert(
            ['tahun' => 2026],
            [
                'nisab_penghasilan_tahunan' => 91681728,
                'nisab_penghasilan_bulanan' => 7640144,
                'nisab_maal' => 91681728,
                'persentase_zakat' => 2.50,
                'zakat_fitrah_per_jiwa' => 50000,
                'beras_fitrah_kg' => 2.50,
                'beras_fitrah_liter' => 3.50,
                'sumber' => 'BAZNAS RI SK No. 15 Tahun 2026 untuk zakat penghasilan dan SK No. 14 Tahun 2026 untuk zakat fitrah. Nisab maal dapat diperbarui pengelola sesuai harga emas dan ketetapan yang digunakan Pusdai.',
                'berlaku_mulai' => '2026-01-01',
                'berlaku_sampai' => '2026-12-31',
                'aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        if (Schema::hasTable('ziswaf_penerimaan')) {
            $this->tambahkanJenisZakatPenghasilan();

            Schema::table('ziswaf_penerimaan', function (Blueprint $table) {
                if (!Schema::hasColumn('ziswaf_penerimaan', 'zakat_setting_id')) {
                    $table->foreignId('zakat_setting_id')
                        ->nullable()
                        ->after('coa_id')
                        ->constrained('zakat_settings')
                        ->nullOnDelete();
                }

                if (!Schema::hasColumn('ziswaf_penerimaan', 'rincian_perhitungan')) {
                    $table->longText('rincian_perhitungan')
                        ->nullable()
                        ->after('zakat_setting_id');
                }

                if (!Schema::hasColumn('ziswaf_penerimaan', 'nisab_digunakan')) {
                    $table->unsignedBigInteger('nisab_digunakan')
                        ->nullable()
                        ->after('rincian_perhitungan');
                }

                if (!Schema::hasColumn('ziswaf_penerimaan', 'persentase_zakat')) {
                    $table->decimal('persentase_zakat', 5, 2)
                        ->nullable()
                        ->after('nisab_digunakan');
                }
            });
        }

        if (Schema::hasTable('ziswaf_penyaluran')) {
            $this->tambahkanJenisPenyaluranPenghasilan();
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ziswaf_penerimaan')) {
            Schema::table('ziswaf_penerimaan', function (Blueprint $table) {
                if (Schema::hasColumn('ziswaf_penerimaan', 'zakat_setting_id')) {
                    $table->dropConstrainedForeignId('zakat_setting_id');
                }

                $columns = array_values(array_filter([
                    Schema::hasColumn('ziswaf_penerimaan', 'rincian_perhitungan') ? 'rincian_perhitungan' : null,
                    Schema::hasColumn('ziswaf_penerimaan', 'nisab_digunakan') ? 'nisab_digunakan' : null,
                    Schema::hasColumn('ziswaf_penerimaan', 'persentase_zakat') ? 'persentase_zakat' : null,
                ]));

                if ($columns !== []) {
                    $table->dropColumn($columns);
                }
            });
        }

        Schema::dropIfExists('zakat_settings');
    }

    private function tambahkanJenisZakatPenghasilan(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE ziswaf_penerimaan MODIFY jenis_ziswaf ENUM(
            'zakat_maal',
            'zakat_fitrah',
            'zakat_penghasilan',
            'infaq',
            'shadaqah',
            'wakaf',
            'fidyah'
        ) NOT NULL");
    }

    private function tambahkanJenisPenyaluranPenghasilan(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE ziswaf_penyaluran MODIFY jenis_ziswaf_asal ENUM(
            'zakat_maal',
            'zakat_fitrah',
            'zakat_penghasilan',
            'infaq',
            'shadaqah',
            'wakaf',
            'fidyah'
        ) NOT NULL");
    }
};
