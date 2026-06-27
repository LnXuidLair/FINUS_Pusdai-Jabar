<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Http\Requests\StoreCoaRequest;
use App\Http\Requests\UpdateCoaRequest;
use Illuminate\Http\Request;

class CoaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function tabel()
    {
        // Ambil semua data COA tanpa filter 'header_akun'
        $coa = Coa::all();  // Mengambil semua data COA
        return view('coa.index', [
            'coa' => $coa,
            'title' => 'Contoh M2',
            'nama' => 'Farel Prayoga'
        ]);
    }

    public function index()
    {
        // Ambil data COA
        $coa = Coa::all(); // Mengambil semua data COA tanpa filter perusahaan
        
        // Debug: Dump and die to check the data
        // dd($coa);
        
        return view('coa.index', [
            'coa' => $coa,
            'title' => 'Daftar COA',
            'nama' => 'Admin'
        ]);
    }

    /**
     * Fetch all COA data for Ajax requests.
     */
    public function fetchAll()
    {
        $coas = Coa::all(); // Ambil semua data COA
        if ($coas->isEmpty()) {
            return '<h1 class="text-center text-secondary my-5">No record present in the database!</h1>';
        }

        $output = view('coa.partials.coa_table', ['coas' => $coas])->render();
        return response()->json(['html' => $output]);
    }

    /**
     * Fetch COA data in JSON format for API or Ajax requests.
     */
    public function fetchCoa()
    {
        $coa = Coa::all(); // Ambil semua data COA
        return response()->json(['coa' => $coa]);
    }

    /**
     * Display a specific COA record for API view.
     */
    public function view($id)
    {
        $coa = Coa::findOrFail($id);
        return response()->json($coa);
    }

    /**
     * Show the form for creating a new COA.
     */
    public function create()
    {
        return view('coa.create');
    }

    /**
     * Store a newly created COA in storage.
     */
    public function store(StoreCoaRequest $request)
    {
        // Validasi tambahan di StoreCoaRequest
        $validated = $request->validated();
        if (!isset($validated['kode_akun']) || empty($validated['kode_akun'])) {
            return redirect()->route('admin.coa.create')->with('error', 'Kode Akun harus diisi.');
        }

        $coa = Coa::create($validated);

        return redirect()->route('admin.coa.index')->with('success', 'COA berhasil disimpan.');
    }

    /**
     * Show the form for editing the specified COA.
     */
    public function edit($id)
    {
        $coa = Coa::findOrFail($id);
        return view('coa.edit', compact('coa'));
    }

    /**
     * Update the specified COA in storage.
     */
    public function update(UpdateCoaRequest $request, $id)
{
    \Log::info('Data yang dikirim:', $request->all()); // Debug request

    $coa = Coa::findOrFail($id);

    \Log::info('Data sebelum update:', $coa->toArray()); // Debug data sebelum update

    $validated = $request->validated();
    $coa->update($validated);

    \Log::info('Data setelah update:', $coa->fresh()->toArray()); // Debug data setelah update

    return redirect()->route('admin.coa.index')->with('success', 'Data COA berhasil diperbarui.');
}

    /**
     * Remove the specified COA from storage.
     */
    public function destroy($id)
    {
        $coa = Coa::findOrFail($id);
        $coa->delete();

        return redirect()->route('admin.coa.index')->with('success', 'COA berhasil dihapus.');
    }
}
