<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PembayaranSiswa;
use App\Models\Pengeluaran;
use App\Models\TagihanSiswa;
use App\Models\Penggajian;
use App\Models\JurnalDetail;
use App\Models\Coa;

class LaporanController extends Controller
{
    public function jurnalUmum()
    {
        // Debug: Check if method is called
        \Log::info('jurnalUmum method called');
        
        $pembayaran = PembayaranSiswa::with(['siswa'])
            ->orderBy('tanggal_bayar', 'desc')
            ->get();

        $pengeluaran = Pengeluaran::orderBy('tanggal', 'desc')
            ->get();

        $penggajian = Penggajian::with(['pegawai'])
            ->orderBy('tanggal', 'desc')
            ->get();

        // Create merged jurnals collection
        $jurnals = collect();

        // Add pembayaran as jurnal entries (pemasukan - kredit)
        foreach ($pembayaran as $bayar) {
            // Add debit entry (Kas/Bank)
            $jurnals->push((object)[
                'tanggal' => \Carbon\Carbon::parse($bayar->tanggal_bayar),
                'tipe' => 'debit',
                'jumlah' => $bayar->jumlah,
                'akun' => 'Kas',
                'keterangan' => 'Penerimaan pembayaran ' . ($bayar->siswa ? $bayar->siswa->nama_siswa : 'Siswa'),
                'referensi' => $bayar->no_pembayaran ?? 'PB-' . $bayar->id,
                'pembayaran' => $bayar,
                'pengeluaran' => null,
                'penggajian' => null,
            ]);
            
            // Add credit entry (Pendapatan)
            $jurnals->push((object)[
                'tanggal' => \Carbon\Carbon::parse($bayar->tanggal_bayar),
                'tipe' => 'kredit',
                'jumlah' => $bayar->jumlah,
                'akun' => 'Pendapatan SPP',
                'keterangan' => 'Pendapatan dari ' . ($bayar->siswa ? $bayar->siswa->nama_siswa : 'Siswa'),
                'referensi' => $bayar->no_pembayaran ?? 'PB-' . $bayar->id,
                'pembayaran' => $bayar,
                'pengeluaran' => null,
                'penggajian' => null,
            ]);
        }

        // Add pengeluaran as jurnal entries (pengeluaran - debit)
        foreach ($pengeluaran as $keluar) {
            // Add debit entry (Beban/Pengeluaran)
            $jurnals->push((object)[
                'tanggal' => \Carbon\Carbon::parse($keluar->tanggal),
                'tipe' => 'debit',
                'jumlah' => $keluar->jumlah,
                'akun' => 'Beban Operasional',
                'keterangan' => $keluar->keterangan,
                'referensi' => 'PGL-' . $keluar->id,
                'pembayaran' => null,
                'pengeluaran' => $keluar,
                'penggajian' => null,
            ]);
            
            // Add credit entry (Kas/Bank)
            $jurnals->push((object)[
                'tanggal' => \Carbon\Carbon::parse($keluar->tanggal),
                'tipe' => 'kredit',
                'jumlah' => $keluar->jumlah,
                'akun' => 'Kas',
                'keterangan' => 'Pengeluaran: ' . $keluar->keterangan,
                'referensi' => 'PGL-' . $keluar->id,
                'pembayaran' => null,
                'pengeluaran' => $keluar,
                'penggajian' => null,
            ]);
        }

        // Add penggajian as jurnal entries (pengeluaran - debit)
        foreach ($penggajian as $gaji) {
            // Add debit entry (Beban Gaji)
            $jurnals->push((object)[
                'tanggal' => \Carbon\Carbon::parse($gaji->tanggal),
                'tipe' => 'debit',
                'jumlah' => $gaji->total_gaji,
                'akun' => 'Beban Gaji',
                'keterangan' => 'Gaji karyawan: ' . $gaji->pegawai->nama_pegawai,
                'referensi' => 'GAJI-' . $gaji->id,
                'pembayaran' => null,
                'pengeluaran' => null,
                'penggajian' => $gaji,
            ]);
            
            // Add credit entry (Kas/Bank)
            $jurnals->push((object)[
                'tanggal' => \Carbon\Carbon::parse($gaji->tanggal),
                'tipe' => 'kredit',
                'jumlah' => $gaji->total_gaji,
                'akun' => 'Kas',
                'keterangan' => 'Pembayaran gaji: ' . $gaji->pegawai->nama_pegawai,
                'referensi' => 'GAJI-' . $gaji->id,
                'pembayaran' => null,
                'pengeluaran' => null,
                'penggajian' => $gaji,
            ]);
        }

        // Sort by date descending
        $jurnals = $jurnals->sortByDesc('tanggal')->values();

        // Debug: Check if we have data
        \Log::info('Total pembayaran: ' . $pembayaran->count());
        \Log::info('Total pengeluaran: ' . $pengeluaran->count());
        \Log::info('Total penggajian: ' . $penggajian->count());
        \Log::info('Total jurnals: ' . $jurnals->count());

        // Debug: Check data before returning
        \Log::info('Total jurnals to return: ' . $jurnals->count());
        \Log::info('Returning view: admin.laporan.jurnal-umum');
        
        return view('admin.laporan.jurnal-umum', compact('jurnals'));
    }

