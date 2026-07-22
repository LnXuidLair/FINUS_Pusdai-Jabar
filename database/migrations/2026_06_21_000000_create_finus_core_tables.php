<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->timestamp('password_changed_at')->nullable();
            $table->enum('role', ['admin', 'pegawai', 'jamaah'])->index();
            $table->boolean('password_changed')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        Schema::create('gaji_jabatan', function (Blueprint $table) {
            $table->id();
            $table->string('jabatan')->unique();
            $table->unsignedBigInteger('gaji_perhari')->default(0);
            $table->timestamps();
        });

        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->unique();
            $table->string('nama_pegawai');
            $table->string('jabatan')->index();
            $table->string('email')->unique();
            $table->text('alamat')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->enum('gender', ['L', 'P'])->nullable();
            $table->string('no_telp', 20)->nullable();
            $table->timestamps();
        });

        Schema::create('coa', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('header_akun');
            $table->string('kode_akun', 50)->unique();
            $table->string('nama_akun')->unique();
            $table->timestamps();
        });

        Schema::create('jurnal_umum', function (Blueprint $table) {
            $table->id();
            $table->date('tgl')->nullable();
            $table->date('tanggal')->nullable();
            $table->string('no_referensi')->nullable();
            $table->text('deskripsi')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pegawai')->nullable()->constrained('pegawai')->nullOnDelete();
            $table->date('tanggal');
            $table->enum('status', ['hadir', 'izin', 'lembur', 'sakit', 'tidak hadir'])->default('tidak hadir');
            $table->text('keterangan')->nullable();
            $table->string('bukti_kehadiran')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['id_pegawai', 'tanggal']);
        });

        Schema::create('penggajian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pegawai')->nullable()->constrained('pegawai')->nullOnDelete();
            $table->string('periode');
            $table->unsignedInteger('jumlah_hari')->default(0);
            $table->unsignedInteger('jumlah_kehadiran')->default(0);
            $table->unsignedBigInteger('gaji_perhari')->default(0);
            $table->unsignedBigInteger('total_gaji')->default(0);
            $table->string('status_penggajian')->default('belum_dibayar');
            $table->date('tanggal')->nullable();
            $table->foreignId('id_jurnal')->nullable()->constrained('jurnal_umum')->nullOnDelete();
            $table->string('bukti_pembayaran')->nullable();
            $table->timestamps();

            $table->unique(['id_pegawai', 'periode']);
        });

        Schema::create('pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_penggajian')->nullable()->constrained('penggajian')->nullOnDelete();
            $table->string('kategori')->nullable();
            $table->string('deskripsi')->nullable();
            $table->unsignedBigInteger('jumlah')->default(0);
            $table->date('tanggal');
            $table->string('bukti_pembayaran')->nullable();
            $table->enum('jenis', ['operasional', 'gaji'])->nullable();
            $table->foreignId('referensi_penggajian_id')->nullable()->constrained('penggajian')->nullOnDelete();
            $table->foreignId('coa_debit_id')->nullable()->constrained('coa')->nullOnDelete();
            $table->foreignId('coa_kredit_id')->nullable()->constrained('coa')->nullOnDelete();
            $table->unsignedBigInteger('nominal')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('status_verifikasi')->default('pending');
            $table->text('catatan_verifikasi')->nullable();
            $table->unsignedBigInteger('nominal_ocr')->nullable();
            $table->date('tanggal_ocr')->nullable();
            $table->timestamps();
        });

        Schema::create('jurnal_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurnal_id')->constrained('jurnal_umum')->cascadeOnDelete();
            $table->foreignId('coa_id')->constrained('coa')->cascadeOnDelete();
            $table->string('deskripsi')->nullable();
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('jurnal', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('jurnals');
        Schema::dropIfExists('jurnal_detail');
        Schema::dropIfExists('pengeluaran');
        Schema::dropIfExists('penggajian');
        Schema::dropIfExists('presensi');
        Schema::dropIfExists('jurnal_umum');
        Schema::dropIfExists('coa');
        Schema::dropIfExists('pegawai');
        Schema::dropIfExists('gaji_jabatan');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};