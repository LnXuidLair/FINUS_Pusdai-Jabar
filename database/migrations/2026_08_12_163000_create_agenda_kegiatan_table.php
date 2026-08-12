<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agenda_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->enum('kategori', ['kajian', 'sosial', 'pendidikan', 'ibadah'])->default('kajian');
            $table->string('hari');
            $table->string('waktu');
            $table->string('lokasi');
            $table->text('deskripsi')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agenda_kegiatan');
    }
};
