<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah dimensi PSAK 109 pada jurnal_detail
        if (Schema::hasTable('jurnal_detail')) {
            Schema::table('jurnal_detail', function (Blueprint $table) {
                if (!Schema::hasColumn('jurnal_detail', 'jenis_dana')) {
                    $table->string('jenis_dana', 30)->default('amil')->after('coa_id');
                }
                if (!Schema::hasColumn('jurnal_detail', 'restriction_type')) {
                    $table->string('restriction_type', 20)->nullable()->after('jenis_dana'); // mutlaqah, muqayyadah
                }
                if (!Schema::hasColumn('jurnal_detail', 'asnaf')) {
                    $table->string('asnaf', 50)->nullable()->after('restriction_type'); // fakir, miskin, amil, dll.
                }
                if (!Schema::hasColumn('jurnal_detail', 'kategori_bidang')) {
                    $table->string('kategori_bidang', 50)->nullable()->after('asnaf'); // idaroh, imaroh, riayah
                }
            });
        }

        // 2. Tambah kolom snapshot amil pada ziswaf_penerimaan
        if (Schema::hasTable('ziswaf_penerimaan')) {
            Schema::table('ziswaf_penerimaan', function (Blueprint $table) {
                if (!Schema::hasColumn('ziswaf_penerimaan', 'persentase_amil')) {
                    $table->decimal('persentase_amil', 5, 2)->default(0)->after('nominal');
                }
                if (!Schema::hasColumn('ziswaf_penerimaan', 'nominal_amil')) {
                    $table->unsignedBigInteger('nominal_amil')->default(0)->after('persentase_amil');
                }
                if (!Schema::hasColumn('ziswaf_penerimaan', 'kebijakan_amil_id')) {
                    $table->unsignedBigInteger('kebijakan_amil_id')->nullable()->after('nominal_amil');
                }
            });
        }

        // 3. Buat tabel kebijakan_amil untuk pengaturan dinamis
        if (!Schema::hasTable('kebijakan_amil')) {
            Schema::create('kebijakan_amil', function (Blueprint $table) {
                $table->id();
                $table->string('jenis_sumber', 30); // zakat, infaq_mutlaqah, infaq_muqayyadah
                $table->decimal('persentase_amil', 5, 2); // 12.50, 10.00, dll.
                $table->date('berlaku_mulai');
                $table->date('berlaku_sampai')->nullable();
                $table->string('dasar_aturan', 255)->nullable(); // Misal: SK DKM Pusdai / Fatwa MUI
                $table->boolean('potong_infak_terikat')->default(false);
                $table->boolean('aktif')->default(true);
                $table->timestamps();
            });

            // Masukkan data awal kebijakan amil
            DB::table('kebijakan_amil')->insert([
                [
                    'jenis_sumber' => 'zakat',
                    'persentase_amil' => 12.50,
                    'berlaku_mulai' => '2024-01-01',
                    'berlaku_sampai' => null,
                    'dasar_aturan' => 'Maksimal 1/8 (12.5%) Bagian Asnaf Amil (Ketentuan Syariah & BAZNAS)',
                    'potong_infak_terikat' => false,
                    'aktif' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'jenis_sumber' => 'infaq_mutlaqah',
                    'persentase_amil' => 10.00,
                    'berlaku_mulai' => '2024-01-01',
                    'berlaku_sampai' => null,
                    'dasar_aturan' => 'Kebijakan Pengelolaan DKM Pusdai Jabar untuk Operasional',
                    'potong_infak_terikat' => false,
                    'aktif' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'jenis_sumber' => 'infaq_muqayyadah',
                    'persentase_amil' => 0.00,
                    'berlaku_mulai' => '2024-01-01',
                    'berlaku_sampai' => null,
                    'dasar_aturan' => 'Infak terikat disalurkan 100% sesuai amanah donatur tanpa potongan',
                    'potong_infak_terikat' => false,
                    'aktif' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('jurnal_detail')) {
            Schema::table('jurnal_detail', function (Blueprint $table) {
                $columns = ['jenis_dana', 'restriction_type', 'asnaf', 'kategori_bidang'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('jurnal_detail', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('ziswaf_penerimaan')) {
            Schema::table('ziswaf_penerimaan', function (Blueprint $table) {
                $columns = ['persentase_amil', 'nominal_amil', 'kebijakan_amil_id'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('ziswaf_penerimaan', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        Schema::dropIfExists('kebijakan_amil');
    }
};
