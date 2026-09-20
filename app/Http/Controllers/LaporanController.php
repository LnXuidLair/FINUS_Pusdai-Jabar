<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\JurnalUmum;
use App\Models\Pengeluaran;
use App\Models\Penggajian;
use App\Models\ZiswafPenerimaan;
use App\Services\Accounting\Psak109PostingService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    /**
     * Jurnal Umum: Menampilkan seluruh mutasi debit dan kredit permanen PSAK 109.
     */
    public function jurnalUmum(Request $request)
    {
        $jurnalUmumList = JurnalUmum::with(['detail.coa', 'createdBy'])
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();

        $jurnals = collect();

        if ($jurnalUmumList->isNotEmpty()) {
            foreach ($jurnalUmumList as $ju) {
                $tanggal = $ju->tanggal ? Carbon::parse($ju->tanggal)->format('Y-m-d') : '-';
                $deskripsi = $ju->deskripsi ?: ($ju->keterangan ?: 'Transaksi '.$ju->no_referensi);

                foreach ($ju->detail as $detail) {
                    $namaAkun = $detail->coa?->nama_akun ?? 'Akun '.$detail->coa_id;
                    $debit = (float) $detail->debit;
                    $credit = (float) $detail->credit;

                    if ($debit > 0) {
                        $jurnals->push((object) [
                            'tanggal' => $tanggal,
                            'tipe' => 'debit',
                            'jumlah' => $debit,
                            'akun' => $namaAkun,
                            'keterangan' => $deskripsi,
                            'referensi' => $ju->no_referensi,
                            'jenis_dana' => $detail->jenis_dana,
                        ]);
                    }

                    if ($credit > 0) {
                        $jurnals->push((object) [
                            'tanggal' => $tanggal,
                            'tipe' => 'kredit',
                            'jumlah' => $credit,
                            'akun' => $namaAkun,
                            'keterangan' => $deskripsi,
                            'referensi' => $ju->no_referensi,
                            'jenis_dana' => $detail->jenis_dana,
                        ]);
                    }
                }
            }
        } else {
            // Fallback virtual jika belum ada jurnal permanen yang tersimpan
            $penerimaan = $this->queryPenerimaanLaporan()
                ->with(['muzakki', 'coa'])
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

            foreach ($penerimaan as $item) {
                $jumlah = (int) $item->nominal;
                if ($jumlah <= 0) {
                    continue;
                }

                $tanggal = Carbon::parse($item->tanggal)->format('Y-m-d');
                $namaJamaah = $item->muzakki?->name ?? 'Jamaah';
                $jenisZiswaf = $this->labelJenisZiswaf($item->jenis_ziswaf);
                $akunKas = $this->akunKasBerdasarkanMetode($item->metode_pembayaran);
                $akunPenerimaan = $item->coa?->nama_akun ?? 'Penerimaan '.$jenisZiswaf;
                $keterangan = 'Penerimaan '.$jenisZiswaf.' dari '.$namaJamaah;

                $jurnals->push((object) [
                    'tanggal' => $tanggal,
                    'tipe' => 'debit',
                    'jumlah' => $jumlah,
                    'akun' => $akunKas,
                    'keterangan' => $keterangan,
                    'referensi' => 'ZISWAF-'.$item->id,
                ]);

                $jurnals->push((object) [
                    'tanggal' => $tanggal,
                    'tipe' => 'kredit',
                    'jumlah' => $jumlah,
                    'akun' => $akunPenerimaan,
                    'keterangan' => $keterangan,
                    'referensi' => 'ZISWAF-'.$item->id,
                ]);
            }

            foreach ($pengeluaran as $item) {
                $jumlah = $this->nilaiPengeluaran($item);
                if ($jumlah <= 0) {
                    continue;
                }

                $tanggal = Carbon::parse($item->tanggal)->format('Y-m-d');
                $keterangan = $this->keteranganPengeluaran($item);
                $akunDebit = $namaCoa->get($item->coa_debit_id) ?? $item->kategori ?? 'Beban Operasional';
                $akunKredit = $namaCoa->get($item->coa_kredit_id) ?? 'Kas';

                $jurnals->push((object) [
                    'tanggal' => $tanggal,
                    'tipe' => 'debit',
                    'jumlah' => $jumlah,
                    'akun' => $akunDebit,
                    'keterangan' => $keterangan,
                    'referensi' => 'PGL-'.$item->id,
                ]);

                $jurnals->push((object) [
                    'tanggal' => $tanggal,
                    'tipe' => 'kredit',
                    'jumlah' => $jumlah,
                    'akun' => $akunKredit,
                    'keterangan' => $keterangan,
                    'referensi' => 'PGL-'.$item->id,
                ]);
            }

            foreach ($penggajian as $item) {
                $jumlah = (int) $item->total_gaji;
                if ($jumlah <= 0 || empty($item->tanggal)) {
                    continue;
                }

                $tanggal = Carbon::parse($item->tanggal)->format('Y-m-d');
                $namaPegawai = $item->pegawai?->nama_pegawai ?? 'Pegawai';
                $keterangan = 'Pembayaran gaji '.$namaPegawai.' periode '.$item->periode;

                $jurnals->push((object) [
                    'tanggal' => $tanggal,
                    'tipe' => 'debit',
                    'jumlah' => $jumlah,
                    'akun' => 'Beban Gaji',
                    'keterangan' => $keterangan,
                    'referensi' => 'GAJI-'.$item->id,
                ]);

                $jurnals->push((object) [
                    'tanggal' => $tanggal,
                    'tipe' => 'kredit',
                    'jumlah' => $jumlah,
                    'akun' => 'Kas',
                    'keterangan' => $keterangan,
                    'referensi' => 'GAJI-'.$item->id,
                ]);
            }
        }

        return view('admin.laporan.jurnal-umum', compact('jurnals'));
    }

    /**
     * Jurnal Pemasukan: Buku Jurnal Khusus Penerimaan Kas/Bank ZISWAF berbasis PSAK 109.
     */
    public function jurnalPemasukan(Request $request)
    {
        $danaFilter = $request->input('dana');
        $tanggalDari = $request->input('tanggal_dari');
        $tanggalSampai = $request->input('tanggal_sampai');
        $search = trim($request->input('q') ?? '');

        $query = JurnalUmum::pemasukan()
            ->with(['detail.coa', 'createdBy'])
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        if ($tanggalDari) {
            $query->whereDate('tanggal', '>=', $tanggalDari);
        }
        if ($tanggalSampai) {
            $query->whereDate('tanggal', '<=', $tanggalSampai);
        }
        if ($danaFilter && $danaFilter !== 'all') {
            $query->whereHas('detail', function ($q) use ($danaFilter) {
                $q->where('jenis_dana', $danaFilter);
            });
        }
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('no_referensi', 'like', '%'.$search.'%')
                    ->orWhere('deskripsi', 'like', '%'.$search.'%')
                    ->orWhere('keterangan', 'like', '%'.$search.'%');
            });
        }

        $jurnals = $query->paginate(15)->withQueryString();

        // Ringkasan real-time
        $postingService = app(Psak109PostingService::class);
        $saldoDana = $postingService->getSaldoDana();

        // Rekapitulasi transaksi pemasukan
        $allPemasukanJurnals = (clone $query)->get();
        $totalPemasukanKas = 0;
        $totalZakat = 0;
        $totalInfak = 0;
        $totalAlokasiAmil = 0;

        foreach ($allPemasukanJurnals as $j) {
            foreach ($j->detail as $d) {
                $isKasBank = in_array($d->coa?->kode_akun, ['1101', '1102'])
                    || str_contains($d->coa?->nama_akun ?? '', 'Kas')
                    || str_contains($d->coa?->nama_akun ?? '', 'Bank');

                if ($isKasBank && (float) $d->debit > 0) {
                    $totalPemasukanKas += (float) $d->debit;

                    if ($d->jenis_dana === 'zakat') {
                        $totalZakat += (float) $d->debit;
                    } elseif (in_array($d->jenis_dana, ['infak', 'infak_sedekah'])) {
                        $totalInfak += (float) $d->debit;
                    }
                }

                // Bagian Amil (Kredit akun 4301 / 4302)
                if ($d->jenis_dana === 'amil' && (float) $d->credit > 0) {
                    $totalAlokasiAmil += (float) $d->credit;
                }
            }
        }

        $summary = [
            'total_pemasukan' => $totalPemasukanKas,
            'total_zakat' => $totalZakat,
            'total_infak' => $totalInfak,
            'total_amil' => $totalAlokasiAmil,
            'saldo_dana' => $saldoDana,
        ];

        return view('admin.laporan.jurnal-pemasukan', compact(
            'jurnals',
            'summary',
            'danaFilter',
            'tanggalDari',
            'tanggalSampai',
            'search'
        ));
    }

    /**
     * Jurnal Pengeluaran: Buku Jurnal Khusus Pengeluaran Kas/Bank (Penyaluran & Beban Amil) PSAK 109.
     */
    public function jurnalPengeluaran(Request $request)
    {
        $danaFilter = $request->input('dana');
        $tipeFilter = $request->input('tipe'); // all, penyaluran, operasional
        $tanggalDari = $request->input('tanggal_dari');
        $tanggalSampai = $request->input('tanggal_sampai');
        $search = trim($request->input('q') ?? '');

        $query = JurnalUmum::pengeluaran()
            ->with(['detail.coa', 'createdBy'])
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        if ($tanggalDari) {
            $query->whereDate('tanggal', '>=', $tanggalDari);
        }
        if ($tanggalSampai) {
            $query->whereDate('tanggal', '<=', $tanggalSampai);
        }
        if ($danaFilter && $danaFilter !== 'all') {
            $query->whereHas('detail', function ($q) use ($danaFilter) {
                $q->where('jenis_dana', $danaFilter);
            });
        }
        if ($tipeFilter && $tipeFilter !== 'all') {
            if ($tipeFilter === 'penyaluran') {
                $query->where(function ($q) {
                    $q->where('sumber_tabel', 'ziswaf_penyaluran')
                        ->orWhere('deskripsi', 'like', '%penyaluran%')
                        ->orWhereHas('detail', function ($dq) {
                            $dq->where('jenis_dana', '!=', 'amil');
                        });
                });
            } elseif ($tipeFilter === 'operasional') {
                $query->whereIn('sumber_tabel', ['pengeluaran', 'penggajian']);
            }
        }
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('no_referensi', 'like', '%'.$search.'%')
                    ->orWhere('deskripsi', 'like', '%'.$search.'%')
                    ->orWhere('keterangan', 'like', '%'.$search.'%');
            });
        }

        $jurnals = $query->paginate(15)->withQueryString();

        $postingService = app(Psak109PostingService::class);
        $saldoDana = $postingService->getSaldoDana();

        $allPengeluaranJurnals = (clone $query)->get();
        $totalPengeluaranKas = 0;
        $totalPenyaluranZakat = 0;
        $totalPenyaluranInfak = 0;
        $totalBebanAmil = 0;
        $totalPenyaluranWakaf = 0;

        foreach ($allPengeluaranJurnals as $j) {
            foreach ($j->detail as $d) {
                $isKasBank = in_array($d->coa?->kode_akun, ['1101', '1102'])
                    || str_contains($d->coa?->nama_akun ?? '', 'Kas')
                    || str_contains($d->coa?->nama_akun ?? '', 'Bank');

                if ($isKasBank && (float) $d->credit > 0) {
                    $totalPengeluaranKas += (float) $d->credit;
                }

                if (! $isKasBank && (float) $d->debit > 0) {
                    if ($d->jenis_dana === 'zakat') {
                        $totalPenyaluranZakat += (float) $d->debit;
                    } elseif (in_array($d->jenis_dana, ['infak', 'infak_sedekah'])) {
                        $totalPenyaluranInfak += (float) $d->debit;
                    } elseif ($d->jenis_dana === 'amil') {
                        $totalBebanAmil += (float) $d->debit;
                    } elseif ($d->jenis_dana === 'wakaf') {
                        $totalPenyaluranWakaf += (float) $d->debit;
                    }
                }
            }
        }

        $summary = [
            'total_pengeluaran' => $totalPengeluaranKas,
            'total_penyaluran_zakat' => $totalPenyaluranZakat,
            'total_penyaluran_infak' => $totalPenyaluranInfak,
            'total_beban_amil' => $totalBebanAmil,
            'total_penyaluran_wakaf' => $totalPenyaluranWakaf,
            'saldo_dana' => $saldoDana,
        ];

        return view('admin.laporan.jurnal-pengeluaran', compact(
            'jurnals',
            'summary',
            'danaFilter',
            'tipeFilter',
            'tanggalDari',
            'tanggalSampai',
            'search'
        ));
    }

    public function arusKas(Request $request)
    {
        $periode = $request->input('periode', 'bulanan'); // 'bulanan', 'tahunan', 'semua'
        $tahun = (int) $request->input('tahun', now()->year);
        $bulan = (int) $request->input('bulan', now()->month);

        // Helper filter tanggal
        $applyDateFilter = function ($query, $column = 'tanggal') use ($periode, $tahun, $bulan) {
            if ($periode === 'bulanan') {
                $query->whereYear($column, $tahun)->whereMonth($column, $bulan);
            } elseif ($periode === 'tahunan') {
                $query->whereYear($column, $tahun);
            }

            return $query;
        };

        // Query penerimaan terfilter
        $queryPenerimaan = $this->queryPenerimaanLaporan();
        $applyDateFilter($queryPenerimaan, 'tanggal');

        // Query pengeluaran operasional terfilter
        $queryPengeluaran = $this->queryPengeluaranOperasional();
        $applyDateFilter($queryPengeluaran, 'tanggal');

        // Query penggajian terfilter
        $queryPenggajian = Penggajian::query()
            ->where('status_penggajian', 'sudah_dibayar')
            ->whereNotNull('tanggal');
        $applyDateFilter($queryPenggajian, 'tanggal');

        // 1. RINCIAN PENERIMAAN PER KELOMPOK / GOLONGAN ZISWAF
        $golonganZiswaf = [
            'zakat_maal' => 'Zakat Maal',
            'zakat_penghasilan' => 'Zakat Penghasilan',
            'infaq' => 'Infak',
            'wakaf' => 'Wakaf',
            'parkir' => 'Parkir',
        ];

        $detailPemasukan = [];
        $totalPemasukan = 0;

        foreach ($golonganZiswaf as $key => $label) {
            $q = (clone $queryPenerimaan)->where(function ($query) use ($key) {
                if ($key === 'parkir') {
                    $query->whereIn('jenis_ziswaf', ['parkir', 'hasil_parkir']);
                } else {
                    $query->where('jenis_ziswaf', $key);
                }
            });
            $count = (clone $q)->count();
            $nominal = (int) (clone $q)->sum('nominal');
            $items = (clone $q)->with('muzakki')->orderByDesc('tanggal')->get();

            $detailPemasukan[] = (object) [
                'kode' => $key,
                'label' => $label,
                'transaksi' => $count,
                'nominal' => $nominal,
                'items' => $items,
            ];

            $totalPemasukan += $nominal;
        }

        // Cek jika ada jenis_ziswaf lain di luar list standar
        $otherPenerimaan = (clone $queryPenerimaan)
            ->whereNotIn('jenis_ziswaf', array_keys($golonganZiswaf))
            ->with('muzakki')
            ->orderByDesc('tanggal')
            ->get();
        if ($otherPenerimaan->isNotEmpty()) {
            $otherNominal = (int) $otherPenerimaan->sum('nominal');
            $detailPemasukan[] = (object) [
                'kode' => 'lainnya_khusus',
                'label' => 'Penerimaan Lainnya',
                'transaksi' => $otherPenerimaan->count(),
                'nominal' => $otherNominal,
                'items' => $otherPenerimaan,
            ];
            $totalPemasukan += $otherNominal;
        }

        // Rincian arus kas hanya memuat akun yang dapat dipakai untuk
        // pengeluaran manual dan akun gaji. Akun alokasi antar-dana tidak
        // dicampurkan dengan pengeluaran kas.
        $kodePengeluaran = collect(config('coa.manual_expense_accounts', []))
            ->flatMap(fn (array $accounts) => array_keys($accounts))
            ->push('5104')
            ->unique()
            ->values();

        $coaBebanMaster = Coa::whereIn('kode_akun', $kodePengeluaran)
            ->orderBy('kode_akun')
            ->get();

        // Ambil data pengeluaran operasional per kategori
        $pengeluaranItems = (clone $queryPengeluaran)
            ->with(['coaDebit', 'coaKredit'])
            ->orderByDesc('tanggal')
            ->get();

        $pengeluaranByCoa = $pengeluaranItems
            ->filter(fn ($item) => ! empty($item->coa_debit_id))
            ->groupBy('coa_debit_id');
        $pengeluaranByName = $pengeluaranItems
            ->filter(fn ($item) => empty($item->coa_debit_id))
            ->groupBy(fn ($item) => $item->kategori ?: 'Beban Operasional Lain-lain');

        $gajiItems = (clone $queryPenggajian)->with('pegawai')->orderByDesc('tanggal')->get();
        $totalGaji = (int) $gajiItems->sum('total_gaji');
        $totalPengeluaran = $totalGaji;
        $detailPengeluaran = [];

        foreach ($coaBebanMaster as $coa) {
            $namaAkun = $coa->nama_akun;
            $kodeAkun = $coa->kode_akun;

            if ($kodeAkun === '5104' || str_contains(strtolower($namaAkun), 'honorarium')) {
                // Beban gaji dan honorarium bersumber dari Penggajian.
                $count = $gajiItems->count();
                $nominal = $totalGaji;
                $items = $gajiItems;
            } else {
                $items = $pengeluaranByCoa->get($coa->id, collect())
                    ->concat($pengeluaranByName->get($namaAkun, collect()));
                $count = $items->count();
                $nominal = (int) $items->sum(function ($item) {
                    return (int) ($item->nominal ?: $item->jumlah);
                });
                $totalPengeluaran += $nominal;
            }

            $detailPengeluaran[] = (object) [
                'kode_akun' => $kodeAkun,
                'nama_akun' => $namaAkun,
                'transaksi' => $count,
                'nominal' => $nominal,
                'items' => $items,
            ];
        }

        // Hitung saldo
        $saldo = $totalPemasukan - $totalPengeluaran;

        // Pilihan daftar tahun untuk filter
        $tahunSekarang = now()->year;
        $tahunAwal = ZiswafPenerimaan::min(DB::raw('YEAR(tanggal)')) ?: ($tahunSekarang - 2);
        $daftarTahun = range(max($tahunAwal, $tahunSekarang - 5), $tahunSekarang);
        rsort($daftarTahun);

        $daftarBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return view('admin.laporan.arus-kas', compact(
            'periode',
            'tahun',
            'bulan',
            'daftarTahun',
            'daftarBulan',
            'detailPemasukan',
            'detailPengeluaran',
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
                'keterangan' => 'Penerimaan '.$jenisZiswaf.' dari '.$namaJamaah,
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
                'keterangan' => 'Gaji '.$namaPegawai.' periode '.$item->periode,
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
            'zakat_penghasilan' => 'Zakat Penghasilan',
            'zakat_fitrah' => 'Zakat Fitrah',
            'infaq', 'shadaqah' => 'Infak',
            'wakaf' => 'Wakaf',
            'fidyah' => 'Fidyah',
            'parkir', 'hasil_parkir' => 'Parkir',
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
