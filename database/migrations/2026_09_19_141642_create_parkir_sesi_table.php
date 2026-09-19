<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parkir_sesi', function (Blueprint $table) {
            $table->id();

            // Info kendaraan
            $table->string('plat_nomor', 20)->nullable();
            $table->enum('jenis_kendaraan', ['motor', 'mobil', 'lain'])->default('motor');

            // Tarif & pembayaran
            $table->unsignedBigInteger('nominal')->default(0);
            $table->enum('metode_pembayaran', ['qris', 'tunai'])->default('qris');

            // Status sesi
            $table->enum('status', ['masuk', 'selesai', 'dibatalkan'])->default('masuk');
            $table->timestamp('waktu_masuk')->nullable();
            $table->timestamp('waktu_keluar')->nullable();

            // Keterangan & input petugas
            $table->string('keterangan', 500)->nullable();
            $table->unsignedBigInteger('input_by')->nullable();

            // Link ke pemasukan (dibuat saat selesai / lunas)
            $table->foreignId('ziswaf_penerimaan_id')
                ->nullable()
                ->constrained('ziswaf_penerimaan')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('jenis_kendaraan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parkir_sesi');
    }
};