    public function arusKas()
    {
        $pemasukanBulanan = PembayaranSiswa::selectRaw('MONTH(tanggal_bayar) as bulan, YEAR(tanggal_bayar) as tahun, SUM(jumlah) as total')
            ->where('status_pembayaran', 'lunas')
            ->groupBy('bulan', 'tahun')
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        $pengeluaranBulanan = Pengeluaran::selectRaw('MONTH(tanggal) as bulan, YEAR(tanggal) as tahun, SUM(jumlah) as total')
            ->groupBy('bulan', 'tahun')
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        $penggajianBulanan = Penggajian::selectRaw('MONTH(tanggal) as bulan, YEAR(tanggal) as tahun, SUM(total_gaji) as total')
            ->where('status_penggajian', 'sudah_dibayar')
            ->groupBy('bulan', 'tahun')
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        $totalPemasukan = PembayaranSiswa::where('status_pembayaran', 'lunas')->sum('jumlah');
        $totalPengeluaran = Pengeluaran::sum('jumlah');
        $saldo = $totalPemasukan - $totalPengeluaran;

        return view('admin.laporan.arus-kas', compact('pemasukanBulanan', 'pengeluaranBulanan', 'penggajianBulanan', 'totalPemasukan', 'totalPengeluaran', 'saldo'));
    }

