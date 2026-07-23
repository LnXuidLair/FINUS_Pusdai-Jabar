<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ziswaf_penerimaan')) {
            DB::statement("
                ALTER TABLE ziswaf_penerimaan
                MODIFY jenis_ziswaf VARCHAR(50) NOT NULL
            ");
        }

        if (Schema::hasTable('ziswaf_penyaluran')) {
            DB::statement("
                ALTER TABLE ziswaf_penyaluran
                MODIFY jenis_ziswaf_asal VARCHAR(50) NOT NULL
            ");

            Schema::table('ziswaf_penyaluran', function (Blueprint $table) {
                if (!Schema::hasColumn('ziswaf_penyaluran', 'asnaf')) {
                    $table->string('asnaf', 50)->nullable()->after('jenis_ziswaf_asal');
                }

                if (!Schema::hasColumn('ziswaf_penyaluran', 'nama_penerima')) {
                    $table->string('nama_penerima')->nullable()->after('asnaf');
                }

                if (!Schema::hasColumn('ziswaf_penyaluran', 'alamat_penerima')) {
                    $table->text('alamat_penerima')->nullable()->after('nama_penerima');
                }

                if (!Schema::hasColumn('ziswaf_penyaluran', 'nik_penerima')) {
                    $table->string('nik_penerima', 30)->nullable()->after('alamat_penerima');
                }

                if (!Schema::hasColumn('ziswaf_penyaluran', 'no_hp_penerima')) {
                    $table->string('no_hp_penerima', 30)->nullable()->after('nik_penerima');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ziswaf_penyaluran')) {
            Schema::table('ziswaf_penyaluran', function (Blueprint $table) {
                $columns = [];

                foreach ([
                    'asnaf',
                    'nama_penerima',
                    'alamat_penerima',
                    'nik_penerima',
                    'no_hp_penerima',
                ] as $column) {
                    if (Schema::hasColumn('ziswaf_penyaluran', $column)) {
                        $columns[] = $column;
                    }
                }

                if ($columns !== []) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};