<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kebijakan/master zakat adalah standar global FINUS yang mengacu BAZNAS.
        // organization_id hanya dipakai pada transaksi/operasional masjid, bukan pada standar ini.
        $this->restoreGlobalUnique('barang_zakat', 'kode', 'barang_zakat_org_kode_unique', 'barang_zakat_kode_unique');
        $this->restoreGlobalUnique('barang_zakat', 'nama', 'barang_zakat_org_nama_unique', 'barang_zakat_nama_unique');
        $this->restoreGlobalUnique('ketentuan_pokok_zakat', 'kode', 'ketentuan_zakat_org_kode_unique', 'ketentuan_pokok_zakat_kode_unique');
        $this->restoreGlobalUnique('kebijakan_zakat_versions', 'kode', 'kebijakan_zakat_org_kode_unique', 'kebijakan_zakat_versions_kode_unique');

        foreach ([
            'ketentuan_pokok_zakat',
            'kebijakan_zakat_versions',
            'kebijakan_amil',
            'kebijakan_mustahik',
            'zakat_settings',
            'barang_zakat',
            'harga_barang_zakat',
        ] as $tableName) {
            $this->dropOrganizationColumn($tableName);
        }
    }

    public function down(): void
    {
        // Rollback hanya mengembalikan kolom ownership; aplikasi tetap menganggap
        // kebijakan zakat sebagai data global kecuali kode juga di-rollback.
        foreach ([
            'ketentuan_pokok_zakat',
            'kebijakan_zakat_versions',
            'kebijakan_amil',
            'kebijakan_mustahik',
            'zakat_settings',
            'barang_zakat',
            'harga_barang_zakat',
        ] as $tableName) {
            if (! Schema::hasTable($tableName) || Schema::hasColumn($tableName, 'organization_id')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table): void {
                $table->foreignId('organization_id')
                    ->nullable()
                    ->constrained('organizations')
                    ->nullOnDelete();
            });
        }
    }

    private function restoreGlobalUnique(string $tableName, string $column, string $orgIndex, string $globalIndex): void
    {
        if (! Schema::hasTable($tableName) || ! Schema::hasColumn($tableName, $column)) {
            return;
        }

        // Jangan menghapus data diam-diam. Jika pernah terbentuk duplikat antar-organization,
        // administrator harus menentukannya secara eksplisit sebelum standar dijadikan global lagi.
        $duplicates = DB::table($tableName)
            ->select($column, DB::raw('COUNT(*) as total'))
            ->whereNotNull($column)
            ->groupBy($column)
            ->havingRaw('COUNT(*) > 1')
            ->limit(1)
            ->first();

        if ($duplicates) {
            throw new \RuntimeException(
                "Tidak dapat mengembalikan {$tableName}.{$column} menjadi global karena ada nilai duplikat: {$duplicates->{$column}}."
            );
        }

        if (Schema::hasIndex($tableName, $orgIndex)) {
            Schema::table($tableName, fn (Blueprint $table) => $table->dropUnique($orgIndex));
        }

        if (! Schema::hasIndex($tableName, $globalIndex)) {
            Schema::table($tableName, fn (Blueprint $table) => $table->unique($column, $globalIndex));
        }
    }

    private function dropOrganizationColumn(string $tableName): void
    {
        if (! Schema::hasTable($tableName) || ! Schema::hasColumn($tableName, 'organization_id')) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table): void {
            try {
                $table->dropConstrainedForeignId('organization_id');
            } catch (Throwable) {
                $table->dropColumn('organization_id');
            }
        });
    }
};
