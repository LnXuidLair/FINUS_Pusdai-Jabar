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
        $coa = Coa::orderBy('kode_akun')->get();

        return view('coa.index', [
            'coa' => $coa,
            'title' => 'Contoh M2',
            'nama' => 'Farel Prayoga',
        ]);
    }

    public function index()
    {
        $coa = Coa::orderBy('kode_akun')->get();

        return view('coa.index', [
            'coa' => $coa,
            'title' => 'Daftar COA',
            'nama' => 'Admin',
        ]);
    }

    /**
     * Fetch all COA data for Ajax requests.
     */
    public function fetchAll()
    {
        $coas = Coa::orderBy('kode_akun')->get();

        if ($coas->isEmpty()) {
            return '<h1 class="text-center text-secondary my-5">No record present in the database!</h1>';
        }

        $output = view('coa.partials.coa_table', [
            'coas' => $coas,
        ])->render();

        return response()->json([
            'html' => $output,
        ]);
    }

    /**
     * Fetch COA data in JSON format for API or Ajax requests.
     */
    public function fetchCoa()
    {
        $coa = Coa::orderBy('kode_akun')->get();

        return response()->json([
            'coa' => $coa,
        ]);
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

        if (empty($validated['kode_akun'])) {
            return redirect()
                ->route('admin.coa.create')
                ->with('error', 'Kode Akun harus diisi.');
        }

        Coa::create($validated);

        return redirect()
            ->route('admin.coa.index')
            ->with('success', 'COA berhasil disimpan.');
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
        $coa = Coa::findOrFail($id);

        $validated = $request->validated();

        $coa->update($validated);

        return redirect()
            ->route('admin.coa.index')
            ->with('success', 'Data COA berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $coa = Coa::findOrFail($id);

        $coa->delete();

        return redirect()
            ->route('admin.coa.index')
            ->with('success', 'COA berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_csv' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $file = $request->file('file_csv');
        $path = $file->getRealPath();

        $delimiter = $this->detectCsvDelimiter($path);

        $handle = fopen($path, 'r');

        if (! $handle) {
            return back()->withErrors([
                'file_csv' => 'File CSV tidak dapat dibaca.',
            ]);
        }

        $header = fgetcsv($handle, 0, $delimiter);

        if (! $header) {
            fclose($handle);

            return back()->withErrors([
                'file_csv' => 'File CSV kosong atau header tidak valid.',
            ]);
        }

        $header = array_map(function ($value) {
            $value = preg_replace('/^\xEF\xBB\xBF/', '', (string) $value);

            return strtolower(trim($value));
        }, $header);

        $requiredHeaders = [
            'kode_akun',
            'nama_akun',
        ];

        $missingHeaders = array_diff($requiredHeaders, $header);

        $hasHeaderAkun = in_array('header_akun', $header, true);
        $hasKelompokAkun = in_array('kelompok_akun', $header, true);

        if ($missingHeaders !== [] || (! $hasHeaderAkun && ! $hasKelompokAkun)) {
            fclose($handle);

            return back()->withErrors([
                'file_csv' => 'Header CSV tidak lengkap. Wajib ada: kelompok_akun, kode_akun, nama_akun. Kolom header_akun juga tetap didukung jika ingin memakai angka.',
            ]);
        }

        $headerIndex = array_flip($header);

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $rowNumber = 1;
        $importErrors = [];

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $rowNumber++;

                if (count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0) {
                    continue;
                }

                $kelompokAkun = $hasKelompokAkun
                    ? trim($row[$headerIndex['kelompok_akun']] ?? '')
                    : '';

                $headerAkun = $hasHeaderAkun
                    ? trim($row[$headerIndex['header_akun']] ?? '')
                    : $this->resolveHeaderAkun($kelompokAkun);

                $data = [
                    'header_akun' => $headerAkun,
                    'kode_akun' => trim($row[$headerIndex['kode_akun']] ?? ''),
                    'nama_akun' => trim($row[$headerIndex['nama_akun']] ?? ''),
                ];

                $validator = Validator::make($data, [
                    'header_akun' => ['required', 'integer', 'in:1,2,3,4,5'],
                    'kode_akun' => ['required', 'string', 'max:50'],
                    'nama_akun' => ['required', 'string', 'max:255'],
                ], [
                    'header_akun.required' => 'Kelompok akun tidak valid. Gunakan Aset, Kewajiban, Dana, Penerimaan, atau Pengeluaran.',
                    'header_akun.integer' => 'Kelompok akun harus valid.',
                    'header_akun.in' => 'Kelompok akun hanya boleh Aset, Kewajiban, Dana, Penerimaan, atau Pengeluaran.',
                    'kode_akun.required' => 'Kode akun wajib diisi.',
                    'nama_akun.required' => 'Nama akun wajib diisi.',
                ]);

                if ($validator->fails()) {
                    $skipped++;

                    $importErrors[] = 'Baris ' . $rowNumber . ': ' . implode(', ', $validator->errors()->all());

                    continue;
                }

                $existingCoa = Coa::where('kode_akun', $data['kode_akun'])->first();

                if ($existingCoa) {
                    $existingCoa->update([
                        'header_akun' => (int) $data['header_akun'],
                        'nama_akun' => $data['nama_akun'],
                    ]);

                    $updated++;
                } else {
                    Coa::create([
                        'header_akun' => (int) $data['header_akun'],
                        'kode_akun' => $data['kode_akun'],
                        'nama_akun' => $data['nama_akun'],
                    ]);

                    $created++;
                }
            }

            fclose($handle);

            DB::commit();

            return redirect()
                ->route('admin.coa.index')
                ->with('success', "Import COA selesai. Data baru: {$created}, diperbarui: {$updated}, dilewati: {$skipped}.")
                ->with('import_errors', array_slice($importErrors, 0, 20));
        } catch (\Throwable $exception) {
            fclose($handle);

            DB::rollBack();

            return back()->withErrors([
                'file_csv' => 'Import gagal: ' . $exception->getMessage(),
            ]);
        }
    }

    public function downloadTemplate()
    {
        $rows = [
            ['kelompok_akun', 'kode_akun', 'nama_akun'],
            ['Aset', '1101', 'Kas'],
            ['Aset', '1102', 'Bank'],
            ['Penerimaan', '4101', 'Infaq Kotak Amal'],
            ['Penerimaan', '4102', 'Infaq Layanan QRIS'],
            ['Penerimaan', '4105', 'Zakat'],
            ['Penerimaan', '4106', 'Infak'],
            ['Penerimaan', '4107', 'Wakaf'],
            ['Pengeluaran', '5101', 'Biaya Bidang Idaroh'],
            ['Pengeluaran', '5102', 'Biaya Bidang Imaroh'],
            ['Pengeluaran', '5103', 'Biaya Bidang Riayah'],
        ];

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');

            fputs($handle, "\xEF\xBB\xBF");

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, 'template-import-coa.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function resolveHeaderAkun(string $kelompokAkun): ?int
    {
        $kelompok = strtolower(trim($kelompokAkun));

        return match ($kelompok) {
            '1',
            'aset',
            'aktiva' => 1,

            '2',
            'kewajiban',
            'liabilitas',
            'utang',
            'hutang' => 2,

            '3',
            'dana',
            'ekuitas',
            'saldo dana',
            'modal' => 3,

            '4',
            'penerimaan',
            'pendapatan',
            'income' => 4,

            '5',
            'pengeluaran',
            'beban',
            'biaya',
            'expense' => 5,

            default => null,
        };
    }

    private function detectCsvDelimiter(string $path): string
    {
        $firstLine = '';

        $handle = fopen($path, 'r');

        if ($handle) {
            $firstLine = fgets($handle) ?: '';
            fclose($handle);
        }

        $commaCount = substr_count($firstLine, ',');
        $semicolonCount = substr_count($firstLine, ';');

        return $semicolonCount > $commaCount ? ';' : ',';
    }
}