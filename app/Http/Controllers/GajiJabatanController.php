<?php

namespace App\Http\Controllers;

use App\Models\GajiJabatan;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        $validated = $request->validate(
            [
                'jabatan' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:gaji_jabatan,jabatan',
                ],
                'gaji_perhari' => [
                    'required',
                    'integer',
                    'min:0',
                ],
            ],
            [
                'jabatan.required' => 'Nama jabatan wajib diisi.',
                'jabatan.string' => 'Nama jabatan harus berupa teks.',
                'jabatan.unique' => 'Jabatan tersebut sudah tersedia.',

                'gaji_perhari.required' => 'Gaji per hari wajib diisi.',
                'gaji_perhari.integer' => 'Gaji per hari harus berupa angka bulat.',
                'gaji_perhari.min' => 'Gaji per hari tidak boleh kurang dari Rp0.',
            ]
        );

        GajiJabatan::create($validated);

        return redirect()
            ->route('admin.gaji-jabatan.index')
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
        $gajiJabatan = GajiJabatan::findOrFail($id);

        $validated = $request->validate(
            [
                'jabatan' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('gaji_jabatan', 'jabatan')
                        ->ignore($gajiJabatan->id),
                ],
                'gaji_perhari' => [
                    'required',
                    'integer',
                    'min:0',
                ],
            ],
            [
                'jabatan.required' => 'Nama jabatan wajib diisi.',
                'jabatan.string' => 'Nama jabatan harus berupa teks.',
                'jabatan.unique' => 'Jabatan tersebut sudah tersedia.',

                'gaji_perhari.required' => 'Gaji per hari wajib diisi.',
                'gaji_perhari.integer' => 'Gaji per hari harus berupa angka bulat.',
                'gaji_perhari.min' => 'Gaji per hari tidak boleh kurang dari Rp0.',
            ]
        );

        $jabatanLama = $gajiJabatan->jabatan;

        $gajiJabatan->update($validated);

        Pegawai::where('jabatan', $jabatanLama)
            ->update([
                'jabatan' => $validated['jabatan'],
            ]);

        return redirect()
            ->route('admin.gaji-jabatan.index')
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
