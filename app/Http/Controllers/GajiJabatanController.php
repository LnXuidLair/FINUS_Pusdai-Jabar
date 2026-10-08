<?php

namespace App\Http\Controllers;

use App\Models\GajiJabatan;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class GajiJabatanController extends Controller
{
    public function index()
    {
        $gajiJabatans = GajiJabatan::query()
            ->with(['createdBy:id,name', 'updatedBy:id,name'])
            ->orderBy('jabatan')
            ->get();

        return view('gaji_jabatan.index', compact('gajiJabatans'));
    }

    public function create()
    {
        return view('gaji_jabatan.create');
    }

    public function store(Request $request)
    {
        $organizationId = $this->organizationId();

        $validated = $request->validate(
            [
                'jabatan' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('gaji_jabatan', 'jabatan')
                        ->where(fn ($query) => $query->where('organization_id', $organizationId)),
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
                'jabatan.unique' => 'Jabatan tersebut sudah tersedia pada masjid ini.',
                'gaji_perhari.required' => 'Gaji per hari wajib diisi.',
                'gaji_perhari.integer' => 'Gaji per hari harus berupa angka bulat.',
                'gaji_perhari.min' => 'Gaji per hari tidak boleh kurang dari Rp0.',
            ]
        );

        $adminId = Auth::guard(User::ROLE_ADMIN)->id();

        $gajiJabatan = GajiJabatan::create([
            ...$validated,
            'organization_id' => $organizationId,
            'created_by' => $adminId,
            'updated_by' => $adminId,
        ]);

        $this->catatRiwayatGaji($gajiJabatan, (int) $validated['gaji_perhari']);

        return redirect()
            ->route('admin.gaji-jabatan.index')
            ->with('success', 'Data gaji jabatan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $gajiJabatan = GajiJabatan::findOrFail($id);

        return view('gaji_jabatan.edit', compact('gajiJabatan'));
    }

    public function update(Request $request, $id)
    {
        $gajiJabatan = GajiJabatan::findOrFail($id);
        $organizationId = $this->organizationId();

        abort_unless((int) $gajiJabatan->organization_id === $organizationId, 404);

        $validated = $request->validate(
            [
                'jabatan' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('gaji_jabatan', 'jabatan')
                        ->where(fn ($query) => $query->where('organization_id', $organizationId))
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
                'jabatan.unique' => 'Jabatan tersebut sudah tersedia pada masjid ini.',
                'gaji_perhari.required' => 'Gaji per hari wajib diisi.',
                'gaji_perhari.integer' => 'Gaji per hari harus berupa angka bulat.',
                'gaji_perhari.min' => 'Gaji per hari tidak boleh kurang dari Rp0.',
            ]
        );

        $jabatanLama = $gajiJabatan->jabatan;
        $gajiLama = (int) $gajiJabatan->gaji_perhari;

        $gajiJabatan->update([
            ...$validated,
            'updated_by' => Auth::guard(User::ROLE_ADMIN)->id(),
        ]);

        if ($gajiLama !== (int) $validated['gaji_perhari']) {
            $this->tutupRiwayatGajiAktif($gajiJabatan);
            $this->catatRiwayatGaji($gajiJabatan, (int) $validated['gaji_perhari']);
        }

        Pegawai::query()
            ->where('organization_id', $organizationId)
            ->where('jabatan', $jabatanLama)
            ->update(['jabatan' => $validated['jabatan']]);

        return redirect()
            ->route('admin.gaji-jabatan.index')
            ->with('success', 'Data gaji jabatan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $gajiJabatan = GajiJabatan::findOrFail($id);

        if ($gajiJabatan->pegawais()
            ->where('pegawai.organization_id', $gajiJabatan->organization_id)
            ->exists()) {
            return redirect()->route('admin.gaji-jabatan.index')
                ->with('error', 'Tidak dapat menghapus jabatan ini karena masih ada pegawai masjid ini yang terkait.');
        }

        $gajiJabatan->delete();

        return redirect()->route('admin.gaji-jabatan.index')
            ->with('success', 'Data gaji jabatan berhasil dihapus.');
    }

    private function tutupRiwayatGajiAktif(GajiJabatan $gajiJabatan): void
    {
        $gajiJabatan->riwayat()
            ->whereNull('berlaku_sampai')
            ->update([
                'berlaku_sampai' => now()->subDay()->toDateString(),
            ]);
    }

    private function catatRiwayatGaji(GajiJabatan $gajiJabatan, int $gajiPerhari): void
    {
        $gajiJabatan->riwayat()->create([
            'organization_id' => $gajiJabatan->organization_id,
            'gaji_perhari' => $gajiPerhari,
            'berlaku_mulai' => now()->toDateString(),
            'created_by' => Auth::guard(User::ROLE_ADMIN)->id(),
        ]);
    }

    private function organizationId(): int
    {
        $organizationId = (int) (Auth::guard(User::ROLE_ADMIN)->user()?->organization_id ?? 0);

        abort_if($organizationId < 1, 422, 'Organization Admin belum tersedia.');

        return $organizationId;
    }
}