<?php

namespace App\Http\Controllers;

use App\Models\BarangZakat;
use App\Models\Coa;
use App\Models\HargaBarangZakat;
use App\Models\KebijakanAmil;
use App\Models\KebijakanMustahik;
use App\Models\ZakatSetting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class KebijakanZakatController extends Controller
{
    public function index(Request $request)
    {
        $editMuzakki = $request->filled('edit_muzakki')
            ? ZakatSetting::findOrFail($request->integer('edit_muzakki'))
            : null;

        $editAmil = $request->filled('edit_amil')
            ? KebijakanAmil::findOrFail($request->integer('edit_amil'))
            : null;

        $editMustahik = $request->filled('edit_mustahik')
            ? KebijakanMustahik::findOrFail($request->integer('edit_mustahik'))
            : null;

        $editBarang = $request->filled('edit_barang')
            ? BarangZakat::findOrFail($request->integer('edit_barang'))
            : null;

        $editHarga = $request->filled('edit_harga')
            ? HargaBarangZakat::findOrFail($request->integer('edit_harga'))
            : null;

        return view('admin.kebijakan-zakat.index', [
            'pengaturanMuzakki' => ZakatSetting::latest('berlaku_mulai')->get(),
            'kebijakanAmil' => KebijakanAmil::latest('berlaku_mulai')->get(),
            'kebijakanMustahik' => KebijakanMustahik::query()
                ->orderByDesc('aktif')
                ->orderBy('prioritas')
                ->latest('berlaku_mulai')
                ->get(),
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
            'editMuzakki' => $editMuzakki,
            'editAmil' => $editAmil,
            'editMustahik' => $editMustahik,
            'editBarang' => $editBarang,
            'editHarga' => $editHarga,
            'activeTab' => $request->string('tab')->toString() ?: 'barang',
            'asnafLabels' => KebijakanMustahik::ASNAF,
            'bentukPenyaluranLabels' => KebijakanMustahik::BENTUK_PENYALURAN,
            'jenisSumberLabels' => $this->jenisSumberLabels(),
            'kategoriBarangLabels' => BarangZakat::KATEGORI,
            'satuanBarangLabels' => BarangZakat::SATUAN,
            'metodePenilaianLabels' => BarangZakat::METODE_PENILAIAN,
            'statusHargaLabels' => HargaBarangZakat::STATUS,
        ]);
    }

    public function storeBarang(Request $request)
    {
        $data = $this->validateBarang($request);
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;

        BarangZakat::create($data);

        return $this->redirectToTab('barang', 'Barang zakat berhasil ditambahkan.');
    }

    public function updateBarang(Request $request, BarangZakat $barang)
    {
        $data = $this->validateBarang($request, $barang);
        $data['updated_by'] = $request->user()->id;
        $barang->update($data);

        return $this->redirectToTab('barang', 'Barang zakat berhasil diperbarui.');
    }

    public function storeHargaBarang(Request $request)
    {
        $data = $this->validateHargaBarang($request);
        $this->ensureHargaPeriodAvailable($data);
        $data = $this->setPriceApproval($data, $request->user()->id);
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;

        HargaBarangZakat::create($data);

        return $this->redirectToTab('barang', 'Harga barang zakat berhasil ditambahkan.');
    }

    public function updateHargaBarang(Request $request, HargaBarangZakat $harga)
    {
        $data = $this->validateHargaBarang($request, $harga);
        $this->ensureHargaPeriodAvailable($data, $harga);
        $data = $this->setPriceApproval($data, $request->user()->id);
        $data['updated_by'] = $request->user()->id;
        $harga->update($data);

        return $this->redirectToTab('barang', 'Harga barang zakat berhasil diperbarui.');
    }

    public function storeMuzakki(Request $request)
    {
        $data = $this->validateMuzakki($request);
        $this->ensurePeriodAvailable(ZakatSetting::query(), $data, 'Periode aturan muzakki bertabrakan dengan aturan aktif lainnya.');

        ZakatSetting::create($data);

        return $this->redirectToTab('muzakki', 'Aturan perhitungan muzakki berhasil ditambahkan.');
    }

    public function updateMuzakki(Request $request, ZakatSetting $pengaturan)
    {
        $data = $this->validateMuzakki($request, $pengaturan);
        $this->ensurePeriodAvailable(
            ZakatSetting::query()->whereKeyNot($pengaturan->id),
            $data,
            'Periode aturan muzakki bertabrakan dengan aturan aktif lainnya.'
        );

        $pengaturan->update($data);

        return $this->redirectToTab('muzakki', 'Aturan perhitungan muzakki berhasil diperbarui.');
    }

    public function storeAmil(Request $request)
    {
        $data = $this->validateAmil($request);
        $this->ensurePeriodAvailable(
            KebijakanAmil::query()->where('jenis_sumber', $data['jenis_sumber']),
            $data,
            'Periode kebijakan untuk sumber dana ini bertabrakan dengan kebijakan aktif lainnya.'
        );

        KebijakanAmil::create($data);

        return $this->redirectToTab('amil', 'Kebijakan hak amil berhasil ditambahkan.');
    }

    public function updateAmil(Request $request, KebijakanAmil $kebijakan)
    {
        $data = $this->validateAmil($request);
        $this->ensurePeriodAvailable(
            KebijakanAmil::query()
                ->where('jenis_sumber', $data['jenis_sumber'])
                ->whereKeyNot($kebijakan->id),
            $data,
            'Periode kebijakan untuk sumber dana ini bertabrakan dengan kebijakan aktif lainnya.'
        );

        $kebijakan->update($data);

        return $this->redirectToTab('amil', 'Kebijakan hak amil berhasil diperbarui.');
    }

    public function storeMustahik(Request $request)
    {
        $data = $this->validateMustahik($request);
        $this->ensureMustahikAllocationAvailable($data);
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;

        KebijakanMustahik::create($data);

        return $this->redirectToTab('mustahik', 'Kebijakan mustahik berhasil ditambahkan.');
    }

    public function updateMustahik(Request $request, KebijakanMustahik $kebijakan)
    {
        $data = $this->validateMustahik($request, $kebijakan);
        $this->ensureMustahikAllocationAvailable($data, $kebijakan);
        $data['updated_by'] = $request->user()->id;
        $kebijakan->update($data);

        return $this->redirectToTab('mustahik', 'Kebijakan mustahik berhasil diperbarui.');
    }

    private function validateMuzakki(Request $request, ?ZakatSetting $pengaturan = null): array
    {
        $data = $request->validate([
            'tahun' => [
                'required',
                'integer',
                'min:2000',
                'max:2100',
                Rule::unique('zakat_settings', 'tahun')->ignore($pengaturan?->id),
            ],
            'nisab_penghasilan_tahunan' => ['required', 'integer', 'min:0'],
            'nisab_penghasilan_bulanan' => ['required', 'integer', 'min:0'],
            'nisab_maal' => ['required', 'integer', 'min:0'],
            'persentase_zakat' => ['required', 'numeric', 'min:0.01', 'max:100'],
            'zakat_fitrah_per_jiwa' => ['required', 'integer', 'min:0'],
            'beras_fitrah_kg' => ['required', 'numeric', 'min:0.01', 'max:100'],
            'beras_fitrah_liter' => ['required', 'numeric', 'min:0.01', 'max:100'],
            'sumber' => ['nullable', 'string', 'max:2000'],
            'berlaku_mulai' => ['required', 'date'],
            'berlaku_sampai' => ['nullable', 'date', 'after_or_equal:berlaku_mulai'],
            'aktif' => ['nullable', 'boolean'],
        ]);

        $data['aktif'] = $request->boolean('aktif');

        return $data;
    }

    private function validateBarang(Request $request, ?BarangZakat $barang = null): array
    {
        $data = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:30',
                Rule::unique('barang_zakat', 'kode')->ignore($barang?->id),
            ],
            'nama' => [
                'required',
                'string',
                'max:100',
                Rule::unique('barang_zakat', 'nama')->ignore($barang?->id),
            ],
            'kategori' => ['required', Rule::in(array_keys(BarangZakat::KATEGORI))],
            'satuan_dasar' => ['required', Rule::in(array_keys(BarangZakat::SATUAN))],
            'metode_penilaian' => ['required', Rule::in(array_keys(BarangZakat::METODE_PENILAIAN))],
            'coa_persediaan_id' => ['nullable', 'integer', Rule::exists('coa', 'id')->where('header_akun', 1)],
            'aktif' => ['nullable', 'boolean'],
        ]);

        $data['kode'] = strtoupper(trim($data['kode']));
        $data['nama'] = trim($data['nama']);
        $data['aktif'] = $request->boolean('aktif');

        return $data;
    }

    private function validateHargaBarang(Request $request, ?HargaBarangZakat $harga = null): array
    {
        $uniquePeriod = Rule::unique('harga_barang_zakat', 'berlaku_mulai')
            ->where(fn ($query) => $query
                ->where('barang_zakat_id', $request->integer('barang_zakat_id'))
                ->where('wilayah', trim((string) $request->input('wilayah'))))
            ->ignore($harga?->id);

        $data = $request->validate([
            'barang_zakat_id' => ['required', 'integer', Rule::exists('barang_zakat', 'id')],
            'wilayah' => ['required', 'string', 'max:100'],
            'harga_per_satuan' => ['required', 'integer', 'min:1'],
            'berlaku_mulai' => ['required', 'date', $uniquePeriod],
            'berlaku_sampai' => ['nullable', 'date', 'after_or_equal:berlaku_mulai'],
            'sumber_harga' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(array_keys(HargaBarangZakat::STATUS))],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ]);

        $data['wilayah'] = trim($data['wilayah']);
        $data['sumber_harga'] = trim($data['sumber_harga']);

        return $data;
    }

    private function validateAmil(Request $request): array
    {
        $data = $request->validate([
            'jenis_sumber' => ['required', Rule::in(array_keys($this->jenisSumberLabels()))],
            'persentase_amil' => ['required', 'numeric', 'min:0', 'max:100'],
            'berlaku_mulai' => ['required', 'date'],
            'berlaku_sampai' => ['nullable', 'date', 'after_or_equal:berlaku_mulai'],
            'dasar_aturan' => ['nullable', 'string', 'max:255'],
            'potong_infak_terikat' => ['nullable', 'boolean'],
            'aktif' => ['nullable', 'boolean'],
        ]);

        $data['potong_infak_terikat'] = $request->boolean('potong_infak_terikat');
        $data['aktif'] = $request->boolean('aktif');

        return $data;
    }

    private function validateMustahik(Request $request, ?KebijakanMustahik $kebijakan = null): array
    {
        $uniquePeriode = Rule::unique('kebijakan_mustahik', 'berlaku_mulai')
            ->where(fn ($query) => $query->where('asnaf', $request->input('asnaf')))
            ->ignore($kebijakan?->id);

        $data = $request->validate([
            'asnaf' => ['required', Rule::in(array_keys(KebijakanMustahik::ASNAF))],
            'prioritas' => ['required', 'integer', 'min:1', 'max:8'],
            'target_persentase' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'batas_bantuan' => ['nullable', 'integer', 'min:0'],
            'bentuk_penyaluran' => ['required', Rule::in(array_keys(KebijakanMustahik::BENTUK_PENYALURAN))],
            'kriteria' => ['required', 'string', 'max:2000'],
            'dasar_aturan' => ['nullable', 'string', 'max:255'],
            'berlaku_mulai' => ['required', 'date', $uniquePeriode],
            'berlaku_sampai' => ['nullable', 'date', 'after_or_equal:berlaku_mulai'],
            'aktif' => ['nullable', 'boolean'],
        ]);

        $data['aktif'] = $request->boolean('aktif');

        return $data;
    }

    private function redirectToTab(string $tab, string $message)
    {
        return redirect()
            ->route('admin.kebijakan-zakat.index', ['tab' => $tab])
            ->with('success', $message);
    }

    private function ensureMustahikAllocationAvailable(
        array $data,
        ?KebijakanMustahik $current = null
    ): void {
        if (! $data['aktif']) {
            return;
        }

        $overlapping = $this->overlappingPolicies(
            KebijakanMustahik::query()
                ->when($current, fn ($query) => $query->whereKeyNot($current->id)),
            $data
        );

        if ((clone $overlapping)->where('asnaf', $data['asnaf'])->exists()) {
            throw ValidationException::withMessages([
                'berlaku_mulai' => 'Asnaf ini sudah memiliki kebijakan aktif pada periode tersebut.',
            ]);
        }

        $existingTarget = (float) $overlapping->sum('target_persentase');
        $newTarget = (float) ($data['target_persentase'] ?? 0);

        if ($existingTarget + $newTarget > 100) {
            throw ValidationException::withMessages([
                'target_persentase' => 'Total target alokasi pada periode yang sama tidak boleh melebihi 100%.',
            ]);
        }
    }

    private function ensureHargaPeriodAvailable(
        array $data,
        ?HargaBarangZakat $current = null
    ): void {
        if ($data['status'] !== 'disetujui') {
            return;
        }

        $query = HargaBarangZakat::query()
            ->where('barang_zakat_id', $data['barang_zakat_id'])
            ->where('wilayah', $data['wilayah'])
            ->where('status', 'disetujui')
            ->when($current, fn ($builder) => $builder->whereKeyNot($current->id));

        $endDate = $data['berlaku_sampai'] ?? '9999-12-31';

        $overlapExists = $query
            ->whereDate('berlaku_mulai', '<=', $endDate)
            ->where(function ($builder) use ($data): void {
                $builder->whereNull('berlaku_sampai')
                    ->orWhereDate('berlaku_sampai', '>=', $data['berlaku_mulai']);
            })
            ->exists();

        if ($overlapExists) {
            throw ValidationException::withMessages([
                'berlaku_mulai' => 'Harga yang disetujui untuk barang dan wilayah ini bertabrakan dengan periode harga lain.',
            ]);
        }
    }

    private function setPriceApproval(array $data, int $userId): array
    {
        $approved = $data['status'] === 'disetujui';
        $data['approved_by'] = $approved ? $userId : null;
        $data['approved_at'] = $approved ? now() : null;

        return $data;
    }

    private function ensurePeriodAvailable($query, array $data, string $message): void
    {
        if (! $data['aktif']) {
            return;
        }

        if ($this->overlappingPolicies($query, $data)->exists()) {
            throw ValidationException::withMessages([
                'berlaku_mulai' => $message,
            ]);
        }
    }

    private function overlappingPolicies($query, array $data)
    {
        $endDate = $data['berlaku_sampai'] ?? '9999-12-31';

        return $query
            ->where('aktif', true)
            ->whereDate('berlaku_mulai', '<=', $endDate)
            ->where(function ($builder) use ($data): void {
                $builder->whereNull('berlaku_sampai')
                    ->orWhereDate('berlaku_sampai', '>=', $data['berlaku_mulai']);
            });
    }

    private function jenisSumberLabels(): array
    {
        return [
            'zakat' => 'Zakat',
            'infaq_mutlaqah' => 'Infak Tidak Terikat',
            'infaq_muqayyadah' => 'Infak Terikat',
        ];
    }
}
