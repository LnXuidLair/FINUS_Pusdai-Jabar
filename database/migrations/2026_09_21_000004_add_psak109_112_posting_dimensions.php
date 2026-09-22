<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ziswaf_penerimaan', function (Blueprint $table): void {
            if (! Schema::hasColumn('ziswaf_penerimaan', 'restriction_type')) {
                $table->string('restriction_type', 20)->nullable()->after('jenis_ziswaf');
            }
            if (! Schema::hasColumn('ziswaf_penerimaan', 'wakaf_type')) {
                $table->string('wakaf_type', 30)->nullable()->after('restriction_type');
            }
            if (! Schema::hasColumn('ziswaf_penerimaan', 'wakaf_return_date')) {
                $table->date('wakaf_return_date')->nullable()->after('wakaf_type');
            }
            if (! Schema::hasColumn('ziswaf_penerimaan', 'persentase_nazhir')) {
                $table->decimal('persentase_nazhir', 5, 2)->default(0)->after('nominal_amil');
            }
            if (! Schema::hasColumn('ziswaf_penerimaan', 'nominal_nazhir')) {
                $table->unsignedBigInteger('nominal_nazhir')->default(0)->after('persentase_nazhir');
            }
        });

        Schema::table('pengeluaran', function (Blueprint $table): void {
            if (! Schema::hasColumn('pengeluaran', 'restriction_type')) {
                $table->string('restriction_type', 20)->nullable()->after('kategori');
            }
        });

        Schema::table('jurnal_detail', function (Blueprint $table): void {
            if (! Schema::hasColumn('jurnal_detail', 'psak_reference')) {
                $table->string('psak_reference', 20)->nullable()->after('restriction_type');
            }
        });

        foreach ([
            [2, '2201', 'Liabilitas Wakaf Temporer'],
            [4, '4107', 'Penerimaan Wakaf Permanen'],
            [4, '4109', 'Hasil Pengelolaan dan Pengembangan Wakaf'],
            [4, '4310', 'Bagian Nazhir dari Hasil Pengelolaan Wakaf'],
            [5, '5411', 'Penyaluran Manfaat Wakaf'],
            [5, '5412', 'Imbalan Nazhir atas Hasil Pengelolaan Wakaf'],
        ] as [$header, $code, $name]) {
            DB::table('coa')->updateOrInsert(
                ['kode_akun' => $code],
                [
                    'header_akun' => $header,
                    'nama_akun' => $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        Schema::table('jurnal_detail', function (Blueprint $table): void {
            if (Schema::hasColumn('jurnal_detail', 'psak_reference')) {
                $table->dropColumn('psak_reference');
            }
        });

        Schema::table('pengeluaran', function (Blueprint $table): void {
            if (Schema::hasColumn('pengeluaran', 'restriction_type')) {
                $table->dropColumn('restriction_type');
            }
        });

        Schema::table('ziswaf_penerimaan', function (Blueprint $table): void {
            $columns = [
                'restriction_type',
                'wakaf_type',
                'wakaf_return_date',
                'persentase_nazhir',
                'nominal_nazhir',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('ziswaf_penerimaan', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
