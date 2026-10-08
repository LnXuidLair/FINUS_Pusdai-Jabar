<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('presensi')) {
            return;
        }

        Schema::table('presensi', function (Blueprint $table): void {
            if (! Schema::hasColumn('presensi', 'kondisi')) {
                $table->enum('kondisi', ['normal', 'pulang_awal', 'tugas_dinas', 'lembur'])
                    ->nullable()
                    ->after('status');
            }

            if (! Schema::hasColumn('presensi', 'jam_datang')) {
                $table->time('jam_datang')->nullable()->after('kondisi');
            }

            if (! Schema::hasColumn('presensi', 'jam_pulang')) {
                $table->time('jam_pulang')->nullable()->after('jam_datang');
            }

            if (! Schema::hasColumn('presensi', 'bukti_datang')) {
                $table->string('bukti_datang')->nullable()->after('bukti_kehadiran');
            }

            if (! Schema::hasColumn('presensi', 'bukti_pulang')) {
                $table->string('bukti_pulang')->nullable()->after('bukti_datang');
            }

            if (! Schema::hasColumn('presensi', 'bukti_status')) {
                $table->string('bukti_status')->nullable()->after('bukti_pulang');
            }
        });

        Schema::table('presensi', function (Blueprint $table): void {
            if (! Schema::hasColumn('presensi', 'input_datang_by')) {
                $table->foreignId('input_datang_by')
                    ->nullable()
                    ->after('bukti_status')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('presensi', 'input_datang_role')) {
                $table->string('input_datang_role', 20)->nullable()->after('input_datang_by');
            }

            if (! Schema::hasColumn('presensi', 'input_pulang_by')) {
                $table->foreignId('input_pulang_by')
                    ->nullable()
                    ->after('input_datang_role')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('presensi', 'input_pulang_role')) {
                $table->string('input_pulang_role', 20)->nullable()->after('input_pulang_by');
            }

            if (! Schema::hasColumn('presensi', 'input_status_by')) {
                $table->foreignId('input_status_by')
                    ->nullable()
                    ->after('input_pulang_role')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('presensi', 'input_status_role')) {
                $table->string('input_status_role', 20)->nullable()->after('input_status_by');
            }
        });

        // Kolom lama `bukti_kehadiran` sengaja TIDAK dihapus.
        // Data presensi yang sudah ada tetap dapat dibuka dan tetap kompatibel
        // dengan penggajian lama, sementara data baru memakai bukti Datang/Pulang.
    }

    public function down(): void
    {
        if (! Schema::hasTable('presensi')) {
            return;
        }

        Schema::table('presensi', function (Blueprint $table): void {
            foreach (['input_datang_by', 'input_pulang_by', 'input_status_by'] as $column) {
                if (Schema::hasColumn('presensi', $column)) {
                    try {
                        $table->dropForeign(['' . $column]);
                    } catch (\Throwable) {
                        // Aman jika constraint sudah tidak ada.
                    }
                }
            }
        });

        Schema::table('presensi', function (Blueprint $table): void {
            $columns = [
                'input_status_role',
                'input_status_by',
                'input_pulang_role',
                'input_pulang_by',
                'input_datang_role',
                'input_datang_by',
                'bukti_status',
                'bukti_pulang',
                'bukti_datang',
                'jam_pulang',
                'jam_datang',
                'kondisi',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('presensi', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};