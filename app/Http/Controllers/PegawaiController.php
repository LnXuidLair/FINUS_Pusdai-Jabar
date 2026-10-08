<?php

namespace App\Http\Controllers;

use App\Models\GajiJabatan;
use App\Models\Pegawai;
use App\Models\Penggajian;
use App\Models\Presensi;
use App\Models\User;
use App\Support\PhoneNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PegawaiController extends Controller
{
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
            'staffDomain' => $this->staffDomain(),
        ]);
    }

    public function store(Request $request)
    {
        $staffDomain = $this->staffDomain();

        $request->merge([
            'email' => $this->makeStaffEmail(
                (string) $request->input('nama_pegawai'),
                (string) $request->input('nip'),
                $staffDomain
            ),
            'no_telp' => PhoneNumber::normalize($request->input('no_telp')),
            'alamat' => trim((string) $request->input('alamat')),
        ]);

        $validated = $request->validate($this->rules(), $this->validationMessages());
        $organizationId = (int) (Auth::guard(User::ROLE_ADMIN)->user()?->organization_id ?? 0);
        abort_if($organizationId < 1, 422, 'Organization Admin belum tersedia. Jalankan migration organization terlebih dahulu.');

        DB::transaction(function () use ($validated, $organizationId): void {
            $pegawai = new Pegawai($validated);
            $pegawai->organization_id = $organizationId;
            $pegawai->createdby = Auth::guard(User::ROLE_ADMIN)->id();
            $pegawai->save();

            $user = new User([
                'name' => $validated['nama_pegawai'],
                'organization_id' => $organizationId,
                'email' => strtolower($validated['email']),
                // Email institusi Pegawai tidak melalui verifikasi email publik.
                'email_verified_at' => now(),
                // Password acak ini tidak pernah diberikan kepada Pegawai.
                // Password asli baru ditetapkan ketika proses aktivasi selesai.
                'password' => Hash::make(Str::random(64)),
                'role' => User::ROLE_PEGAWAI,
            ]);

            $user->rotateRecoveryCode();
            $user->save();
        });

        return redirect()->route('admin.pegawai.index')
            ->with('success', 'Data pegawai berhasil ditambahkan. Recovery Code telah dibuat otomatis.');
    }

    public function show($id)
    {
        $pegawai = Pegawai::with(['gajiJabatan', 'user'])->findOrFail($id);

        $jumlahHadirDisetujui = Presensi::where('id_pegawai', $pegawai->id)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->where('status', 'hadir')
            ->where('is_approved', true)
            ->whereNotNull('approved_at')
            ->distinct()
            ->count('tanggal');

        $presensiMenunggu = Presensi::where('id_pegawai', $pegawai->id)
            ->where('is_approved', false)
            ->count();

        $penggajianTerakhir = Penggajian::where('id_pegawai', $pegawai->id)
            ->orderByDesc('periode')
            ->first();

        return view('pegawai.show', compact(
            'pegawai',
            'jumlahHadirDisetujui',
            'presensiMenunggu',
            'penggajianTerakhir'
        ));
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
        $staffDomain = $this->staffDomain();

        $request->merge([
            'email' => $this->makeStaffEmail(
                (string) $request->input('nama_pegawai', $pegawai->nama_pegawai),
                (string) $request->input('nip', $pegawai->nip),
                $staffDomain,
                $pegawai->id,
                $oldEmail
            ),
            'no_telp' => PhoneNumber::normalize($request->input('no_telp')),
            'alamat' => trim((string) $request->input('alamat')),
        ]);

        $validated = $request->validate($this->rules($pegawai), $this->validationMessages());

        DB::transaction(function () use ($pegawai, $oldEmail, $validated): void {
            $pegawai->update($validated);

            $user = User::query()
                ->where('email', $oldEmail)
                ->where('role', User::ROLE_PEGAWAI)
                ->first();

            if ($user) {
                $user->forceFill([
                    'name' => $validated['nama_pegawai'],
                    'organization_id' => $pegawai->organization_id,
                    'email' => strtolower($validated['email']),
                ])->save();

                return;
            }

            // Kompatibilitas untuk data Pegawai lama yang dibuat sebelum
            // mekanisme User + Recovery Code diterapkan.
            $user = new User([
                'name' => $validated['nama_pegawai'],
                'organization_id' => $pegawai->organization_id,
                'email' => strtolower($validated['email']),
                'email_verified_at' => now(),
                'password' => Hash::make(Str::random(64)),
                'role' => User::ROLE_PEGAWAI,
            ]);

            $user->rotateRecoveryCode();
            $user->save();
        });

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

        DB::transaction(function () use ($pegawai): void {
            User::query()
                ->where('email', strtolower((string) $pegawai->email))
                ->where('role', User::ROLE_PEGAWAI)
                ->delete();

            $pegawai->delete();
        });

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
                Rule::unique('pegawai', 'nip')
                    ->where(fn ($query) => $query->where(
                        'organization_id',
                        (int) (Auth::guard(User::ROLE_ADMIN)->user()?->organization_id ?? $pegawai?->organization_id ?? 0)
                    ))
                    ->ignore($pegawai?->id),
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
            'no_telp' => [
                'nullable',
                'string',
                'max:16',
                'regex:/^\+[1-9][0-9]{7,14}$/',
                Rule::unique('pegawai', 'no_telp')->ignore($pegawai?->id),
            ],
            'alamat' => ['nullable', 'string', 'max:500'],
        ];
    }

    private function validationMessages(): array
    {
        return [
                        'no_telp.max' => 'Nomor telepon melebihi panjang maksimal format internasional.',
            'no_telp.regex' => 'Pilih kode negara lalu masukkan nomor telepon yang valid.',
            'no_telp.unique' => 'Nomor telepon tersebut sudah digunakan oleh pegawai lain.',
        ];
    }

    private function staffDomain(): string
    {
        $admin = Auth::guard(User::ROLE_ADMIN)->user();
        abort_unless($admin && $admin->organization_id, 403);

        $adminEmail = strtolower(trim((string) $admin->email));

        if (! preg_match('/^admin@([a-z0-9]+)\.finus\.id$/', $adminEmail, $matches)) {
            throw ValidationException::withMessages([
                'email' => 'Email Admin tidak sesuai format FINUS.',
            ]);
        }

        return 'staff' . $matches[1] . '.finus.id';
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