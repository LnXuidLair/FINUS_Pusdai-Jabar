<?php

namespace App\Services\Accounting;

use App\Models\Coa;
use App\Models\JurnalDetail;
use App\Models\JurnalUmum;
use App\Models\KebijakanAmil;
use App\Models\Pengeluaran;
use App\Models\Penggajian;
use App\Models\ZiswafPenerimaan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Psak109PostingService
{
    /**
     * Posting penerimaan ZISWAF ke jurnal umum dan detail secara permanen.
     */
    public function postPenerimaan(ZiswafPenerimaan $penerimaan): ?JurnalUmum
    {
        if ((int) $penerimaan->nominal <= 0) {
            return null;
        }

        return DB::transaction(function () use ($penerimaan) {
            // Jika sudah ada jurnal sebelumnya, hapus detail lama untuk sinkronisasi ulang
            if ($penerimaan->jurnal_id) {
                $existing = JurnalUmum::find($penerimaan->jurnal_id);
                if ($existing) {
                    $existing->detail()->delete();
                    $jurnal = $existing;
                }
            }

            if (! isset($jurnal)) {
                $tanggalObj = $penerimaan->tanggal ? Carbon::parse($penerimaan->tanggal) : now();
                $jurnal = JurnalUmum::create([
                    'tgl' => $tanggalObj->toDateString(),
                    'tanggal' => $tanggalObj->toDateString(),
                    'no_referensi' => 'JMASUK-'.str_pad($penerimaan->id, 6, '0', STR_PAD_LEFT),
                    'sumber_tabel' => 'ziswaf_penerimaan',
                    'sumber_id' => $penerimaan->id,
                    'deskripsi' => $this->keteranganPenerimaan($penerimaan),
                    'keterangan' => $penerimaan->keterangan,
                    'created_by' => $penerimaan->verified_by ?? auth()->id(),
                ]);
            }

            $jenisDana = $this->resolveJenisDana($penerimaan->jenis_ziswaf);
            $coaKas = $this->resolveCoaKas($penerimaan->metode_pembayaran);
            $coaPenerimaan = $this->resolveCoaPenerimaan($penerimaan);

            $nominal = (int) $penerimaan->nominal;

            // 1. Debit Kas/Bank (1 Rekening Utama dengan penanda jenis_dana)
            JurnalDetail::create([
                'jurnal_id' => $jurnal->id,
                'coa_id' => $coaKas->id,
                'deskripsi' => 'Penerimaan kas/bank '.$penerimaan->jenis_ziswaf,
                'debit' => $nominal,
                'credit' => 0,
                'jenis_dana' => $jenisDana,
            ]);

            // 2. Kredit Akun Penerimaan ZISWAF
            JurnalDetail::create([
                'jurnal_id' => $jurnal->id,
                'coa_id' => $coaPenerimaan->id,
                'deskripsi' => $coaPenerimaan->nama_akun,
                'debit' => 0,
                'credit' => $nominal,
                'jenis_dana' => $jenisDana,
            ]);

            // 3. Alokasi Bagian Amil (Jika berlaku dan persentase > 0)
            $this->alokasiBagianAmil($jurnal, $penerimaan, $jenisDana, $nominal);

            // Simpan link balik jurnal_id
            $penerimaan->jurnal_id = $jurnal->id;
            $penerimaan->saveQuietly();

            return $jurnal;
        });
    }

    /**
     * Posting pengeluaran operasional / penyaluran ke jurnal umum.
     */
    public function postPengeluaran(Pengeluaran $pengeluaran): ?JurnalUmum
    {
        $nominal = (int) ($pengeluaran->nominal ?: $pengeluaran->jumlah);
        if ($nominal <= 0) {
            return null;
        }

        return DB::transaction(function () use ($pengeluaran, $nominal) {
            if ($pengeluaran->jurnal_id) {
                $existing = JurnalUmum::find($pengeluaran->jurnal_id);
                if ($existing) {
                    $existing->detail()->delete();
                    $jurnal = $existing;
                }
            }

            if (! isset($jurnal)) {
                $tanggalObj = $pengeluaran->tanggal ? Carbon::parse($pengeluaran->tanggal) : now();
                $jurnal = JurnalUmum::create([
                    'tgl' => $tanggalObj->toDateString(),
                    'tanggal' => $tanggalObj->toDateString(),
                    'no_referensi' => 'JKELUAR-'.str_pad($pengeluaran->id, 6, '0', STR_PAD_LEFT),
                    'sumber_tabel' => 'pengeluaran',
                    'sumber_id' => $pengeluaran->id,
                    'deskripsi' => $pengeluaran->kategori.' - '.($pengeluaran->keterangan ?: $pengeluaran->deskripsi),
                    'keterangan' => $pengeluaran->keterangan ?: $pengeluaran->deskripsi,
                    'created_by' => $pengeluaran->verified_by ?? auth()->id(),
                ]);
            }

            $coaDebit = $pengeluaran->coa_debit_id
                ? Coa::find($pengeluaran->coa_debit_id)
                : $this->resolveCoaBeban($pengeluaran->kategori);

            $coaKredit = $pengeluaran->coa_kredit_id
                ? Coa::find($pengeluaran->coa_kredit_id)
                : $this->resolveCoaKas('kas');

            $jenisDana = $this->resolveJenisDanaPengeluaran($coaDebit, $pengeluaran->kategori);
            $kategoriBidang = $this->resolveKategoriBidang($coaDebit);

            $zakatDetails = $coaDebit->kode_akun === '5210'
                ? $pengeluaran->zakatPenyaluran()->get()
                : collect();

            if ($zakatDetails->isNotEmpty()) {
                foreach ($zakatDetails as $detail) {
                    $asnaf = ucwords(str_replace('_', ' ', $detail->asnaf));
                    JurnalDetail::create([
                        'jurnal_id' => $jurnal->id,
                        'coa_id' => $coaDebit->id,
                        'deskripsi' => "Penyaluran zakat - {$asnaf} ({$detail->jumlah_penerima} orang)",
                        'debit' => $detail->nominal,
                        'credit' => 0,
                        'jenis_dana' => 'zakat',
                        'asnaf' => $detail->asnaf,
                        'kategori_bidang' => null,
                    ]);
                }
            } else {
                JurnalDetail::create([
                    'jurnal_id' => $jurnal->id,
                    'coa_id' => $coaDebit->id,
                    'deskripsi' => $pengeluaran->deskripsi ?: $coaDebit->nama_akun,
                    'debit' => $nominal,
                    'credit' => 0,
                    'jenis_dana' => $jenisDana,
                    'kategori_bidang' => $kategoriBidang,
                ]);
            }

            // 2. Kredit Kas/Bank
            JurnalDetail::create([
                'jurnal_id' => $jurnal->id,
                'coa_id' => $coaKredit->id,
                'deskripsi' => 'Pengeluaran kas/bank',
                'debit' => 0,
                'credit' => $nominal,
                'jenis_dana' => $jenisDana,
                'kategori_bidang' => $kategoriBidang,
            ]);

            $pengeluaran->jurnal_id = $jurnal->id;
            $pengeluaran->saveQuietly();

            return $jurnal;
        });
    }

    /**
     * Posting penggajian yang sudah dibayar ke jurnal umum (Beban Pegawai Dana Amil).
     */
    public function postPenggajian(Penggajian $penggajian): ?JurnalUmum
    {
        $nominal = (int) $penggajian->total_gaji;
        if ($nominal <= 0 || empty($penggajian->tanggal)) {
            return null;
        }

        return DB::transaction(function () use ($penggajian, $nominal) {
            if ($penggajian->id_jurnal) {
                $existing = JurnalUmum::find($penggajian->id_jurnal);
                if ($existing) {
                    $existing->detail()->delete();
                    $jurnal = $existing;
                }
            }

            if (! isset($jurnal)) {
                $tanggalObj = Carbon::parse($penggajian->tanggal);
                $namaPegawai = $penggajian->pegawai?->nama_pegawai ?? 'Pegawai';
                $jurnal = JurnalUmum::create([
                    'tgl' => $tanggalObj->toDateString(),
                    'tanggal' => $tanggalObj->toDateString(),
                    'no_referensi' => 'JGAJI-'.str_pad($penggajian->id, 6, '0', STR_PAD_LEFT),
                    'sumber_tabel' => 'penggajian',
                    'sumber_id' => $penggajian->id,
                    'deskripsi' => 'Pembayaran gaji '.$namaPegawai.' periode '.$penggajian->periode,
                    'keterangan' => 'Gaji Pegawai Pusdai periode '.$penggajian->periode,
                    'created_by' => auth()->id(),
                ]);
            }

            $coaGaji = $this->resolveCoaBeban('Beban Gaji dan Honorarium');
            $coaKas = $this->resolveCoaKas('bank'); // Default bank atau kas

            // 1. Debit Beban Pegawai / Honorarium [Dana: Amil]
            JurnalDetail::create([
                'jurnal_id' => $jurnal->id,
                'coa_id' => $coaGaji->id,
                'deskripsi' => 'Beban Gaji '.($penggajian->pegawai?->nama_pegawai ?? ''),
                'debit' => $nominal,
                'credit' => 0,
                'jenis_dana' => 'amil',
                'kategori_bidang' => 'Idaroh',
            ]);

            // 2. Kredit Kas/Bank [Dana: Amil]
            JurnalDetail::create([
                'jurnal_id' => $jurnal->id,
                'coa_id' => $coaKas->id,
                'deskripsi' => 'Kas keluar pembayaran gaji',
                'debit' => 0,
                'credit' => $nominal,
                'jenis_dana' => 'amil',
                'kategori_bidang' => 'Idaroh',
            ]);

            $penggajian->id_jurnal = $jurnal->id;
            $penggajian->saveQuietly();

            return $jurnal;
        });
    }

    /**
     * Membatalkan / membalik jurnal saat transaksi dibatalkan atau ditolak.
     */
    public function reverseJurnal(?int $jurnalId, string $alasan = ''): void
    {
        if (! $jurnalId) {
            return;
        }

        DB::transaction(function () use ($jurnalId) {
            $jurnal = JurnalUmum::find($jurnalId);
            if (! $jurnal) {
                return;
            }

            // Hapus detail dan jurnal untuk menjaga kebersihan ledger transaksi aktif
            $jurnal->detail()->delete();
            $jurnal->delete();
        });
    }

    /**
     * Hitung alokasi hak amil dan catat jurnal transfer antar-dana.
     */
    protected function alokasiBagianAmil(JurnalUmum $jurnal, ZiswafPenerimaan $penerimaan, string $jenisDana, int $nominal): void
    {
        // Hanya dana Zakat dan Infak yang dialokasikan ke Amil
        if (! in_array($jenisDana, ['zakat', 'infak_sedekah'])) {
            return;
        }

        $sumberAturan = $jenisDana === 'zakat' ? 'zakat' : 'infaq_mutlaqah';
        $kebijakan = KebijakanAmil::ambilKebijakan($sumberAturan, $penerimaan->tanggal);

        $persentase = $kebijakan ? (float) $kebijakan->persentase_amil : ($jenisDana === 'zakat' ? 12.50 : 10.00);

        if ($persentase <= 0) {
            $penerimaan->persentase_amil = 0;
            $penerimaan->nominal_amil = 0;
            $penerimaan->saveQuietly();

            return;
        }

        $nominalAmil = (int) round($nominal * ($persentase / 100));

        // Simpan snapshot pada penerimaan
        $penerimaan->persentase_amil = $persentase;
        $penerimaan->nominal_amil = $nominalAmil;
        $penerimaan->kebijakan_amil_id = $kebijakan?->id;
        $penerimaan->saveQuietly();

        // Cari atau siapkan akun alokasi amil
        if ($jenisDana === 'zakat') {
            $coaDebetAlokasi = $this->findOrCreateCoa('5210', 'Penyaluran Zakat', 5);
            $coaKreditAlokasi = $this->findOrCreateCoa('4301', 'Bagian Amil dari Zakat', 4);
        } else {
            $coaDebetAlokasi = $this->findOrCreateCoa('5312', 'Alokasi Infak dan Sedekah - Bagian Amil', 5);
            $coaKreditAlokasi = $this->findOrCreateCoa('4302', 'Bagian Amil dari Infak dan Sedekah', 4);
        }

        // Debit: Penyaluran Bagian Amil [Mengurangi Dana Zakat/Infak]
        JurnalDetail::create([
            'jurnal_id' => $jurnal->id,
            'coa_id' => $coaDebetAlokasi->id,
            'deskripsi' => 'Hak amil '.$persentase.'% dari '.$penerimaan->jenis_ziswaf,
            'debit' => $nominalAmil,
            'credit' => 0,
            'jenis_dana' => $jenisDana,
            'asnaf' => $jenisDana === 'zakat' ? 'amil' : null,
        ]);

        // Kredit: Penerimaan Bagian Amil [Menambah Dana Amil]
        JurnalDetail::create([
            'jurnal_id' => $jurnal->id,
            'coa_id' => $coaKreditAlokasi->id,
            'deskripsi' => 'Bagian amil '.$persentase.'% dari '.$penerimaan->jenis_ziswaf,
            'debit' => 0,
            'credit' => $nominalAmil,
            'jenis_dana' => 'amil',
        ]);
    }

    /**
     * Hitung rekapitulasi saldo dana secara real-time dari 1 rekening kas/bank.
     */
    public function getSaldoDana(): array
    {
        $coaKasIds = Coa::whereIn('kode_akun', ['1101', '1102'])
            ->orWhere('nama_akun', 'like', '%Kas%')
            ->orWhere('nama_akun', 'like', '%Bank%')
            ->pluck('id');

        $detailKas = JurnalDetail::whereIn('coa_id', $coaKasIds)->get();

        $saldoZakat = 0;
        $saldoInfak = 0;
        $saldoAmil = 0;
        $saldoWakaf = 0;
        $totalBank = 0;

        foreach ($detailKas as $d) {
            $mutasi = (float) $d->debit - (float) $d->credit;
            $totalBank += $mutasi;

            match ($d->jenis_dana) {
                'zakat' => $saldoZakat += $mutasi,
                'infak', 'infak_sedekah' => $saldoInfak += $mutasi,
                'amil' => $saldoAmil += $mutasi,
                'wakaf' => $saldoWakaf += $mutasi,
                default => $saldoAmil += $mutasi,
            };
        }

        // Hitung juga dampak transfer hak amil (yang tidak melalui mutasi kas)
        $detailAmilNonKas = JurnalDetail::with('jurnalUmum')
            ->whereNotIn('coa_id', $coaKasIds)
            ->get();
        foreach ($detailAmilNonKas as $d) {
            if ($d->coa_id == $this->resolveCoaBagianAmilZakat()->id || $d->coa_id == $this->resolveCoaBagianAmilInfak()->id) {
                // Kredit Bagian Amil menambah hak amil
                $saldoAmil += (float) $d->credit;
            }
            if (
                $d->coa_id == $this->resolveCoaPenyaluranBagianAmilZakat()->id
                && $d->jurnalUmum?->sumber_tabel === 'ziswaf_penerimaan'
            ) {
                // Alokasi hak amil tidak melalui kas, sehingga perlu mengurangi dana zakat di sini.
                $saldoZakat -= (float) $d->debit;
            }
            if ($d->coa_id == $this->resolveCoaPenyaluranBagianAmilInfak()->id) {
                // Debit Penyaluran Infak ke Amil mengurangi hak infak
                $saldoInfak -= (float) $d->debit;
            }
        }

        return [
            'zakat' => $saldoZakat,
            'infak_sedekah' => $saldoInfak,
            'amil' => $saldoAmil,
            'wakaf' => $saldoWakaf,
            'total_bank' => $totalBank,
        ];
    }

    /**
     * Resolusi jenis dana berdasarkan string jenis ziswaf.
     */
    public function resolveJenisDana(?string $jenisZiswaf): string
    {
        $jenis = strtolower($jenisZiswaf ?? '');

        if (str_contains($jenis, 'zakat')) {
            return 'zakat';
        }
        if (str_contains($jenis, 'infaq') || str_contains($jenis, 'infak') || str_contains($jenis, 'sedekah') || str_contains($jenis, 'fidyah')) {
            return 'infak_sedekah';
        }
        if (str_contains($jenis, 'wakaf')) {
            return 'wakaf';
        }
        if (str_contains($jenis, 'parkir')) {
            return 'amil';
        }

        return 'amil';
    }

    protected function resolveCoaKas(?string $metode): Coa
    {
        $metodeStr = strtolower($metode ?? '');

        // Jika tunai -> Kas (1101)
        if ($metodeStr === 'tunai' || $metodeStr === 'kas') {
            $coa = Coa::where('kode_akun', '1101')->first();
            if ($coa) {
                return $coa;
            }
        }

        // Lainnya (transfer, qris, gateway) -> Bank (1102)
        $coa = Coa::where('kode_akun', '1102')->first();
        if ($coa) {
            return $coa;
        }

        return Coa::firstOrCreate(
            ['kode_akun' => '1102'],
            ['header_akun' => 1, 'nama_akun' => 'Bank']
        );
    }

    protected function resolveCoaPenerimaan(ZiswafPenerimaan $penerimaan): Coa
    {
        if ($penerimaan->coa_id) {
            $coa = Coa::find($penerimaan->coa_id);
            if ($coa) {
                return $coa;
            }
        }

        $jenis = strtolower($penerimaan->jenis_ziswaf ?? '');

        if (str_contains($jenis, 'zakat')) {
            return $this->findOrCreateCoa('4105', 'Penerimaan Zakat', 4);
        }
        if (str_contains($jenis, 'wakaf')) {
            return $this->findOrCreateCoa('4107', 'Penerimaan Wakaf', 4);
        }
        if (str_contains($jenis, 'sedekah') || str_contains($jenis, 'shadaqah')) {
            return $this->findOrCreateCoa('4106', 'Penerimaan Infak dan Sedekah', 4);
        }
        if (str_contains($jenis, 'fidyah')) {
            return $this->findOrCreateCoa('4108', 'Penerimaan Fidyah', 4);
        }
        if (str_contains($jenis, 'parkir')) {
            return $this->findOrCreateCoa('4103', 'Penerimaan Parkir dan Kegiatan', 4);
        }

        // Default infak
        if (str_contains($penerimaan->metode_pembayaran ?? '', 'qris')) {
            return $this->findOrCreateCoa('4102', 'Infak Layanan QRIS', 4);
        }

        return $this->findOrCreateCoa('4101', 'Infak Kotak Amal', 4);
    }

    protected function resolveCoaBeban(?string $kategori): Coa
    {
        $kat = trim($kategori ?? '');

        $coa = Coa::where('nama_akun', $kat)
            ->orWhere('kode_akun', explode(' - ', $kat)[0])
            ->first();

        if ($coa) {
            return $coa;
        }

        if (str_contains(strtolower($kat), 'honorarium') || str_contains(strtolower($kat), 'gaji')) {
            return $this->findOrCreateCoa('5104', 'Beban Gaji dan Honorarium', 5);
        }

        return $this->findOrCreateCoa('5199', 'Beban Operasional Lain-lain', 5);
    }

    protected function resolveJenisDanaPengeluaran(?Coa $coa, ?string $kategori): string
    {
        $code = $coa?->kode_akun ?? '';

        if (str_starts_with($code, '52')) {
            return 'zakat';
        }
        if (str_starts_with($code, '53') || str_starts_with($code, '55')) {
            return 'infak_sedekah';
        }
        if (str_starts_with($code, '54')) {
            return 'wakaf';
        }

        $name = strtolower($kategori ?? '');
        if (str_contains($name, 'zakat')) {
            return 'zakat';
        }
        if (str_contains($name, 'infak') || str_contains($name, 'sedekah') || str_contains($name, 'fidyah')) {
            return 'infak_sedekah';
        }
        if (str_contains($name, 'wakaf')) {
            return 'wakaf';
        }

        return 'amil';
    }

    protected function resolveKategoriBidang(?Coa $coa): ?string
    {
        return match ($coa?->kode_akun) {
            '5101', '5104', '5106', '5111', '5112', '5199' => 'Idaroh',
            '5102', '5105', '5109' => 'Imaroh',
            '5103', '5107', '5108', '5110' => 'Riayah',
            default => null,
        };
    }

    protected function findOrCreateCoa(string $kode, string $nama, int $header): Coa
    {
        return Coa::firstOrCreate(
            ['kode_akun' => $kode],
            ['header_akun' => $header, 'nama_akun' => $nama]
        );
    }

    public function resolveCoaBagianAmilZakat(): Coa
    {
        return $this->findOrCreateCoa('4301', 'Bagian Amil dari Zakat', 4);
    }

    public function resolveCoaBagianAmilInfak(): Coa
    {
        return $this->findOrCreateCoa('4302', 'Bagian Amil dari Infak dan Sedekah', 4);
    }

    public function resolveCoaPenyaluranBagianAmilZakat(): Coa
    {
        return $this->findOrCreateCoa('5210', 'Penyaluran Zakat', 5);
    }

    public function resolveCoaPenyaluranBagianAmilInfak(): Coa
    {
        return $this->findOrCreateCoa('5312', 'Alokasi Infak dan Sedekah - Bagian Amil', 5);
    }

    protected function keteranganPenerimaan(ZiswafPenerimaan $item): string
    {
        $namaJamaah = $item->muzakki?->name ?? 'Jamaah';
        $jenis = ucfirst(str_replace('_', ' ', $item->jenis_ziswaf ?? 'Penerimaan'));

        return 'Penerimaan '.$jenis.' dari '.$namaJamaah;
    }
}
