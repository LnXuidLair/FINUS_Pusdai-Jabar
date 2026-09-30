<?php

namespace App\Http\Controllers;

use App\Models\BarangZakat;
use App\Models\Coa;
use App\Models\HargaBarangZakat;
use App\Models\KetentuanPokokZakat;
use App\Models\MasterAsnaf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KebijakanZakatController extends Controller
{
    public function index(Request $request)
    {
        $editBarang = $request->filled('edit_barang')
            ? BarangZakat::findOrFail($request->integer('edit_barang'))
            : null;

        $editHarga = $request->filled('edit_harga')
            ? HargaBarangZakat::findOrFail($request->integer('edit_harga'))
            : null;

        $editKetentuanPokok = $request->filled('edit_ketentuan')
            ? KetentuanPokokZakat::findOrFail($request->integer('edit_ketentuan'))
            : null;

        return view('admin.kebijakan-zakat.index', [
            'barangZakat' => BarangZakat::with(['coaPersediaan', 'hargaTerbaru'])
                ->orderByDesc('aktif')
                ->orderBy('nama')
                ->get(),
            'hargaBarang' => HargaBarangZakat::with('barang')
                ->latest('berlaku_mulai')
                ->latest('id')
                ->get(),
            'akunPersediaan' => Coa::query()
                ->where('header_akun', 1)
                ->orderBy('kode_akun')
                ->get(),
            'ketentuanPokok' => KetentuanPokokZakat::query()->orderBy('id')->get(),
            'masterAsnaf' => MasterAsnaf::query()->orderBy('urutan')->get(),
            'editKetentuanPokok' => $editKetentuanPokok,
            'editBarang' => $editBarang,
            'editHarga' => $editHarga,
            'activeTab' => $request->query('tab', 'ketentuan_pokok'),
            'kategoriBarangLabels' => BarangZakat::KATEGORI,
            'satuanBarangLabels' => BarangZakat::SATUAN,
            'metodePenilaianLabels' => BarangZakat::METODE_PENILAIAN,
            'statusHargaLabels' => HargaBarangZakat::STATUS,
        ]);
    }

    public function updateKetentuanPokok(Request $request, KetentuanPokokZakat $ketentuan)
    {
        abort_if($ketentuan->terkunci, 403, 'Ketentuan pokok sedang terkunci dan tidak dapat diubah.');

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'kadar_persentase' => ['required', 'numeric', 'min:0', 'max:100'],
            'haul' => ['required', 'string', 'max:30'],
            'nisab_pokok' => ['nullable', 'string', 'max:100'],
            'berat_fitrah_kg' => ['nullable', 'numeric', 'min:0'],
            'berat_fitrah_liter' => ['nullable', 'numeric', 'min:0'],
            'deskripsi' => ['nullable', 'string'],
            'dasar_hukum' => ['nullable', 'string'],
            'dasar_regulasi' => ['nullable', 'string'],
            'aktif' => ['boolean'],
            'persentase_amil' => ['nullable', 'numeric', 'min:0', 'max:12.5'],
            'target_mustahik' => ['nullable', 'array'],
            'target_mustahik.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $amil = (float) ($validated['persentase_amil'] ?? 0);
        $totalMustahik = 0;
        if (isset($validated['target_mustahik'])) {
            foreach ($validated['target_mustahik'] as $key => $val) {
                if ($val === null) {
                    $validated['target_mustahik'][$key] = 0;
                }
                $totalMustahik += (float) $val;
            }
        }

        $totalSum = $amil + $totalMustahik;
        if (abs($totalSum - 100.0) > 0.01) {
            return back()->withInput()->withErrors(['target_mustahik' => 'Total persentase (Amil + Mustahik) harus pas 100%. Saat ini totalnya: '.$totalSum.'%']);
        }

        $validated['aktif'] = $request->boolean('aktif');
        $validated['updated_by'] = $request->user()->id;

        $ketentuan->update($validated);

        return redirect()->route('admin.kebijakan-zakat.index', ['tab' => 'ketentuan_pokok'])->with('success', 'Ketentuan pokok zakat berhasil diperbarui.');
    }

    public function toggleLockKetentuanPokok(Request $request, KetentuanPokokZakat $ketentuan)
    {
        $ketentuan->update([
            'terkunci' => ! $ketentuan->terkunci,
            'updated_by' => $request->user()->id,
        ]);

        $status = $ketentuan->terkunci ? 'dikunci' : 'dibuka kuncinya';

        return back()->with('success', "Ketentuan pokok berhasil {$status}.");
    }

    public function storeBarang(Request $request)
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:30', 'unique:barang_zakat,kode'],
            'nama' => ['required', 'string', 'max:100', 'unique:barang_zakat,nama'],
            'kategori' => ['required', 'string'],
            'satuan_dasar' => ['required', 'string', 'max:20'],
            'metode_penilaian' => ['required', 'string'],
            'coa_persediaan_id' => ['nullable', Rule::exists('coa', 'id')],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'aktif' => ['boolean'],
        ]);

        $validated['aktif'] = $request->boolean('aktif', true);
        $validated['created_by'] = $request->user()->id;

        BarangZakat::create($validated);

        return back()->with('success', 'Barang zakat berhasil ditambahkan.');
    }

    public function updateBarang(Request $request, BarangZakat $barang)
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:30', Rule::unique('barang_zakat')->ignore($barang->id)],
            'nama' => ['required', 'string', 'max:100', Rule::unique('barang_zakat')->ignore($barang->id)],
            'kategori' => ['required', 'string'],
            'satuan_dasar' => ['required', 'string', 'max:20'],
            'metode_penilaian' => ['required', 'string'],
            'coa_persediaan_id' => ['nullable', Rule::exists('coa', 'id')],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'aktif' => ['boolean'],
        ]);

        $validated['aktif'] = $request->boolean('aktif', false);
        $validated['updated_by'] = $request->user()->id;

        $barang->update($validated);

        return redirect()->route('admin.kebijakan-zakat.index')->with('success', 'Barang zakat berhasil diperbarui.');
    }

    public function storeHargaBarang(Request $request)
    {
        $validated = $request->validate([
            'barang_zakat_id' => ['required', Rule::exists('barang_zakat', 'id')],
            'wilayah' => ['required', 'string', 'max:100'],
            'harga_per_satuan' => ['required', 'numeric', 'min:0'],
            'berlaku_mulai' => ['required', 'date'],
            'berlaku_sampai' => ['nullable', 'date', 'after_or_equal:berlaku_mulai'],
            'sumber_harga' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:draft,disetujui'],
        ]);

        $validated['created_by'] = $request->user()->id;
        if ($validated['status'] === 'disetujui') {
            $validated['approved_by'] = $request->user()->id;
            $validated['approved_at'] = now();
        }

        HargaBarangZakat::create($validated);

        return back()->with('success', 'Harga barang berhasil ditambahkan.');
    }

    public function updateHargaBarang(Request $request, HargaBarangZakat $harga)
    {
        $validated = $request->validate([
            'barang_zakat_id' => ['required', Rule::exists('barang_zakat', 'id')],
            'wilayah' => ['required', 'string', 'max:100'],
            'harga_per_satuan' => ['required', 'numeric', 'min:0'],
            'berlaku_mulai' => ['required', 'date'],
            'berlaku_sampai' => ['nullable', 'date', 'after_or_equal:berlaku_mulai'],
            'sumber_harga' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:draft,disetujui'],
        ]);

        $validated['updated_by'] = $request->user()->id;
        if ($validated['status'] === 'disetujui' && $harga->status !== 'disetujui') {
            $validated['approved_by'] = $request->user()->id;
            $validated['approved_at'] = now();
        } elseif ($validated['status'] === 'draft') {
            $validated['approved_by'] = null;
            $validated['approved_at'] = null;
        }

        $harga->update($validated);

        return redirect()->route('admin.kebijakan-zakat.index')->with('success', 'Harga barang berhasil diperbarui.');
    }

    public function destroyHargaBarang(HargaBarangZakat $harga)
    {
        $harga->delete();

        return back()->with('success', 'Harga barang berhasil dihapus.');
    }
}
