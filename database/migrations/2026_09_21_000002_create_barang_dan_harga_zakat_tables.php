<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_zakat', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique();
            $table->string('nama')->unique();
            $table->string('kategori', 50);
            $table->string('satuan_dasar', 30);
            $table->string('metode_penilaian', 30)->default('harga_pasar');
            $table->foreignId('coa_persediaan_id')->nullable()->constrained('coa')->nullOnDelete();
            $table->boolean('aktif')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['kategori', 'aktif']);
        });

        Schema::create('harga_barang_zakat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_zakat_id')->constrained('barang_zakat')->cascadeOnDelete();
            $table->string('wilayah', 100);
            $table->unsignedBigInteger('harga_per_satuan');
            $table->date('berlaku_mulai');
            $table->date('berlaku_sampai')->nullable();
            $table->string('sumber_harga', 255);
            $table->string('status', 20)->default('draft');
            $table->text('catatan')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['barang_zakat_id', 'wilayah', 'status'], 'harga_barang_wilayah_status_index');
            $table->index(['berlaku_mulai', 'berlaku_sampai']);
            $table->unique(
                ['barang_zakat_id', 'wilayah', 'berlaku_mulai'],
                'harga_barang_wilayah_mulai_unique'
            );
        });

        DB::table('barang_zakat')->insert([
            'kode' => 'BRAS',
            'nama' => 'Beras',
            'kategori' => 'makanan_pokok',
            'satuan_dasar' => 'kg',
            'metode_penilaian' => 'harga_pasar',
            'aktif' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('harga_barang_zakat');
        Schema::dropIfExists('barang_zakat');
    }
};
