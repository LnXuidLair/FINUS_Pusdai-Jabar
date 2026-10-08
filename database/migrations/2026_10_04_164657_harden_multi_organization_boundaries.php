<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addMissingOrganizationColumns();
        $this->backfillSingleOrganizationData();
        $this->addGajiJabatanAuditColumns();

        // Identitas yang hanya perlu unik di dalam satu masjid.
        $this->replaceUnique(
            'gaji_jabatan',
            'gaji_jabatan_jabatan_unique',
            ['organization_id', 'jabatan'],
            'gaji_jabatan_org_jabatan_unique'
        );

        $this->replaceUnique(
            'pegawai',
            'pegawai_nip_unique',
            ['organization_id', 'nip'],
            'pegawai_org_nip_unique'
        );

        $this->replaceUnique(
            'coa',
            'coa_kode_akun_unique',
            ['organization_id', 'kode_akun'],
            'coa_org_kode_akun_unique'
        );

        $this->replaceUnique(
            'coa',
            'coa_nama_akun_unique',
            ['organization_id', 'nama_akun'],
            'coa_org_nama_akun_unique'
        );

        $this->replaceUnique(
            'periode_penyaluran_zakat',
            'periode_penyaluran_zakat_periode_unique',
            ['organization_id', 'periode'],
            'periode_zakat_org_periode_unique'
        );




    }

    public function down(): void
    {
        $compositeIndexes = [
            ['gaji_jabatan', 'gaji_jabatan_org_jabatan_unique'],
            ['pegawai', 'pegawai_org_nip_unique'],
            ['coa', 'coa_org_kode_akun_unique'],
            ['coa', 'coa_org_nama_akun_unique'],
            ['periode_penyaluran_zakat', 'periode_zakat_org_periode_unique'],
        ];

        foreach ($compositeIndexes as [$tableName, $indexName]) {
            if (Schema::hasTable($tableName) && Schema::hasIndex($tableName, $indexName)) {
                Schema::table($tableName, fn (Blueprint $table) => $table->dropUnique($indexName));
            }
        }

        if (Schema::hasTable('gaji_jabatan')) {
            Schema::table('gaji_jabatan', function (Blueprint $table): void {
                foreach (['updated_by', 'created_by'] as $column) {
                    if (Schema::hasColumn('gaji_jabatan', $column)) {
                        $table->dropConstrainedForeignId($column);
                    }
                }
            });
        }

    }

    private function addMissingOrganizationColumns(): void
    {
        // Standar/kebijakan zakat bersifat global FINUS (acuan BAZNAS),
        // sehingga tidak diberi organization_id.
    }

    private function backfillSingleOrganizationData(): void
    {
        // Tidak ada master kebijakan zakat global yang perlu dibackfill ke organization.
    }

    private function addGajiJabatanAuditColumns(): void
    {
        if (! Schema::hasTable('gaji_jabatan')) {
            return;
        }

        Schema::table('gaji_jabatan', function (Blueprint $table): void {
            if (! Schema::hasColumn('gaji_jabatan', 'created_by')) {
                $table->foreignId('created_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('gaji_jabatan', 'updated_by')) {
                $table->foreignId('updated_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    private function replaceUnique(
        string $tableName,
        string $oldIndex,
        array $columns,
        string $newIndex
    ): void {
        if (! Schema::hasTable($tableName)) {
            return;
        }

        foreach ($columns as $column) {
            if (! Schema::hasColumn($tableName, $column)) {
                return;
            }
        }

        if (Schema::hasIndex($tableName, $oldIndex)) {
            Schema::table($tableName, fn (Blueprint $table) => $table->dropUnique($oldIndex));
        }

        if (! Schema::hasIndex($tableName, $newIndex)) {
            Schema::table($tableName, fn (Blueprint $table) => $table->unique($columns, $newIndex));
        }
    }
};
