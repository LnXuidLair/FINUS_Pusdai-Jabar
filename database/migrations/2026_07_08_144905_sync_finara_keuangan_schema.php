<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | JURNAL UMUM
        |--------------------------------------------------------------------------
        */

        if (!Schema::hasTable('jurnal_umum')) {
            Schema::create('jurnal_umum', function (Blueprint $table) {
                $table->id();
                $table->date('tgl')->nullable();
                $table->date('tanggal')->nullable();
                $table->string('no_referensi')->nullable();
                $table->string('sumber_tabel', 100)->nullable();
                $table->unsignedBigInteger('sumber_id')->nullable();
                $table->text('deskripsi')->nullable();
                $table->text('keterangan')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index('tanggal', 'idx_jurnal_umum_tanggal');
                $table->index(['sumber_tabel', 'sumber_id'], 'idx_jurnal_umum_sumber');
                $table->index('created_by', 'idx_jurnal_umum_created_by');
            });
        } else {
            Schema::table('jurnal_umum', function (Blueprint $table) {
                if (!Schema::hasColumn('jurnal_umum', 'tgl')) {
                    $table->date('tgl')->nullable()->after('id');
                }

                if (!Schema::hasColumn('jurnal_umum', 'tanggal')) {
                    $table->date('tanggal')->nullable()->after('tgl');
                }

                if (!Schema::hasColumn('jurnal_umum', 'no_referensi')) {
                    $table->string('no_referensi')->nullable()->after('tanggal');
                }

                if (!Schema::hasColumn('jurnal_umum', 'sumber_tabel')) {
                    $table->string('sumber_tabel', 100)->nullable()->after('no_referensi');
                }

                if (!Schema::hasColumn('jurnal_umum', 'sumber_id')) {
                    $table->unsignedBigInteger('sumber_id')->nullable()->after('sumber_tabel');
                }

                if (!Schema::hasColumn('jurnal_umum', 'deskripsi')) {
                    $table->text('deskripsi')->nullable()->after('sumber_id');
                }

                if (!Schema::hasColumn('jurnal_umum', 'keterangan')) {
                    $table->text('keterangan')->nullable()->after('deskripsi');
                }

                if (!Schema::hasColumn('jurnal_umum', 'created_by')) {
                    $table->foreignId('created_by')->nullable()->after('keterangan')->constrained('users')->nullOnDelete();
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | JURNAL DETAIL
        |--------------------------------------------------------------------------
        */

        if (!Schema::hasTable('jurnal_detail')) {
            Schema::create('jurnal_detail', function (Blueprint $table) {
                $table->id();
                $table->foreignId('jurnal_id')->constrained('jurnal_umum')->cascadeOnDelete();
                $table->foreignId('coa_id')->constrained('coa')->cascadeOnDelete();
                $table->string('deskripsi')->nullable();
                $table->decimal('debit', 15, 2)->default(0);
                $table->decimal('credit', 15, 2)->default(0);
                $table->timestamps();

                $table->index('jurnal_id', 'idx_jurnal_detail_jurnal_id');
                $table->index('coa_id', 'idx_jurnal_detail_coa_id');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | ZISWAF PENERIMAAN
        |--------------------------------------------------------------------------
        */

        if (!Schema::hasTable('ziswaf_penerimaan')) {
            Schema::create('ziswaf_penerimaan', function (Blueprint $table) {
                $table->id();
                $table->foreignId('muzakki_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('id_pegawai')->nullable()->constrained('pegawai')->nullOnDelete();
                $table->date('tanggal');
                $table->string('jenis_ziswaf', 50);
                $table->unsignedBigInteger('nominal')->default(0);
                $table->string('metode_pembayaran', 50)->default('manual_transfer');
                $table->string('bukti_pembayaran')->nullable();
                $table->enum('status_verifikasi', ['pending', 'diterima', 'ditolak'])->default('pending');
                $table->text('catatan_verifikasi')->nullable();
                $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('verified_at')->nullable();
                $table->text('keterangan')->nullable();
                $table->foreignId('coa_id')->nullable()->constrained('coa')->nullOnDelete();
                $table->foreignId('jurnal_id')->nullable()->constrained('jurnal_umum')->nullOnDelete();
                $table->timestamps();

                $table->index('muzakki_id', 'idx_muzakki');
                $table->index('id_pegawai', 'idx_pegawai');
                $table->index('coa_id', 'idx_coa');
                $table->index('status_verifikasi', 'idx_ziswaf_status_verifikasi');
                $table->index('tanggal', 'idx_ziswaf_tanggal');
                $table->index('jurnal_id', 'idx_ziswaf_jurnal_id');
                $table->index('verified_by', 'idx_ziswaf_verified_by');
            });
        } else {
            Schema::table('ziswaf_penerimaan', function (Blueprint $table) {
                if (!Schema::hasColumn('ziswaf_penerimaan', 'bukti_pembayaran')) {
                    $table->string('bukti_pembayaran')->nullable()->after('metode_pembayaran');
                }

                if (!Schema::hasColumn('ziswaf_penerimaan', 'status_verifikasi')) {
                    $table->enum('status_verifikasi', ['pending', 'diterima', 'ditolak'])
                        ->default('pending')
                        ->after('bukti_pembayaran');
                }

                if (!Schema::hasColumn('ziswaf_penerimaan', 'catatan_verifikasi')) {
                    $table->text('catatan_verifikasi')->nullable()->after('status_verifikasi');
                }

                if (!Schema::hasColumn('ziswaf_penerimaan', 'verified_by')) {
                    $table->foreignId('verified_by')->nullable()->after('catatan_verifikasi')->constrained('users')->nullOnDelete();
                }

                if (!Schema::hasColumn('ziswaf_penerimaan', 'verified_at')) {
                    $table->timestamp('verified_at')->nullable()->after('verified_by');
                }

                if (!Schema::hasColumn('ziswaf_penerimaan', 'jurnal_id')) {
                    $table->foreignId('jurnal_id')->nullable()->after('coa_id')->constrained('jurnal_umum')->nullOnDelete();
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | ZISWAF PENYALURAN
        |--------------------------------------------------------------------------
        */

        if (!Schema::hasTable('ziswaf_penyaluran')) {
            Schema::create('ziswaf_penyaluran', function (Blueprint $table) {
                $table->id();
                $table->date('tanggal');
                $table->string('kategori_program');
                $table->string('penerima_manfaat')->nullable();
                $table->unsignedBigInteger('nominal')->default(0);
                $table->string('jenis_ziswaf_asal', 50);
                $table->string('bukti_penyaluran')->nullable();
                $table->text('keterangan')->nullable();
                $table->foreignId('id_pengeluaran')->nullable()->constrained('pengeluaran')->nullOnDelete();
                $table->timestamps();

                $table->index('id_pengeluaran', 'idx_pengeluaran');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | PENGELUARAN
        |--------------------------------------------------------------------------
        */

        if (Schema::hasTable('pengeluaran')) {
            Schema::table('pengeluaran', function (Blueprint $table) {
                if (!Schema::hasColumn('pengeluaran', 'created_by')) {
                    $table->foreignId('created_by')->nullable()->after('tanggal')->constrained('users')->nullOnDelete();
                }

                if (!Schema::hasColumn('pengeluaran', 'bukti_pembayaran')) {
                    $table->string('bukti_pembayaran')->nullable()->after('created_by');
                }

                if (!Schema::hasColumn('pengeluaran', 'jenis')) {
                    $table->enum('jenis', ['operasional', 'gaji'])->nullable()->after('bukti_pembayaran');
                }

                if (!Schema::hasColumn('pengeluaran', 'referensi_penggajian_id')) {
                    $table->foreignId('referensi_penggajian_id')->nullable()->after('jenis')->constrained('penggajian')->nullOnDelete();
                }

                if (!Schema::hasColumn('pengeluaran', 'coa_debit_id')) {
                    $table->foreignId('coa_debit_id')->nullable()->after('referensi_penggajian_id')->constrained('coa')->nullOnDelete();
                }

                if (!Schema::hasColumn('pengeluaran', 'coa_kredit_id')) {
                    $table->foreignId('coa_kredit_id')->nullable()->after('coa_debit_id')->constrained('coa')->nullOnDelete();
                }

                if (!Schema::hasColumn('pengeluaran', 'nominal')) {
                    $table->unsignedBigInteger('nominal')->nullable()->after('coa_kredit_id');
                }

                if (!Schema::hasColumn('pengeluaran', 'keterangan')) {
                    $table->string('keterangan')->nullable()->after('nominal');
                }

                if (!Schema::hasColumn('pengeluaran', 'status_verifikasi')) {
                    $table->string('status_verifikasi')->default('pending')->after('keterangan');
                }

                if (!Schema::hasColumn('pengeluaran', 'catatan_verifikasi')) {
                    $table->text('catatan_verifikasi')->nullable()->after('status_verifikasi');
                }

                if (!Schema::hasColumn('pengeluaran', 'verified_by')) {
                    $table->foreignId('verified_by')->nullable()->after('catatan_verifikasi')->constrained('users')->nullOnDelete();
                }

                if (!Schema::hasColumn('pengeluaran', 'verified_at')) {
                    $table->timestamp('verified_at')->nullable()->after('verified_by');
                }

                if (!Schema::hasColumn('pengeluaran', 'jurnal_id')) {
                    $table->foreignId('jurnal_id')->nullable()->after('verified_at')->constrained('jurnal_umum')->nullOnDelete();
                }

                if (!Schema::hasColumn('pengeluaran', 'nominal_ocr')) {
                    $table->unsignedBigInteger('nominal_ocr')->nullable()->after('jurnal_id');
                }

                if (!Schema::hasColumn('pengeluaran', 'tanggal_ocr')) {
                    $table->date('tanggal_ocr')->nullable()->after('nominal_ocr');
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | PENGGAJIAN
        |--------------------------------------------------------------------------
        */

        if (Schema::hasTable('penggajian')) {
            Schema::table('penggajian', function (Blueprint $table) {
                if (!Schema::hasColumn('penggajian', 'tanggal')) {
                    $table->date('tanggal')->nullable()->after('status_penggajian');
                }

                if (!Schema::hasColumn('penggajian', 'id_jurnal')) {
                    $table->foreignId('id_jurnal')->nullable()->after('tanggal')->constrained('jurnal_umum')->nullOnDelete();
                }

                if (!Schema::hasColumn('penggajian', 'bukti_pembayaran')) {
                    $table->string('bukti_pembayaran')->nullable()->after('id_jurnal');
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | VIEW TRANSAKSI KEUANGAN
        |--------------------------------------------------------------------------
        */

        DB::statement("DROP VIEW IF EXISTS v_transaksi_keuangan");

        DB::statement("
            CREATE VIEW v_transaksi_keuangan AS
            SELECT
                'ziswaf_penerimaan' AS sumber_tabel,
                z.id AS sumber_id,
                z.tanggal AS tanggal,
                'pemasukan' AS tipe,
                z.jenis_ziswaf AS kategori,
                z.keterangan AS keterangan,
                z.nominal AS nominal,
                z.status_verifikasi AS status,
                z.jurnal_id AS jurnal_id,
                z.created_at AS created_at,
                z.updated_at AS updated_at
            FROM ziswaf_penerimaan z
        ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS v_transaksi_keuangan");

        Schema::dropIfExists('ziswaf_penyaluran');
        Schema::dropIfExists('ziswaf_penerimaan');
        Schema::dropIfExists('jurnal_detail');
        Schema::dropIfExists('jurnal_umum');
    }
};