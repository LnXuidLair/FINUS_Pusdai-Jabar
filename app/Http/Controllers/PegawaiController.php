<?php

namespace App\Http\Controllers;

use App\Models\GajiJabatan;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PegawaiController extends Controller
{
    public function verifyStaff(Request $request)
    {
        $validated = $request->validate([
            'nip' => ['required', 'string', 'max:255'],
        ]);

        $pegawai = Pegawai::where('nip', $validated['nip'])->first();

        if (!$pegawai) {
            return response()->json(['valid' => false]);
        }

        return response()->json([
            'valid' => true,
            'verified' => (bool) $pegawai->is_verified,
            'redirect' => $pegawai->is_verified
                ? null
                : route('activate.staff', $pegawai->nip),
        ]);
    }

    public function index()
    {
        $pegawais = Pegawai::with('gajiJabatan')
            ->orderBy('nama_pegawai')
            ->get();

        return view('pegawai.index', compact('pegawais'));
    }

    public function create()
    {
        return view('pegawai.create', [
            'jabatanOptions' => $this->jabatanOptions(),
        ]);
    }

    public function store(Request $request)
    {
        Pegawai::create($request->validate($this->rules()));

        return redirect()->route('admin.pegawai.index')
            ->with('success', 'Data pegawai berhasil ditambahkan.');
    }

    public function show($id)
    {
        $pegawai = Pegawai::findOrFail($id);

        return view('pegawai.show', compact('pegawai'));
    }

    public function edit($id)
    {
        $pegawai = Pegawai::findOrFail($id);

        return view('pegawai.edit', [
            'pegawai' => $pegawai,
            'jabatanOptions' => $this->jabatanOptions($pegawai->jabatan),
        ]);
    }

    public function update(Request $request, $id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->update($request->validate($this->rules($pegawai)));

        return redirect()->route('admin.pegawai.index')
            ->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pegawai = Pegawai::findOrFail($id);

        if ($pegawai->presensis()->exists() || $pegawai->penggajians()->exists()) {
            return redirect()->route('admin.pegawai.index')
                ->with('error', 'Pegawai tidak dapat dihapus karena memiliki data presensi atau penggajian.');
        }

        $pegawai->delete();

        return redirect()->route('admin.pegawai.index')
            ->with('success', 'Data pegawai berhasil dihapus.');
    }

    public function indexKepsek()
    {
        $pegawai = Pegawai::orderBy('nama_pegawai')->get();

        return view('dashboard.pegawai.kepsek.pegawai', compact('pegawai'));
    }

    public function detailPegawaiKepsek($id)
    {
        $pegawai = Pegawai::findOrFail($id);

        return view('dashboard.pegawai.kepsek.detail-pegawai', compact('pegawai'));
    }

    private function jabatanOptions(?string $current = null): array
    {
        $databaseOptions = GajiJabatan::query()
            ->whereNotNull('jabatan')
            ->orderBy('jabatan')
            ->pluck('jabatan');

        return collect(Pegawai::JABATAN_DEFAULT)
            ->merge($databaseOptions)
            ->when($current, fn ($items) => $items->push($current))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    private function rules(?Pegawai $pegawai = null): array
    {
        return [
            'nip' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pegawai', 'nip')->ignore($pegawai?->id),
            ],
            'nama_pegawai' => ['required', 'string', 'max:255'],
            'jabatan' => [
                'required',
                'string',
                'max:100',
                Rule::in($this->jabatanOptions($pegawai?->jabatan)),
            ],
            'gender' => ['required', Rule::in(['L', 'P'])],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('pegawai', 'email')->ignore($pegawai?->id),
            ],
            'no_telp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string', 'max:500'],
        ];
    }
}