    /**
     * Laporan Arus Kas sesuai PSAK 2/2009 (Ikatan Akuntansi Indonesia)
     * Menghitung arus kas dari data jurnal umum dengan klasifikasi:
     * 1. Arus Kas dari Aktivitas Operasi
     * 2. Arus Kas dari Aktivitas Investasi  
     * 3. Arus Kas dari Aktivitas Pendanaan
     */
    public function arusKasDariJurnal()
    {
        // Ambil data jurnal yang sudah ada
        $pembayaran = PembayaranSiswa::with(['siswa'])
            ->where('status_pembayaran', 'lunas')
            ->orderBy('tanggal_bayar', 'desc')
            ->get();

        $pengeluaran = Pengeluaran::orderBy('tanggal', 'desc')->get();
        
        $penggajian = Penggajian::with(['pegawai'])
            ->where('status_penggajian', 'sudah_dibayar')
            ->orderBy('tanggal', 'desc')
            ->get();

        // Struktur arus kas sesuai PSAK 2/2009
        $arusKas = [
            'operasi' => [
                'masuk' => [
                    'pendapatan_spp' => 0,
                    'pendapatan_lain' => 0,
                    'penjualan_barang' => 0,
                    'piutang_diterima' => 0,
                    'bunga_diterima' => 0,
                    'dividen_diterima' => 0,
                    'total' => 0
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
                    'total' => 0
                ],
                'bersih' => 0
            ],
            'investasi' => [
                'masuk' => [
                    'penjualan_aset_tetap' => 0,
                    'penjualan_investasi' => 0,
                    'pengembalian_pinjaman' => 0,
                    'total' => 0
                ],
                'keluar' => [
                    'pembelian_aset_tetap' => 0,
                    'pembelian_investasi' => 0,
                    'pemberian_pinjaman' => 0,
                    'total' => 0
                ],
                'bersih' => 0
            ],
            'pendanaan' => [
                'masuk' => [
                    'penambahan_modal' => 0,
                    'pinjaman_diterima' => 0,
                    'penerbitan_saham' => 0,
                    'total' => 0
                ],
                'keluar' => [
                    'pembayaran_dividen' => 0,
                    'pengembalian_modal' => 0,
                    'pelunasan_pinjaman' => 0,
                    'pembelian_saham_treasury' => 0,
                    'total' => 0
                ],
                'bersih' => 0
            ],
            'detail_transaksi' => []
        ];

        // Proses Pembayaran Siswa (Arus Kas Operasi - Masuk)
        foreach ($pembayaran as $bayar) {
            $keterangan = 'Pembayaran SPP ' . ($bayar->siswa ? $bayar->siswa->nama_siswa : 'Siswa');
            
            $arusKas['operasi']['masuk']['pendapatan_spp'] += $bayar->jumlah;
            $arusKas['operasi']['masuk']['total'] += $bayar->jumlah;
            
            $arusKas['detail_transaksi'][] = [
                'tanggal' => $bayar->tanggal_bayar,
                'keterangan' => $keterangan,
                'kategori' => 'operasi',
                'jenis' => 'masuk',
                'sub_kategori' => 'pendapatan_spp',
                'jumlah' => $bayar->jumlah
            ];
        }

        // Proses Pengeluaran (diklasifikasikan sesuai jenis)
        foreach ($pengeluaran as $keluar) {
            $kategori = $this->klasifikasikanPengeluaranPSAK($keluar->keterangan);
            $subKategori = $this->getSubKategoriPengeluaran($keluar->keterangan, $kategori);
            
            if ($kategori && $subKategori) {
                $arusKas[$kategori]['keluar'][$subKategori] += $keluar->jumlah;
                $arusKas[$kategori]['keluar']['total'] += $keluar->jumlah;
                
                $arusKas['detail_transaksi'][] = [
                    'tanggal' => $keluar->tanggal,
                    'keterangan' => $keluar->keterangan,
                    'kategori' => $kategori,
                    'jenis' => 'keluar',
                    'sub_kategori' => $subKategori,
                    'jumlah' => $keluar->jumlah
                ];
            }
        }

        // Proses Penggajian (Arus Kas Operasi - Keluar)
        foreach ($penggajian as $gaji) {
            $keterangan = 'Gaji ' . ($gaji->pegawai ? $gaji->pegawai->nama_pegawai : 'Karyawan');
            
            $arusKas['operasi']['keluar']['beban_gaji'] += $gaji->total_gaji;
            $arusKas['operasi']['keluar']['total'] += $gaji->total_gaji;
            
            $arusKas['detail_transaksi'][] = [
                'tanggal' => $gaji->tanggal,
                'keterangan' => $keterangan,
                'kategori' => 'operasi',
                'jenis' => 'keluar',
                'sub_kategori' => 'beban_gaji',
                'jumlah' => $gaji->total_gaji
            ];
        }

        // Hitung arus kas bersih untuk setiap kategori
        foreach (['operasi', 'investasi', 'pendanaan'] as $kategori) {
            $arusKas[$kategori]['bersih'] = $arusKas[$kategori]['masuk']['total'] - $arusKas[$kategori]['keluar']['total'];
        }

        // Total arus kas bersih
        $totalBersih = $arusKas['operasi']['bersih'] + $arusKas['investasi']['bersih'] + $arusKas['pendanaan']['bersih'];

        // Sort detail transaksi by date
        usort($arusKas['detail_transaksi'], function($a, $b) {
            return strtotime($b['tanggal']) - strtotime($a['tanggal']);
        });

        return view('admin.laporan.arus-kas-psak', compact('arusKas', 'totalBersih'));
    }

