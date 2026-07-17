<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\Pengeluaran;
use App\Models\Penggajian;
use App\Models\ZiswafPenerimaan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class LaporanController extends Controller
{
    public function jurnalUmum()
    {
        /*
         * PembayaranSiswa sudah tidak digunakan.
         * Pemasukan FINUS sekarang berasal dari tabel ziswaf_penerimaan.
         * Hanya transaksi yang sudah diterima, serta data lama yang statusnya null,
         * yang dimasukkan ke laporan keuangan.
         */
        $penerimaan = $this->queryPenerimaanLaporan()
            ->with(['muzakki', 'coa'])
            ->orderByDesc('tanggal')
            ->get();

        /*
         * Pengeluaran gaji tidak diambil di sini karena sudah ditampilkan dari
         * tabel penggajian. Ini mencegah pencatatan gaji menjadi dua kali.
         */
        $pengeluaran = $this->queryPengeluaranOperasional()
            ->orderByDesc('tanggal')
            ->get();

        $penggajian = Penggajian::with('pegawai')
            ->where('status_penggajian', 'sudah_dibayar')
            ->whereNotNull('tanggal')
            ->orderByDesc('tanggal')
            ->get();

        $coaIds = $pengeluaran
            ->flatMap(fn (Pengeluaran $item): array => [
                $item->coa_debit_id,
                $item->coa_kredit_id,
            ])
            ->filter()
            ->unique()
            ->values();

        $namaCoa = $coaIds->isEmpty()
            ? collect()
            : Coa::whereIn('id', $coaIds)->pluck('nama_akun', 'id');

        $jurnals = collect();

        // Penerimaan ZISWAF: debit Kas/Bank, kredit akun penerimaan.
        foreach ($penerimaan as $item) {
            $jumlah = (int) $item->nominal;

            if ($jumlah <= 0) {
                continue;
            }

            $tanggal = Carbon::parse($item->tanggal)->format('Y-m-d');
            $namaJamaah = $item->muzakki?->name ?? 'Jamaah';
            $jenisZiswaf = $this->labelJenisZiswaf($item->jenis_ziswaf);
            $akunKas = $this->akunKasBerdasarkanMetode($item->metode_pembayaran);
            $akunPenerimaan = $item->coa?->nama_akun ?? 'Penerimaan ' . $jenisZiswaf;
            $keterangan = 'Penerimaan ' . $jenisZiswaf . ' dari ' . $namaJamaah;

            $jurnals->push((object) [
                'tanggal' => $tanggal,
                'tipe' => 'debit',
                'jumlah' => $jumlah,
                'akun' => $akunKas,
                'keterangan' => $keterangan,
                'referensi' => 'ZISWAF-' . $item->id,
            ]);

            $jurnals->push((object) [
                'tanggal' => $tanggal,
                'tipe' => 'kredit',
                'jumlah' => $jumlah,
                'akun' => $akunPenerimaan,
                'keterangan' => $keterangan,
                'referensi' => 'ZISWAF-' . $item->id,
            ]);
        }

        // Pengeluaran operasional: debit akun beban, kredit Kas/Bank.
        foreach ($pengeluaran as $item) {
            $jumlah = $this->nilaiPengeluaran($item);

            if ($jumlah <= 0) {
                continue;
            }

            $tanggal = Carbon::parse($item->tanggal)->format('Y-m-d');
            $keterangan = $this->keteranganPengeluaran($item);
            $akunDebit = $namaCoa->get($item->coa_debit_id)
                ?? $item->kategori
                ?? 'Beban Operasional';
            $akunKredit = $namaCoa->get($item->coa_kredit_id) ?? 'Kas';

            $jurnals->push((object) [
                'tanggal' => $tanggal,
                'tipe' => 'debit',
                'jumlah' => $jumlah,
                'akun' => $akunDebit,
                'keterangan' => $keterangan,
                'referensi' => 'PGL-' . $item->id,
            ]);

            $jurnals->push((object) [
                'tanggal' => $tanggal,
                'tipe' => 'kredit',
                'jumlah' => $jumlah,
                'akun' => $akunKredit,
                'keterangan' => $keterangan,
                'referensi' => 'PGL-' . $item->id,
            ]);
        }

        // Penggajian yang sudah dibayar: debit Beban Gaji, kredit Kas.
        foreach ($penggajian as $item) {
            $jumlah = (int) $item->total_gaji;

            if ($jumlah <= 0 || empty($item->tanggal)) {
                continue;
            }

            $tanggal = Carbon::parse($item->tanggal)->format('Y-m-d');
            $namaPegawai = $item->pegawai?->nama_pegawai ?? 'Pegawai';
            $keterangan = 'Pembayaran gaji ' . $namaPegawai . ' periode ' . $item->periode;

            $jurnals->push((object) [
                'tanggal' => $tanggal,
                'tipe' => 'debit',
                'jumlah' => $jumlah,
                'akun' => 'Beban Gaji',
                'keterangan' => $keterangan,
                'referensi' => 'GAJI-' . $item->id,
            ]);

            $jurnals->push((object) [
                'tanggal' => $tanggal,
                'tipe' => 'kredit',
                'jumlah' => $jumlah,
                'akun' => 'Kas',
                'keterangan' => $keterangan,
                'referensi' => 'GAJI-' . $item->id,
            ]);
        }

        $jurnals = $jurnals
            ->sortByDesc('tanggal')
            ->values();

        return view('admin.laporan.jurnal-umum', compact('jurnals'));
    }

    public function arusKas()
    {
        $pemasukanBulanan = $this->queryPenerimaanLaporan()
            ->selectRaw('MONTH(tanggal) AS bulan, YEAR(tanggal) AS tahun, SUM(nominal) AS total')
            ->groupByRaw('YEAR(tanggal), MONTH(tanggal)')
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->get();

        $pengeluaranBulanan = $this->queryPengeluaranOperasional()
            ->selectRaw(
                'MONTH(tanggal) AS bulan, YEAR(tanggal) AS tahun, '
                . 'SUM(COALESCE(NULLIF(nominal, 0), jumlah, 0)) AS total'
            )
            ->groupByRaw('YEAR(tanggal), MONTH(tanggal)')
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->get();

        $penggajianBulanan = Penggajian::query()
            ->selectRaw('MONTH(tanggal) AS bulan, YEAR(tanggal) AS tahun, SUM(total_gaji) AS total')
            ->where('status_penggajian', 'sudah_dibayar')
            ->whereNotNull('tanggal')
            ->groupByRaw('YEAR(tanggal), MONTH(tanggal)')
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->get();

        $totalPemasukan = (int) $this->queryPenerimaanLaporan()->sum('nominal');

        $totalPengeluaranOperasional = (int) $this->queryPengeluaranOperasional()
            ->selectRaw('COALESCE(SUM(COALESCE(NULLIF(nominal, 0), jumlah, 0)), 0) AS total')
            ->value('total');

        $totalPenggajian = (int) Penggajian::where('status_penggajian', 'sudah_dibayar')
            ->sum('total_gaji');

        $totalPengeluaran = $totalPengeluaranOperasional + $totalPenggajian;
        $saldo = $totalPemasukan - $totalPengeluaran;

        return view('admin.laporan.arus-kas', compact(
            'pemasukanBulanan',
            'pengeluaranBulanan',
            'penggajianBulanan',
            'totalPemasukan',
            'totalPengeluaran',
            'saldo'
        ));
    }

    /**
     * Laporan arus kas berdasarkan transaksi ZISWAF, pengeluaran, dan penggajian.
     */
    public function arusKasDariJurnal()
    {
        $penerimaan = $this->queryPenerimaanLaporan()
            ->with('muzakki')
            ->orderByDesc('tanggal')
            ->get();

        $pengeluaran = $this->queryPengeluaranOperasional()
            ->orderByDesc('tanggal')
            ->get();

        $penggajian = Penggajian::with('pegawai')
            ->where('status_penggajian', 'sudah_dibayar')
            ->whereNotNull('tanggal')
            ->orderByDesc('tanggal')
            ->get();

        $arusKas = [
            'operasi' => [
                'masuk' => [
                    'penerimaan_ziswaf' => 0,
                    'pendapatan_lain' => 0,
                    'total' => 0,
                ],
                'keluar' => [
                    'beban_gaji' => 0,
                    'beban_sewa' => 0,
                    'beban_listrik' => 0,
                    'beban_telepon' => 0,
                    'beban_marketing' => 0,
                    'beban_administrasi' => 0,
                    'beban_operasional_lain' => 0,
                    'persediaan_dibeli' => 0,
                    'pajak_dibayar' => 0,
                    'bunga_dibayar' => 0,
                    'total' => 0,
                ],
                'bersih' => 0,
            ],
            'investasi' => [
                'masuk' => [
                    'penjualan_aset_tetap' => 0,
                    'penjualan_investasi' => 0,
                    'pengembalian_pinjaman' => 0,
                    'total' => 0,
                ],
                'keluar' => [
                    'pembelian_aset_tetap' => 0,
                    'pembelian_investasi' => 0,
                    'pemberian_pinjaman' => 0,
                    'total' => 0,
                ],
                'bersih' => 0,
            ],
            'pendanaan' => [
                'masuk' => [
                    'penambahan_modal' => 0,
                    'pinjaman_diterima' => 0,
                    'penerbitan_saham' => 0,
                    'total' => 0,
                ],
                'keluar' => [
                    'pembayaran_dividen' => 0,
                    'pengembalian_modal' => 0,
                    'pelunasan_pinjaman' => 0,
                    'pembelian_saham_treasury' => 0,
                    'total' => 0,
                ],
                'bersih' => 0,
            ],
            'detail_transaksi' => [],
        ];

        foreach ($penerimaan as $item) {
            $jumlah = (int) $item->nominal;

            if ($jumlah <= 0) {
                continue;
            }

            $jenisZiswaf = $this->labelJenisZiswaf($item->jenis_ziswaf);
            $namaJamaah = $item->muzakki?->name ?? 'Jamaah';

            $arusKas['operasi']['masuk']['penerimaan_ziswaf'] += $jumlah;
            $arusKas['operasi']['masuk']['total'] += $jumlah;

            $arusKas['detail_transaksi'][] = [
                'tanggal' => Carbon::parse($item->tanggal)->format('Y-m-d'),
                'keterangan' => 'Penerimaan ' . $jenisZiswaf . ' dari ' . $namaJamaah,
                'kategori' => 'operasi',
                'jenis' => 'masuk',
                'sub_kategori' => 'penerimaan_ziswaf',
                'jumlah' => $jumlah,
            ];
        }

        foreach ($pengeluaran as $item) {
            $jumlah = $this->nilaiPengeluaran($item);

            if ($jumlah <= 0) {
                continue;
            }

            $keterangan = $this->keteranganPengeluaran($item);
            $kategori = $this->klasifikasikanPengeluaranPSAK($keterangan);
            $subKategori = $this->getSubKategoriPengeluaran($keterangan, $kategori);

            $arusKas[$kategori]['keluar'][$subKategori] += $jumlah;
            $arusKas[$kategori]['keluar']['total'] += $jumlah;

            $arusKas['detail_transaksi'][] = [
                'tanggal' => Carbon::parse($item->tanggal)->format('Y-m-d'),
                'keterangan' => $keterangan,
                'kategori' => $kategori,
                'jenis' => 'keluar',
                'sub_kategori' => $subKategori,
                'jumlah' => $jumlah,
            ];
        }

        foreach ($penggajian as $item) {
            $jumlah = (int) $item->total_gaji;

            if ($jumlah <= 0) {
                continue;
            }

            $namaPegawai = $item->pegawai?->nama_pegawai ?? 'Pegawai';

            $arusKas['operasi']['keluar']['beban_gaji'] += $jumlah;
            $arusKas['operasi']['keluar']['total'] += $jumlah;

            $arusKas['detail_transaksi'][] = [
                'tanggal' => Carbon::parse($item->tanggal)->format('Y-m-d'),
                'keterangan' => 'Gaji ' . $namaPegawai . ' periode ' . $item->periode,
                'kategori' => 'operasi',
                'jenis' => 'keluar',
                'sub_kategori' => 'beban_gaji',
                'jumlah' => $jumlah,
            ];
        }

        foreach (['operasi', 'investasi', 'pendanaan'] as $kategori) {
            $arusKas[$kategori]['bersih'] =
                $arusKas[$kategori]['masuk']['total']
                - $arusKas[$kategori]['keluar']['total'];
        }

        $totalBersih = $arusKas['operasi']['bersih']
            + $arusKas['investasi']['bersih']
            + $arusKas['pendanaan']['bersih'];

        usort(
            $arusKas['detail_transaksi'],
            fn (array $a, array $b): int => strcmp($b['tanggal'], $a['tanggal'])
        );

        return view('admin.laporan.arus-kas-psak', compact('arusKas', 'totalBersih'));
    }

    private function queryPenerimaanLaporan(): Builder
    {
        return ZiswafPenerimaan::query()
            ->where(function (Builder $query): void {
                $query->where('status_verifikasi', 'diterima')
                    ->orWhereNull('status_verifikasi');
            });
    }

    private function queryPengeluaranLaporan(): Builder
    {
        return Pengeluaran::query()
            ->where(function (Builder $query): void {
                $query->where('status_verifikasi', 'diterima')
                    ->orWhereNull('status_verifikasi');
            });
    }

    private function queryPengeluaranOperasional(): Builder
    {
        return $this->queryPengeluaranLaporan()
            ->where(function (Builder $query): void {
                $query->whereNull('jenis')
                    ->orWhere('jenis', '!=', 'gaji');
            });
    }

    private function labelJenisZiswaf(?string $jenis): string
    {
        return match ($jenis) {
            'zakat_maal' => 'Zakat Maal',
            'zakat_fitrah' => 'Zakat Fitrah',
            'infaq' => 'Infak',
            'shadaqah' => 'Sedekah',
            'wakaf' => 'Wakaf',
            'fidyah' => 'Fidyah',
            default => 'ZISWAF',
        };
    }

    private function akunKasBerdasarkanMetode(?string $metode): string
    {
        return match ($metode) {
            'transfer', 'transfer_bank', 'virtual_account', 'qris' => 'Bank',
            default => 'Kas',
        };
    }

    private function nilaiPengeluaran(Pengeluaran $pengeluaran): int
    {
        $nominal = (int) ($pengeluaran->nominal ?? 0);

        return $nominal > 0
            ? $nominal
            : (int) ($pengeluaran->jumlah ?? 0);
    }

    private function keteranganPengeluaran(Pengeluaran $pengeluaran): string
    {
        return $pengeluaran->keterangan
            ?: $pengeluaran->deskripsi
            ?: $pengeluaran->kategori
            ?: 'Pengeluaran operasional';
    }

    private function klasifikasikanPengeluaranPSAK(string $keterangan): string
    {
        $keterangan = strtolower($keterangan);

        if (
            str_contains($keterangan, 'aset')
            || str_contains($keterangan, 'gedung')
            || str_contains($keterangan, 'kendaraan')
            || str_contains($keterangan, 'mesin')
            || str_contains($keterangan, 'investasi')
            || str_contains($keterangan, 'tanah')
        ) {
            return 'investasi';
        }

        if (
            str_contains($keterangan, 'pinjaman')
            || str_contains($keterangan, 'hutang')
            || str_contains($keterangan, 'modal')
            || str_contains($keterangan, 'dividen')
            || str_contains($keterangan, 'saham')
        ) {
            return 'pendanaan';
        }

        return 'operasi';
    }

    private function getSubKategoriPengeluaran(string $keterangan, string $kategori): string
    {
        $keterangan = strtolower($keterangan);

        if ($kategori === 'investasi') {
            if (
                str_contains($keterangan, 'aset')
                || str_contains($keterangan, 'gedung')
                || str_contains($keterangan, 'kendaraan')
                || str_contains($keterangan, 'mesin')
                || str_contains($keterangan, 'tanah')
            ) {
                return 'pembelian_aset_tetap';
            }

            if (str_contains($keterangan, 'investasi')) {
                return 'pembelian_investasi';
            }

            if (str_contains($keterangan, 'pinjaman')) {
                return 'pemberian_pinjaman';
            }

            return 'pembelian_aset_tetap';
        }

        if ($kategori === 'pendanaan') {
            if (str_contains($keterangan, 'dividen')) {
                return 'pembayaran_dividen';
            }

            if (str_contains($keterangan, 'hutang') || str_contains($keterangan, 'pinjaman')) {
                return 'pelunasan_pinjaman';
            }

            if (str_contains($keterangan, 'modal')) {
                return 'pengembalian_modal';
            }

            return 'pelunasan_pinjaman';
        }

        if (str_contains($keterangan, 'gaji') || str_contains($keterangan, 'upah')) {
            return 'beban_gaji';
        }

        if (str_contains($keterangan, 'sewa')) {
            return 'beban_sewa';
        }

        if (str_contains($keterangan, 'listrik') || str_contains($keterangan, 'pln')) {
            return 'beban_listrik';
        }

        if (str_contains($keterangan, 'telepon') || str_contains($keterangan, 'pulsa')) {
            return 'beban_telepon';
        }

        if (str_contains($keterangan, 'marketing') || str_contains($keterangan, 'iklan')) {
            return 'beban_marketing';
        }

        if (str_contains($keterangan, 'administrasi') || str_contains($keterangan, 'admin')) {
            return 'beban_administrasi';
        }

        return 'beban_operasional_lain';
    }
}