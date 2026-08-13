<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agenda_kegiatan', function (Blueprint $table) {
            $table->enum('tipe_jadwal', ['rutin', 'sekali'])->default('rutin')->after('kategori');
            $table->string('hari_rutin')->nullable()->after('tipe_jadwal');
            $table->date('tanggal')->nullable()->after('hari_rutin');
            $table->string('waktu_mulai', 10)->nullable()->after('waktu');
            $table->string('waktu_selesai', 10)->nullable()->after('waktu_mulai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agenda_kegiatan', function (Blueprint $table) {
            $table->dropColumn(['tipe_jadwal', 'hari_rutin', 'tanggal', 'waktu_mulai', 'waktu_selesai']);
        });
    }
};
