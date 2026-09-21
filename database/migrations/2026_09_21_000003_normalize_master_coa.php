<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('coa')) {
            return;
        }

        DB::transaction(function (): void {
            $this->moveLegacyAccount(
                '5108',
                ['Penyaluran Zakat - Bagian Amil'],
                '5213',
                'Penyaluran Zakat - Amil'
            );
            $this->moveLegacyAccount(
                '5208',
                ['Penyaluran Infak - Bagian Amil'],
                '5312',
                'Alokasi Infak dan Sedekah - Bagian Amil'
            );

            foreach (config('coa.accounts', []) as [$header, $code, $name]) {
                DB::table('coa')->updateOrInsert(
                    ['kode_akun' => $code],
                    [
                        'header_akun' => $header,
                        'nama_akun' => $name,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }

            $this->repairLegacyAllocationDetails();
            $this->connectLegacyExpensesToCoa();
        });
    }

    public function down(): void
    {
        // Data COA dan relasi jurnal tidak dikembalikan ke kode yang bertabrakan.
    }

    private function moveLegacyAccount(
        string $oldCode,
        array $oldNames,
        string $newCode,
        string $newName
    ): void {
        $source = DB::table('coa')
            ->where('kode_akun', $oldCode)
            ->whereIn('nama_akun', $oldNames)
            ->first();

        if (! $source) {
            return;
        }

        $target = DB::table('coa')->where('kode_akun', $newCode)->first();

        if (! $target) {
            DB::table('coa')->where('id', $source->id)->update([
                'kode_akun' => $newCode,
                'nama_akun' => $newName,
                'header_akun' => 5,
                'updated_at' => now(),
            ]);

            return;
        }

        $this->replaceCoaReferences((int) $source->id, (int) $target->id);
        DB::table('coa')->where('id', $source->id)->delete();
    }

    private function replaceCoaReferences(int $sourceId, int $targetId): void
    {
        if (Schema::hasTable('jurnal_detail')) {
            DB::table('jurnal_detail')->where('coa_id', $sourceId)->update(['coa_id' => $targetId]);
        }

        if (Schema::hasTable('pengeluaran')) {
            foreach (['coa_debit_id', 'coa_kredit_id'] as $column) {
                if (Schema::hasColumn('pengeluaran', $column)) {
                    DB::table('pengeluaran')->where($column, $sourceId)->update([$column => $targetId]);
                }
            }
        }

        if (Schema::hasTable('ziswaf_penerimaan') && Schema::hasColumn('ziswaf_penerimaan', 'coa_id')) {
            DB::table('ziswaf_penerimaan')->where('coa_id', $sourceId)->update(['coa_id' => $targetId]);
        }

        if (Schema::hasTable('barang_zakat') && Schema::hasColumn('barang_zakat', 'coa_persediaan_id')) {
            DB::table('barang_zakat')->where('coa_persediaan_id', $sourceId)->update(['coa_persediaan_id' => $targetId]);
        }
    }

    private function repairLegacyAllocationDetails(): void
    {
        if (! Schema::hasTable('jurnal_detail')) {
            return;
        }

        $zakatTarget = DB::table('coa')->where('kode_akun', '5213')->value('id');
        $legacyCleaning = DB::table('coa')->where('kode_akun', '5108')->value('id');

        if ($zakatTarget && $legacyCleaning) {
            $query = DB::table('jurnal_detail')
                ->where('coa_id', $legacyCleaning)
                ->where('deskripsi', 'like', 'Hak amil%');

            if (Schema::hasColumn('jurnal_detail', 'jenis_dana')) {
                $query->where('jenis_dana', 'zakat');
            }

            $query->update(['coa_id' => $zakatTarget]);
        }
    }

    private function connectLegacyExpensesToCoa(): void
    {
        if (! Schema::hasTable('pengeluaran') || ! Schema::hasColumn('pengeluaran', 'coa_debit_id')) {
            return;
        }

        $aliases = [
            'Biaya Bidang Idaroh' => '5101',
            'Biaya Bidang Imaroh' => '5102',
            'Biaya Bidang Riayah' => '5103',
            'Biaya Honorarium' => '5104',
            'Biaya Konsumsi' => '5105',
            'Biaya Administrasi Bank' => '5106',
            'Biaya Pemeliharaan' => '5103',
            'Biaya Kebersihan' => '5108',
            'Biaya Kegiatan' => '5109',
            'Biaya Pengadaan' => '5110',
            'Penyaluran ZISWAF' => '5199',
        ];

        foreach ($aliases as $legacyName => $code) {
            $coa = DB::table('coa')->where('kode_akun', $code)->first();

            if (! $coa) {
                continue;
            }

            DB::table('pengeluaran')
                ->whereNull('coa_debit_id')
                ->where('kategori', $legacyName)
                ->update([
                    'coa_debit_id' => $coa->id,
                    'kategori' => $coa->nama_akun,
                    'updated_at' => now(),
                ]);
        }
    }
};
