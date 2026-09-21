<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoaSeeder extends Seeder
{
    public function run(): void
    {
        $coa = config('coa.accounts', []);

        foreach ($coa as [$headerAkun, $kodeAkun, $namaAkun]) {
            DB::table('coa')->updateOrInsert(
                ['kode_akun' => $kodeAkun],
                [
                    'header_akun' => $headerAkun,
                    'nama_akun' => $namaAkun,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
