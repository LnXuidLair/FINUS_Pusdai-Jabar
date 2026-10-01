<?php

namespace App\Services;

use App\Models\KetentuanPokokZakat;

class ZakatCalculatorService
{
    public function penghasilan(
        array $data,
        KetentuanPokokZakat $ketentuan
    ): array {
        $menggunakanNilaiBersih = array_key_exists('penghasilan_bersih', $data);
        $pendapatanUtama = $menggunakanNilaiBersih
            ? 0
            : $this->nilai($data, 'pendapatan_utama');
        $pendapatanLain = $menggunakanNilaiBersih
            ? 0
            : $this->nilai($data, 'pendapatan_lain');
        $pengurang = $menggunakanNilaiBersih
            ? 0
            : $this->nilai($data, 'pengurang');

        $penghasilanBruto = $menggunakanNilaiBersih
            ? $this->nilai($data, 'penghasilan_bersih')
            : $pendapatanUtama + $pendapatanLain;
        $pengurangTerpakai = min($pengurang, $penghasilanBruto);
        $dasarZakat = max($penghasilanBruto - $pengurangTerpakai, 0);
        $periode = ($data['periode_penghasilan'] ?? 'tahunan') === 'bulanan'
            ? 'bulanan'
            : 'tahunan';
        $nisabTahunan = max((int) round((float) $ketentuan->nisab_rupiah), 0);
        $nisabBulanan = $nisabTahunan > 0
            ? (int) round($nisabTahunan / 12)
            : 0;
        $nisab = $periode === 'bulanan' ? $nisabBulanan : $nisabTahunan;
        $sudahDibayar = $this->nilai($data, 'zakat_sudah_dibayar');

        $memenuhiNisab = $nisab > 0 && $dasarZakat >= $nisab;
        $kewajibanZakat = $memenuhiNisab
            ? $this->persentase($dasarZakat, (float) $ketentuan->kadar_persentase)
            : 0;
        $jumlahZakat = max($kewajibanZakat - $sudahDibayar, 0);
        $kelebihanBayar = max($sudahDibayar - $kewajibanZakat, 0);

        return [
            'jenis' => 'zakat_penghasilan',
            'periode' => $periode,
            'pendapatan_utama' => $pendapatanUtama,
            'pendapatan_lain' => $pendapatanLain,
            'penghasilan_bruto' => $penghasilanBruto,
            'pengurang' => $pengurangTerpakai,
            'metode_dasar_zakat' => $menggunakanNilaiBersih ? 'penghasilan_bersih' : 'rincian_penghasilan',
            'dasar_zakat' => $dasarZakat,
            'nisab' => $nisab,
            'nisab_tahunan' => $nisabTahunan,
            'nisab_bulanan' => $nisabBulanan,
            'persentase' => (float) $ketentuan->kadar_persentase,
            'memenuhi_nisab' => $memenuhiNisab,
            'memenuhi_haul' => null,
            'kewajiban_zakat' => $kewajibanZakat,
            'zakat_sudah_dibayar' => $sudahDibayar,
            'kelebihan_bayar' => $kelebihanBayar,
            'jumlah_zakat' => $jumlahZakat,
            'pesan' => match (true) {
                $nisab <= 0 => 'Nisab tahunan belum ditetapkan pada kebijakan zakat.',
                ! $memenuhiNisab => 'Penghasilan belum mencapai nisab '.$periode.'.',
                $jumlahZakat === 0 => 'Kewajiban zakat untuk periode ini sudah terpenuhi.',
                default => 'Penghasilan telah mencapai nisab '.$periode.'.',
            },
        ];
    }

    public function maal(
        array $data,
        KetentuanPokokZakat $ketentuan
    ): array {
        $rincianAset = [
            'uang_tunai' => $this->nilai($data, 'uang_tunai'),
            'tabungan_deposito' => $this->nilai($data, 'tabungan_deposito'),
            'emas_logam_mulia' => $this->nilai($data, 'emas_logam_mulia'),
            'surat_berharga' => $this->nilai($data, 'surat_berharga'),
            'piutang_tertagih' => $this->nilai($data, 'piutang_tertagih'),
            'persediaan_usaha' => $this->nilai($data, 'persediaan_usaha'),
            'aset_dagang' => $this->nilai($data, 'aset_dagang'),
            'harta_lain' => $this->nilai($data, 'harta_lain'),
        ];

        $totalAset = array_sum($rincianAset);
        $utangJatuhTempo = min($this->nilai($data, 'utang_jatuh_tempo'), $totalAset);
        $hartaBersih = max($totalAset - $utangJatuhTempo, 0);

        $memenuhiNisab = $hartaBersih >= (float) $ketentuan->nisab_rupiah;
        $memenuhiHaul = (bool) ($data['memenuhi_haul'] ?? false);
        $wajibZakat = $memenuhiNisab && $memenuhiHaul;

        return [
            'jenis' => 'zakat_maal',
            'rincian_aset' => $rincianAset,
            'total_aset' => $totalAset,
            'utang_jatuh_tempo' => $utangJatuhTempo,
            'dasar_zakat' => $hartaBersih,
            'nisab' => (float) $ketentuan->nisab_rupiah,
            'persentase' => (float) $ketentuan->kadar_persentase,
            'memenuhi_nisab' => $memenuhiNisab,
            'memenuhi_haul' => $memenuhiHaul,
            'jumlah_zakat' => $wajibZakat ? $this->persentase($hartaBersih, (float) $ketentuan->kadar_persentase) : 0,
            'pesan' => match (true) {
                ! $memenuhiNisab => 'Harta bersih belum mencapai nisab zakat maal.',
                ! $memenuhiHaul => 'Harta belum memenuhi haul satu tahun.',
                default => 'Harta telah mencapai nisab dan memenuhi haul.',
            },
        ];
    }

    public function fitrah(
        array $data,
        KetentuanPokokZakat $ketentuan,
        float $hargaBerasPerKg
    ): array {
        $jumlahJiwa = max((int) ($data['jumlah_jiwa'] ?? 0), 0);
        $nominalPerJiwa = $ketentuan->berat_fitrah_kg * $hargaBerasPerKg;

        return [
            'jenis' => 'zakat_fitrah',
            'jumlah_jiwa' => $jumlahJiwa,
            'nominal_per_jiwa' => $nominalPerJiwa,
            'beras_per_jiwa_kg' => (float) $ketentuan->berat_fitrah_kg,
            'beras_per_jiwa_liter' => (float) $ketentuan->berat_fitrah_liter,
            'dasar_zakat' => $jumlahJiwa * $nominalPerJiwa,
            'nisab' => null,
            'persentase' => null,
            'memenuhi_nisab' => null,
            'memenuhi_haul' => null,
            'jumlah_zakat' => $jumlahJiwa * $nominalPerJiwa,
            'pesan' => 'Nominal dihitung berdasarkan jumlah jiwa dan ketetapan per jiwa yang aktif.',
        ];
    }

    private function nilai(array $data, string $key): int
    {
        return max((int) ($data[$key] ?? 0), 0);
    }

    private function persentase(int $nilai, float $persentase): int
    {
        return (int) round($nilai * ($persentase / 100));
    }
}