    /**
     * Klasifikasikan pengeluaran sesuai PSAK 2/2009
     */
    private function klasifikasikanPengeluaranPSAK($keterangan)
    {
        $keterangan = strtolower($keterangan);
        
        // Investasi - pembelian aset jangka panjang
        if (strpos($keterangan, 'aset') !== false || 
            strpos($keterangan, 'gedung') !== false ||
            strpos($keterangan, 'kendaraan') !== false ||
            strpos($keterangan, 'mesin') !== false ||
            strpos($keterangan, 'investasi') !== false ||
            strpos($keterangan, 'tanah') !== false) {
            return 'investasi';
        }
        
        // Pendanaan - pinjaman, modal, hutang, dividen
        if (strpos($keterangan, 'pinjaman') !== false || 
            strpos($keterangan, 'hutang') !== false ||
            strpos($keterangan, 'modal') !== false ||
            strpos($keterangan, 'dividen') !== false ||
            strpos($keterangan, 'saham') !== false) {
            return 'pendanaan';
        }
        
        // Default ke operasi
        return 'operasi';
    }

    /**
     * Mendapatkan sub kategori pengeluaran
     */
    private function getSubKategoriPengeluaran($keterangan, $kategori)
    {
        $keterangan = strtolower($keterangan);
        
        if ($kategori === 'investasi') {
            if (strpos($keterangan, 'aset') !== false || strpos($keterangan, 'gedung') !== false || 
                strpos($keterangan, 'kendaraan') !== false || strpos($keterangan, 'mesin') !== false) {
                return 'pembelian_aset_tetap';
            }
            if (strpos($keterangan, 'investasi') !== false) {
                return 'pembelian_investasi';
            }
            if (strpos($keterangan, 'pinjaman') !== false) {
                return 'pemberian_pinjaman';
            }
        }
        
        if ($kategori === 'pendanaan') {
            if (strpos($keterangan, 'dividen') !== false) {
                return 'pembayaran_dividen';
            }
            if (strpos($keterangan, 'hutang') !== false || strpos($keterangan, 'pinjaman') !== false) {
                return 'pelunasan_pinjaman';
            }
            if (strpos($keterangan, 'modal') !== false) {
                return 'pengembalian_modal';
            }
        }
        
        if ($kategori === 'operasi') {
            if (strpos($keterangan, 'gaji') !== false || strpos($keterangan, 'upah') !== false) {
                return 'beban_gaji';
            }
            if (strpos($keterangan, 'sewa') !== false) {
                return 'beban_sewa';
            }
            if (strpos($keterangan, 'listrik') !== false || strpos($keterangan, 'pln') !== false) {
                return 'beban_listrik';
            }
            if (strpos($keterangan, 'telepon') !== false || strpos($keterangan, 'pulsa') !== false) {
                return 'beban_telepon';
            }
            if (strpos($keterangan, 'marketing') !== false || strpos($keterangan, 'iklan') !== false) {
                return 'beban_marketing';
            }
            if (strpos($keterangan, 'administrasi') !== false || strpos($keterangan, 'admin') !== false) {
                return 'beban_administrasi';
            }
            if (strpos($keterangan, 'atk') !== false || strpos($keterangan, 'operasional') !== false) {
                return 'beban_operasional_lain';
            }
        }
        
        return 'beban_operasional_lain'; // Default
    }

    /**
     * Klasifikasikan pengeluaran ke dalam kategori arus kas
     */
    private function klasifikasikanPengeluaran($keterangan)
    {
        $keterangan = strtolower($keterangan);
        
        // Investasi - pembelian aset jangka panjang
        if (strpos($keterangan, 'aset') !== false || 
            strpos($keterangan, 'investasi') !== false ||
            strpos($keterangan, 'gedung') !== false ||
            strpos($keterangan, 'kendaraan') !== false ||
            strpos($keterangan, 'mesin') !== false) {
            return 'investasi';
        }
        
        // Pendanaan - pinjaman, modal, hutang
        if (strpos($keterangan, 'pinjaman') !== false || 
            strpos($keterangan, 'hutang') !== false ||
            strpos($keterangan, 'modal') !== false ||
            strpos($keterangan, 'dividen') !== false) {
            return 'pendanaan';
        }
        
        // Default ke operasi
        return 'operasi';
    }
}
