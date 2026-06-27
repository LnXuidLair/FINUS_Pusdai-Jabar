<?php

namespace App\Http\Controllers;

use App\Models\GajiJabatan;
use App\Models\Pegawai;
use Illuminate\Http\Request;

class GajiJabatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $gajiJabatans = GajiJabatan::orderBy('jabatan')->get();
        return view('gaji_jabatan.index', compact('gajiJabatans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('gaji_jabatan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jabatan' => 'required|string|max:255|unique:gaji_jabatan,jabatan',
            'gaji_perhari' => 'required|integer|min:0',
        ]);

        GajiJabatan::create($request->all());

        return redirect()->route('admin.gaji-jabatan.index')
            ->with('success', 'Data gaji jabatan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $gajiJabatan = GajiJabatan::findOrFail($id);
        return view('gaji_jabatan.edit', compact('gajiJabatan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'jabatan' => 'required|string|max:255',
            'gaji_perhari' => 'required|numeric|min:0',
        ]);

        $gajiJabatan = GajiJabatan::findOrFail($id);

        // SIMPAN JABATAN LAMA
        $jabatanLama = $gajiJabatan->jabatan;

        // UPDATE GAJI_JABATAN
        $gajiJabatan->update([
            'jabatan' => $request->jabatan,
            'gaji_perhari' => $request->gaji_perhari,
        ]);

        // SINKRON KE PEGAWAI
        Pegawai::where('jabatan', $jabatanLama)
            ->update(['jabatan' => $request->jabatan]);

        return redirect()->route('admin.gaji-jabatan.index')
            ->with('success', 'Data gaji jabatan berhasil diperbarui.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $gajiJabatan = GajiJabatan::findOrFail($id);
        
        // Check if there are any pegawai associated with this jabatan
        if ($gajiJabatan->pegawais()->count() > 0) {
            return redirect()->route('admin.gaji-jabatan.index')
                ->with('error', 'Tidak dapat menghapus jabatan ini karena masih ada pegawai yang terkait.');
        }

        $gajiJabatan->delete();

        return redirect()->route('admin.gaji-jabatan.index')
            ->with('success', 'Data gaji jabatan berhasil dihapus.');
    }
}
