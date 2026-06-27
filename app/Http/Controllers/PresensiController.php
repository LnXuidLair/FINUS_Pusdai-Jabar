<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PresensiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $presensis = Presensi::with('pegawai')->orderBy('tanggal', 'desc')->get();
        return view('presensi.index', compact('presensis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pegawais = Pegawai::orderBy('nama_pegawai')->get();
        return view('presensi.create', compact('pegawais'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /* ===== CEK JAM ADMIN ===== */
        $now = now()->format('H:i');
        $jamAdmin = config('presensi.jam_admin');

        if ($now < $jamAdmin['start'] || $now > $jamAdmin['end']) {
            return back()->withErrors('Admin hanya dapat input presensi di jam kerja');
        }
        
        $request->validate([
            'id_pegawai' => 'required|exists:pegawai,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:hadir,izin,lembur,tidak hadir',
            'keterangan' => 'nullable|required_if:status,izin,lembur'
        ]);

        Presensi::updateOrCreate(
            [
                'id_pegawai' => $request->id_pegawai,
                'tanggal' => $request->tanggal,
                'status' => $request->status,
            ],
            [
                'keterangan' => $request->keterangan,
                'is_approved' => true,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]
        );

        return redirect()->route('admin.presensi.index')
            ->with('success', 'Presensi berhasil disimpan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $presensi = Presensi::findOrFail($id);
        $pegawais = Pegawai::orderBy('nama_pegawai')->get();
        return view('presensi.edit', compact('presensi', 'pegawais'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $presensi = Presensi::findOrFail($id);

        $presensi->update([
            'is_approved' => true,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('admin.presensi.index')
            ->with('success', 'Presensi diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $presensi = Presensi::findOrFail($id);

        if ($presensi->bukti_kehadiran){
            Storage::disk('public')->delete($presensi->bukti_kehadiran);
        }
        
        // Check if there are any related penggajian records
        if ($presensi->pegawai->penggajians()->count() > 0) {
            return redirect()->route('admin.presensi.index')
                ->with('error', 'Tidak dapat menghapus presensi ini karena sudah digunakan dalam perhitungan penggajian.');
        }

        $presensi->delete();

        return redirect()->route('admin.presensi.index')
            ->with('success', 'Data presensi berhasil dihapus.');
    }
     public function pegawaiIndex()
    {
        $pegawai = Pegawai::where('email', auth()->user()->email)->firstOrFail();
        $pegawaiId = $pegawai->id;

        $presensis = Presensi::where('id_pegawai', $pegawaiId)
            ->latest('tanggal')
            ->get();

        return view('pegawai.presensi.index', compact('presensis'));
    }
    public function pegawaiCreate()
    {
        return view('pegawai.presensi.create');
    }

    public function pegawaiStore(Request $request)
    {
        $pegawai = auth()->user()->pegawai;

        abort_unless($pegawai, 404, 'Data pegawai belum terhubung dengan akun ini.');

        $today = now()->toDateString();
        $now   = now()->format('H:i');

        /* ===== BATAS 3 KALI ===== */
        $count = Presensi::where('id_pegawai', $pegawai->id)
            ->whereDate('tanggal',$today)
            ->count();

        if ($count >= 3) {
            return back()->withErrors('Presensi hari ini sudah lengkap');
        }

        /* ===== CEK JAM KERJA (KECUALI LEMBUR) ===== */
        $allowed = false;
        foreach (config('presensi') as $key => $jam) {
            if ($key === 'jam_admin') continue;
            if ($now >= $jam['start'] && $now <= $jam['end']) {
                $allowed = true;
            }
        }

        if (!$allowed && $request->status !== 'lembur') {
            return back()->withErrors('Presensi di luar jam yang diizinkan');
        }

        $request->validate([
            'status' => 'required|in:hadir,izin,lembur',
            'keterangan' => 'nullable|required_if:status,izin,lembur',
            'bukti_kehadiran' => 'required|image|mimes:jpeg,jpg,png|max:1024',
        ]);

        /* ===== NAMA FILE OTOMATIS ===== */
        $tanggal = now()->format('dmY');
        $nama    = str_replace(' ', '_', $pegawai->nama_pegawai);
        $jamKe   = $count + 1;

        $filename = "Presensi{$tanggal}_{$nama}_{$request->status}{$jamKe}."
            .$request->file('bukti_kehadiran')->extension();

        $path = $request->file('bukti_kehadiran')
            ->storeAs('bukti_kehadiran', $filename, 'public');

        Presensi::create([
            'id_pegawai' => $pegawai->id,
            'tanggal' => $today,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
            'bukti_kehadiran' => $path,
            'is_approved' => false,
        ]);

        return redirect()->route('pegawai.presensi.index')
            ->with('success','Presensi berhasil dikirim');
    }
}
