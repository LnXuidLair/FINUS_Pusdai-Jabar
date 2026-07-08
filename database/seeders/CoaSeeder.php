<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoaSeeder extends Seeder
{
    public function run(): void
    {
        $coas = [
            // ASET
            [1, '1101', 'Kas'],
            [1, '1102', 'Bank'],

            // PENERIMAAN
            [4, '4101', 'Infaq Kotak Amal'],
            [4, '4102', 'Infaq Layanan QRIS'],
            [4, '4103', 'Infaq Parkir & Kegiatan'],
            [4, '4104', 'Bagi Hasil Bank'],
            [4, '4105', 'Zakat'],
            [4, '4106', 'Sedekah'],
            [4, '4107', 'Wakaf'],
            [4, '4108', 'Fidyah'],

            // PENGELUARAN
            [5, '5101', 'Biaya Bidang Idaroh'],
            [5, '5102', 'Biaya Bidang Imaroh'],
            [5, '5103', 'Biaya Bidang Riayah'],
            [5, '5104', 'Biaya Honorarium'],
            [5, '5105', 'Biaya Konsumsi'],
            [5, '5106', 'Biaya Administrasi Bank'],
            [5, '5107', 'Biaya Pemeliharaan'],
            [5, '5108', 'Biaya Kebersihan'],
            [5, '5109', 'Biaya Kegiatan'],
            [5, '5110', 'Biaya Pengadaan'],
            [5, '5111', 'Penyaluran ZISWAF'],
        ];

        foreach ($coas as [$headerAkun, $kodeAkun, $namaAkun]) {
            DB::table('coas')->updateOrInsert(
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