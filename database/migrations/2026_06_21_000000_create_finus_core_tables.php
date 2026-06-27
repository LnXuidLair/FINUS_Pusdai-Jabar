<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
            $table->enum('role', ['admin', 'pegawai', 'jamaah'])->index();
            $table->timestamp('password_changed_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
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
            $table->string('jabatan');
            $table->string('email')->unique();
            $table->enum('gender', ['L', 'P'])->nullable();
            $table->string('no_telp')->nullable();
            $table->text('alamat')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });

        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pegawai')->constrained('pegawai')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('status');
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
            $table->foreignId('id_pegawai')->constrained('pegawai')->cascadeOnDelete();
            $table->string('periode');
            $table->unsignedInteger('jumlah_hari')->default(0);
            $table->unsignedInteger('jumlah_kehadiran')->default(0);
            $table->unsignedBigInteger('gaji_perhari')->default(0);
            $table->unsignedBigInteger('total_gaji')->default(0);
            $table->string('status_penggajian')->default('belum_dibayar');
            $table->date('tanggal')->nullable();
            $table->string('bukti_pembayaran')->nullable();
            $table->timestamps();
            $table->unique(['id_pegawai', 'periode']);
        });

        Schema::create('pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_penggajian')->nullable()->constrained('penggajian')->nullOnDelete();
            $table->string('kategori');
            $table->string('deskripsi');
            $table->unsignedBigInteger('jumlah');
            $table->date('tanggal');
            $table->string('bukti_pembayaran')->nullable();
            $table->string('status_verifikasi')->default('pending');
            $table->text('catatan_verifikasi')->nullable();
            $table->timestamps();
        });

        Schema::create('coas', function (Blueprint $table) {
            $table->id();
            $table->string('kode_akun')->unique();
            $table->string('nama_akun');
            $table->unsignedTinyInteger('header_akun');
            $table->timestamps();
        });

        Schema::create('jurnals', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->string('nama_siswa');
            $table->timestamps();
        });

        Schema::create('pembayaran_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->nullable()->constrained('siswa')->nullOnDelete();
            $table->date('tanggal_bayar');
            $table->unsignedBigInteger('jumlah');
            $table->string('status_pembayaran')->default('lunas');
            $table->string('no_pembayaran')->nullable();
            $table->timestamps();
        });

        Schema::create('tagihan_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->nullable()->constrained('siswa')->nullOnDelete();
            $table->unsignedBigInteger('jumlah')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan_siswa');
        Schema::dropIfExists('pembayaran_siswa');
        Schema::dropIfExists('siswa');
        Schema::dropIfExists('jurnals');
        Schema::dropIfExists('coas');
        Schema::dropIfExists('pengeluaran');
        Schema::dropIfExists('penggajian');
        Schema::dropIfExists('presensi');
        Schema::dropIfExists('pegawai');
        Schema::dropIfExists('gaji_jabatan');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
