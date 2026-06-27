<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\Penggajian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PengeluaranController extends Controller
{
    public function index()
    {
        $pengeluaranManual = Pengeluaran::whereNull('id_penggajian')
            ->orderBy('tanggal', 'desc')
            ->get();
            
        $pengeluaranGaji = Penggajian::where('status_penggajian', 'sudah_dibayar')
            ->with('pegawai')
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(function ($gaji) {
                return (object) [
                    'id' => 'gaji_' . $gaji->id,
                    'kategori' => 'penggajian',
                    'deskripsi' => 'Gaji ' . $gaji->pegawai->nama_pegawai,
                    'jumlah' => $gaji->total_gaji,
                    'tanggal' => $gaji->tanggal,
                    'bukti_pembayaran' => $gaji->bukti_pembayaran,
                    'created_at' => $gaji->created_at,
                    'updated_at' => $gaji->updated_at
                ];
            });

        $allPengeluaran = $pengeluaranManual->concat($pengeluaranGaji)
            ->sortByDesc('tanggal')
            ->map(function($item) {
                // Add is_gaji flag and default status for the view
                $isGaji = isset($item->kategori) && $item->kategori === 'penggajian';
                $item->is_gaji = $isGaji;
                
                // Set default status_verifikasi if not exists
                if (!isset($item->status_verifikasi)) {
                    $item->status_verifikasi = $isGaji ? 'approved' : 'pending';
                }
                
                // Ensure bukti_pembayaran exists
                if (!isset($item->bukti_pembayaran)) {
                    $item->bukti_pembayaran = null;
                }
                
                return $item;
            });

        return view('pengeluaran.index', compact('allPengeluaran'));
    }

    public function create()
    {
        return view('pengeluaran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'bukti_pembayaran' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048'
        ]);

        $data = $request->only(['kategori', 'deskripsi', 'jumlah', 'tanggal']);
        
        if ($request->hasFile('bukti_pembayaran')) {
            $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');
            $data['bukti_pembayaran'] = $path;
        }

        Pengeluaran::create($data);

        return redirect()->route('admin.pengeluaran.index')
            ->with('success', 'Data pengeluaran berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        if (strpos($id, 'gaji_') === 0) {
            $gajiId = str_replace('gaji_', '', $id);
            $penggajian = Penggajian::findOrFail($gajiId);
            
            if ($penggajian->bukti_pembayaran) {
                Storage::disk('public')->delete($penggajian->bukti_pembayaran);
            }
            
            $penggajian->update(['status_penggajian' => 'belum_dibayar']);
        } else {
            $pengeluaran = Pengeluaran::findOrFail($id);
            
            if ($pengeluaran->bukti_pembayaran) {
                Storage::disk('public')->delete($pengeluaran->bukti_pembayaran);
            }
            
            $pengeluaran->delete();
        }

        return response()->json(['success' => true]);
    }
}
