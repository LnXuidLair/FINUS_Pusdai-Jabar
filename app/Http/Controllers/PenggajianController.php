<?php

namespace App\Http\Controllers;

use App\Models\Penggajian;
use App\Models\Pegawai;
use App\Models\Presensi;
use App\Models\GajiJabatan;
use Illuminate\Http\Request;

class PenggajianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $penggajians = Penggajian::with('pegawai')->orderBy('periode', 'desc')->get();
        return view('penggajian.index', compact('penggajians'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pegawais = Pegawai::with('gajiJabatan')->orderBy('nama_pegawai')->get();
        return view('penggajian.create', compact('pegawais'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_pegawai' => 'required|exists:pegawai,id',
            'periode' => 'required|date_format:Y-m',
            'status_penggajian' => 'required|string|max:255',
        ]);

        $pegawai = Pegawai::findOrFail($request->id_pegawai);
        
        // Check if payroll already exists for this employee and period
        $existingPenggajian = Penggajian::where('id_pegawai', $request->id_pegawai)
            ->where('periode', $request->periode)
            ->first();

        if ($existingPenggajian) {
            return redirect()->back()
                ->with('error', 'Penggajian untuk pegawai ini pada periode tersebut sudah ada.')
                ->withInput();
        }

        // Get gaji_perhari from gaji_jabatan based on employee's jabatan
        $gajiJabatan = GajiJabatan::where('jabatan', $pegawai->jabatan)->first();
        
        if (!$gajiJabatan) {
            return redirect()->back()
                ->with('error', 'Jabatan pegawai tidak ditemukan di tabel gaji jabatan.')
                ->withInput();
        }

        $gajiPerhari = $gajiJabatan->gaji_perhari;

        // Calculate total attendance with status = 'hadir' for the period
        $jumlahKehadiran = Presensi::where('id_pegawai', $request->id_pegawai)
            ->whereMonth('tanggal', date('m', strtotime($request->periode)))
            ->whereYear('tanggal', date('Y', strtotime($request->periode)))
            ->where('status', 'hadir')
            ->whereNotNull('approved_at') // WAJIB
            ->count();

        // Calculate total salary: total_gaji = jumlah_hadir × gaji_perhari
        $totalGaji = $jumlahKehadiran * $gajiPerhari;

        // Calculate working days in the period
        $year = date('Y', strtotime($request->periode));
        $month = date('m', strtotime($request->periode));
        $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        // Create payroll record
        Penggajian::create([
            'id_pegawai' => $request->id_pegawai,
            'periode' => $request->periode,
            'jumlah_hari' => $jumlahHari,
            'gaji_perhari' => $gajiPerhari,
            'total_gaji' => $totalGaji,
            'status_penggajian' => $request->status_penggajian,
            'tanggal' => now()->toDateString(),
            'jumlah_kehadiran' => $jumlahKehadiran,
        ]);

        return redirect()->route('admin.penggajian.index')
            ->with('success', 'Data penggajian berhasil ditambahkan. Total kehadiran: ' . $jumlahKehadiran . ' hari, Total gaji: Rp ' . number_format($totalGaji, 0, ',', '.'));
    }
}
