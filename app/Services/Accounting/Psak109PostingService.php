<?php

namespace App\Services\Accounting;

use App\Models\Coa;
use App\Models\JurnalDetail;
use App\Models\JurnalUmum;
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
            if ($jenisDana === 'wakaf' && $penerimaan->wakaf_type === 'temporer') {
                $jenisDana = 'wakaf_temporer';
            }
            $psakReference = $this->resolvePsakReferenceForReceipt($penerimaan, $jenisDana);
            $coaKas = $this->resolveCoaKas($penerimaan->metode_pembayaran);
            $coaPenerimaan = $this->resolveCoaPenerimaan($penerimaan);

            $nominal = (int) $penerimaan->nominal;

            // Infak/sedekah adalah dana umum masjid dan tidak dipotong otomatis
            // menjadi bagian amil. Zakat juga tetap disalurkan penuh kepada asnaf.
            $penerimaan->persentase_amil = 0;
            $penerimaan->nominal_amil = 0;

            // 1. Debit Kas/Bank (1 Rekening Utama dengan penanda jenis_dana)
            JurnalDetail::create([
                'jurnal_id' => $jurnal->id,
                'coa_id' => $coaKas->id,
                'deskripsi' => 'Penerimaan kas/bank '.$penerimaan->jenis_ziswaf,
                'debit' => $nominal,
                'credit' => 0,
                'jenis_dana' => $jenisDana,
                'restriction_type' => null,
                'psak_reference' => $psakReference,
            ]);

            // 2. Kredit Akun Penerimaan ZISWAF
            JurnalDetail::create([
                'jurnal_id' => $jurnal->id,
                'coa_id' => $coaPenerimaan->id,
                'deskripsi' => $coaPenerimaan->nama_akun,
                'debit' => 0,
                'credit' => $nominal,
                'jenis_dana' => $jenisDana,
                'restriction_type' => null,
                'psak_reference' => $psakReference,
            ]);

            // 3. PSAK 112: imbalan nazhir hanya dari hasil pengelolaan yang terealisasi.
            $this->alokasiBagianNazhir($jurnal, $penerimaan, $jenisDana, $nominal);

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
            $pengeluaran->loadMissing('transactionCategory.coa');
            $transactionCategory = $pengeluaran->transactionCategory;

            if (! $transactionCategory) {
                throw new \DomainException(
                    'Pengeluaran belum memiliki Kategori Transaksi. Lengkapi pemetaan kategori sebelum jurnal diposting.'
                );
            }

            if (! $transactionCategory->is_active) {
                throw new \DomainException(
                    "Kategori transaksi \"{$transactionCategory->name}\" tidak aktif. Pilih kategori aktif sebelum jurnal diposting."
                );
            }

            $coaDebit = $transactionCategory->coa;
            if (! $coaDebit) {
                throw new \DomainException(
                    "Kategori \"{$transactionCategory->name}\" belum memiliki pemetaan akun debit. Hubungi administrator."
                );
            }

            if ($pengeluaran->coa_debit_id && (int) $pengeluaran->coa_debit_id !== (int) $coaDebit->id) {
                throw new \DomainException(
                    "Akun debit pengeluaran tidak sesuai dengan pemetaan kategori \"{$transactionCategory->name}\"."
                );
            }

            if (! $pengeluaran->jenis_dana || ! $transactionCategory->allowsFund($pengeluaran->jenis_dana)) {
                throw new \DomainException(
                    "Golongan dana pengeluaran tidak diizinkan untuk kategori \"{$transactionCategory->name}\"."
                );
            }

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

            $coaKredit = $pengeluaran->coa_kredit_id
                ? Coa::find($pengeluaran->coa_kredit_id)
                : $this->resolveCoaKas('kas');

            if (! $coaKredit || ! in_array($coaKredit->kode_akun, ['1101', '1102'], true)) {
                throw new \DomainException('Sumber pembayaran tidak valid. Pilih akun Kas atau Bank yang tersedia.');
            }

            $jenisDana = $pengeluaran->jenis_dana;
            $kategoriBidang = $this->resolveKategoriBidang($coaDebit);
            $psakReference = $this->resolvePsakReference($jenisDana);

            $zakatDetails = $coaDebit->kode_akun === '5210'
                ? $pengeluaran->zakatPenyaluran()->get()
                : collect();

            if ($zakatDetails->isNotEmpty()) {
                foreach ($zakatDetails as $detail) {
                    $asnaf = ucwords(str_replace('_', ' ', $detail->asnaf));
                    $penerima = $detail->nama_penerima
                        ? ' - '.$detail->nama_penerima
                        : '';
                    JurnalDetail::create([
                        'jurnal_id' => $jurnal->id,
                        'coa_id' => $coaDebit->id,
                        'deskripsi' => "Penyaluran zakat - {$asnaf}{$penerima} ({$detail->jumlah_penerima} orang)",
                        'debit' => $detail->nominal,
                        'credit' => 0,
                        'jenis_dana' => 'zakat',
                        'psak_reference' => 'PSAK 109',
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
                    'restriction_type' => null,
                    'psak_reference' => $psakReference,
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
                'restriction_type' => null,
                'psak_reference' => $psakReference,
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
     * PSAK 112 membatasi imbalan nazhir pada hasil neto pengelolaan wakaf
     * yang telah terealisasi dalam kas dan setara kas.
     */
    protected function alokasiBagianNazhir(
        JurnalUmum $jurnal,
        ZiswafPenerimaan $penerimaan,
        string $jenisDana,
        int $nominal
    ): void {
        if ($jenisDana !== 'wakaf' || $penerimaan->wakaf_type !== 'hasil_pengelolaan') {
            $penerimaan->persentase_nazhir = 0;
            $penerimaan->nominal_nazhir = 0;
            $penerimaan->saveQuietly();

            return;
        }

        $persentase = min(10, max(0, (float) $penerimaan->persentase_nazhir));
        $nominalNazhir = (int) round($nominal * ($persentase / 100));

        $penerimaan->persentase_nazhir = $persentase;
        $penerimaan->nominal_nazhir = $nominalNazhir;
        $penerimaan->saveQuietly();

        if ($nominalNazhir <= 0) {
            return;
        }

        $bebanNazhir = $this->requireCoa('5412', 'Imbalan Nazhir atas Hasil Pengelolaan Wakaf');
        $bagianNazhir = $this->requireCoa('4310', 'Bagian Nazhir dari Hasil Pengelolaan Wakaf');

        JurnalDetail::create([
            'jurnal_id' => $jurnal->id,
            'coa_id' => $bebanNazhir->id,
            'deskripsi' => 'Imbalan nazhir '.$persentase.'% dari hasil pengelolaan wakaf',
            'debit' => $nominalNazhir,
            'credit' => 0,
            'jenis_dana' => 'wakaf',
            'psak_reference' => 'PSAK 112',
        ]);

        JurnalDetail::create([
            'jurnal_id' => $jurnal->id,
            'coa_id' => $bagianNazhir->id,
            'deskripsi' => 'Bagian nazhir dari hasil pengelolaan wakaf',
            'debit' => 0,
            'credit' => $nominalNazhir,
            'jenis_dana' => 'nazhir',
            'psak_reference' => 'PSAK 112',
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
        $saldoWakafTemporer = 0;
        $saldoNazhir = 0;
        $saldoOperasional = 0;
        $totalBank = 0;

        foreach ($detailKas as $d) {
            $mutasi = (float) $d->debit - (float) $d->credit;
            $totalBank += $mutasi;

            match ($d->jenis_dana) {
                'zakat' => $saldoZakat += $mutasi,
                'infak', 'infak_sedekah' => $saldoInfak += $mutasi,
                'amil' => $saldoAmil += $mutasi,
                'wakaf' => $saldoWakaf += $mutasi,
                'wakaf_temporer' => $saldoWakafTemporer += $mutasi,
                'nazhir' => $saldoNazhir += $mutasi,
                'operasional' => $saldoOperasional += $mutasi,
                default => $saldoAmil += $mutasi,
            };
        }

        // Pertahankan dampak jurnal alokasi lama tanpa membuat akun baru.
        $legacyAccounts = Coa::query()
            ->whereIn('kode_akun', ['4301', '4302', '5210', '5312', '4310', '5412'])
            ->pluck('id', 'kode_akun');
        $detailAmilNonKas = JurnalDetail::with('jurnalUmum')
            ->whereNotIn('coa_id', $coaKasIds)
            ->get();
        foreach ($detailAmilNonKas as $d) {
            if (in_array($d->coa_id, array_filter([$legacyAccounts['4301'] ?? null, $legacyAccounts['4302'] ?? null]), true)) {
                // Kredit Bagian Amil menambah hak amil
                $saldoAmil += (float) $d->credit;
            }
            if (
                $d->coa_id == ($legacyAccounts['5210'] ?? null)
                && $d->jurnalUmum?->sumber_tabel === 'ziswaf_penerimaan'
            ) {
                // Alokasi hak amil tidak melalui kas, sehingga perlu mengurangi dana zakat di sini.
                $saldoZakat -= (float) $d->debit;
            }
            if ($d->coa_id == ($legacyAccounts['5312'] ?? null)) {
                // Debit Penyaluran Infak ke Amil mengurangi hak infak
                $saldoInfak -= (float) $d->debit;
            }
            if ($d->coa_id == ($legacyAccounts['4310'] ?? null)) {
                $saldoNazhir += (float) $d->credit;
            }
            if ($d->coa_id == ($legacyAccounts['5412'] ?? null)) {
                $saldoWakaf -= (float) $d->debit;
            }
        }

        return [
            'zakat' => $saldoZakat,
            'infak_sedekah' => $saldoInfak,
            'amil' => $saldoAmil,
            'wakaf' => $saldoWakaf,
            'wakaf_temporer' => $saldoWakafTemporer,
            'nazhir' => $saldoNazhir,
            'operasional' => $saldoOperasional,
            'total_bank' => $totalBank,
        ];
    }

    public function getSaldoKasBank(Coa $coa): float
    {
        if (! in_array($coa->kode_akun, ['1101', '1102'], true)) {
            return 0;
        }

        return (float) JurnalDetail::query()
            ->where('coa_id', $coa->id)
            ->selectRaw('COALESCE(SUM(debit - credit), 0) AS saldo')
            ->value('saldo');
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
        if (str_contains($jenis, 'infaq') || str_contains($jenis, 'infak') || str_contains($jenis, 'sedekah')) {
            return 'operasional';
        }
        if (str_contains($jenis, 'fidyah')) {
            return 'infak_sedekah';
        }
        if (str_contains($jenis, 'wakaf')) {
            return 'wakaf';
        }
        if (str_contains($jenis, 'parkir')) {
            return 'operasional';
        }

        return 'amil';
    }

    protected function resolveCoaKas(?string $metode): Coa
    {
        $metodeStr = strtolower($metode ?? '');

        if ($metodeStr === 'tunai' || $metodeStr === 'kas') {
            return $this->requireCoa('1101', 'Kas');
        }

        return $this->requireCoa('1102', 'Bank');
    }

    protected function resolveCoaPenerimaan(ZiswafPenerimaan $penerimaan): Coa
    {
        $jenis = strtolower($penerimaan->jenis_ziswaf ?? '');

        // Klasifikasi PSAK 112 harus mengalahkan pemetaan akun lama pada transaksi wakaf.
        if (str_contains($jenis, 'wakaf')) {
            return match ($penerimaan->wakaf_type) {
                'temporer' => $this->requireCoa('2201', 'Liabilitas Wakaf Temporer'),
                'hasil_pengelolaan' => $this->requireCoa('4109', 'Hasil Pengelolaan dan Pengembangan Wakaf'),
                default => $this->requireCoa('4107', 'Penerimaan Wakaf Permanen'),
            };
        }

        if ($penerimaan->coa_id) {
            $coa = Coa::find($penerimaan->coa_id);
            if ($coa) {
                return $coa;
            }
        }

        if (str_contains($jenis, 'zakat')) {
            return $this->requireCoa('4105', 'Penerimaan Zakat');
        }
        if (str_contains($jenis, 'sedekah') || str_contains($jenis, 'shadaqah')) {
            return $this->requireCoa('4106', 'Penerimaan Infak dan Sedekah');
        }
        if (str_contains($jenis, 'fidyah')) {
            return $this->requireCoa('4108', 'Penerimaan Fidyah');
        }
        if (str_contains($jenis, 'parkir')) {
            return $this->requireCoa('4103', 'Penerimaan Parkir dan Kegiatan');
        }

        // Default infak
        if (str_contains($penerimaan->metode_pembayaran ?? '', 'qris')) {
            return $this->requireCoa('4102', 'Infak Layanan QRIS');
        }

        return $this->requireCoa('4101', 'Infak Kotak Amal');
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
            return $this->requireCoa('5104', 'Beban Gaji dan Honorarium');
        }

        return $this->requireCoa('5199', 'Beban Operasional Lain-lain');
    }

    public function resolveJenisDanaPengeluaran(?Coa $coa, ?string $kategori): string
    {
        $code = $coa?->kode_akun ?? '';

        if ($code === '2201') {
            return 'wakaf_temporer';
        }
        if (str_starts_with($code, '51')) {
            return 'operasional';
        }
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

    protected function resolvePsakReference(string $jenisDana): ?string
    {
        return match ($jenisDana) {
            'zakat', 'infak_sedekah' => 'PSAK 109',
            'wakaf', 'wakaf_temporer', 'nazhir' => 'PSAK 112',
            default => null,
        };
    }

    protected function resolvePsakReferenceForReceipt(
        ZiswafPenerimaan $penerimaan,
        string $jenisDana
    ): ?string {
        $jenis = strtolower($penerimaan->jenis_ziswaf ?? '');

        if (str_contains($jenis, 'infaq') || str_contains($jenis, 'infak') || str_contains($jenis, 'sedekah')) {
            return 'PSAK 109';
        }

        return $this->resolvePsakReference($jenisDana);
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

    protected function requireCoa(string $kode, string $nama): Coa
    {
        $coa = Coa::query()->where('kode_akun', $kode)->first();
        if (! $coa) {
            throw new \DomainException("Akun {$kode} - {$nama} belum tersedia pada Master COA.");
        }

        return $coa;
    }

    public function resolveCoaBagianAmilZakat(): Coa
    {
        return $this->requireCoa('4301', 'Bagian Amil dari Zakat');
    }

    public function resolveCoaPenyaluranBagianAmilZakat(): Coa
    {
        return $this->requireCoa('5210', 'Penyaluran Zakat');
    }

    public function resolveCoaBagianNazhirWakaf(): Coa
    {
        return $this->requireCoa('4310', 'Bagian Nazhir dari Hasil Pengelolaan Wakaf');
    }

    public function resolveCoaImbalanNazhirWakaf(): Coa
    {
        return $this->requireCoa('5412', 'Imbalan Nazhir atas Hasil Pengelolaan Wakaf');
    }

    protected function keteranganPenerimaan(ZiswafPenerimaan $item): string
    {
        if (str_contains(strtolower($item->jenis_ziswaf ?? ''), 'parkir')) {
            $penanggungJawab = $item->pegawai?->nama_pegawai ?? 'pegawai';

            return 'Penerimaan parkir per shift oleh '.$penanggungJawab;
        }

        $namaJamaah = $item->muzakki?->name ?? 'Jamaah';
        $jenis = ucfirst(str_replace('_', ' ', $item->jenis_ziswaf ?? 'Penerimaan'));

        return 'Penerimaan '.$jenis.' dari '.$namaJamaah;
    }
}
