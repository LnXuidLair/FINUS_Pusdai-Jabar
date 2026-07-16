<?php

namespace App\Http\Controllers;

use App\Models\GajiJabatan;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PegawaiController extends Controller
{
    private const STAFF_DOMAIN = 'stafffinuspusdai.org';

    public function verifyStaff(Request $request)
    {
        $validated = $request->validate([
            'nip' => ['required', 'string', 'max:255'],
        ]);

        $pegawai = Pegawai::where('nip', $validated['nip'])->first();

        if (! $pegawai) {
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
        $request->merge([
            'email' => $this->makeStaffEmail(
                (string) $request->input('nama_pegawai'),
                (string) $request->input('nip'),
                self::STAFF_DOMAIN
            ),
        ]);

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
        $oldEmail = strtolower(trim((string) $pegawai->email));

        $request->merge([
            'email' => $this->makeStaffEmail(
                (string) $request->input('nama_pegawai', $pegawai->nama_pegawai),
                (string) $request->input('nip', $pegawai->nip),
                self::STAFF_DOMAIN,
                $pegawai->id,
                $oldEmail
            ),
        ]);

        $validated = $request->validate($this->rules($pegawai));

        $pegawai->update($validated);

        if ($oldEmail !== '' && $oldEmail !== strtolower($validated['email'])) {
            User::where('email', $oldEmail)
                ->where('role', 'pegawai')
                ->update([
                    'name' => $validated['nama_pegawai'],
                    'email' => strtolower($validated['email']),
                ]);
        }

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
        return GajiJabatan::query()
            ->whereNotNull('jabatan')
            ->orderBy('jabatan')
            ->pluck('jabatan')
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

    private function makeStaffEmail(
        string $name,
        string $nip,
        string $domain,
        ?int $ignorePegawaiId = null,
        ?string $allowedUserEmail = null
    ): string {
        $parts = collect(preg_split('/\s+/', trim($name)) ?: [])
            ->filter()
            ->map(fn ($part) => Str::of($part)
                ->ascii()
                ->lower()
                ->replaceMatches('/[^a-z0-9]/', '')
                ->toString()
            )
            ->filter()
            ->take(2)
            ->values();

        $selectedName = $parts->implode('');

        if ($selectedName === '') {
            $selectedName = 'pegawai';
        }

        $nipDigits = preg_replace('/\D+/', '', $nip);
        $nipSuffix = substr($nipDigits, -4);

        if ($nipSuffix === '') {
            $nipSuffix = (string) random_int(1000, 9999);
        }

        $email = strtolower($selectedName . $nipSuffix . '@' . $domain);

        if ($this->emailAlreadyUsed($email, $ignorePegawaiId, $allowedUserEmail)) {
            $email = strtolower($selectedName . $nipSuffix . random_int(10, 99) . '@' . $domain);
        }

        return $email;
    }

    private function emailAlreadyUsed(string $email, ?int $ignorePegawaiId = null, ?string $allowedUserEmail = null): bool
    {
        $usedByPegawai = Pegawai::where('email', $email)
            ->when($ignorePegawaiId, fn ($query) => $query->where('id', '!=', $ignorePegawaiId))
            ->exists();

        if ($usedByPegawai) {
            return true;
        }

        return User::where('email', $email)
            ->when($allowedUserEmail, fn ($query) => $query->where('email', '!=', $allowedUserEmail))
            ->exists();
    }
}