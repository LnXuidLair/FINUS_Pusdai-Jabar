<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCoaRequest;
use App\Http\Requests\UpdateCoaRequest;
use App\Models\Coa;
use App\Services\DataFileImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CoaController extends Controller
{
    /**
     * Maksimal ukuran file import dalam kilobyte.
     *
     * 5.120 KB = 5 MB.
     */
    private const MAX_IMPORT_FILE_KB = 5120;

    /**
     * Maksimal jumlah baris data dalam sekali import.
     *
     * Baris header tidak dihitung.
     */
    private const MAX_IMPORT_ROWS = 5000;

    /**
     * Maksimal pesan kesalahan baris yang ditampilkan.
     */
    private const MAX_IMPORT_ERROR_MESSAGES = 20;

    /**
     * Display a listing of the resource.
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

    /**
     * Delete the specified COA.
     */
    public function destroy($id)
    {
        $coa = Coa::findOrFail($id);

        try {
            $coa->delete();

            return redirect()
                ->route('admin.coa.index')
                ->with('success', 'COA berhasil dihapus.');
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()
                ->route('admin.coa.index')
                ->with(
                    'error',
                    'COA tidak dapat dihapus karena kemungkinan sudah digunakan pada transaksi atau jurnal.'
                );
        }
    }

    /**
     * Import data COA dari CSV, TXT, XLSX, atau XLS.
     *
     * Importer dapat membaca file yang memiliki baris kosong sebelum header,
     * kolom kosong di sebelah kiri, serta variasi nama header seperti
     * "Header Akun", "Kode Akun", dan "Nama Akun".
     */
    public function import(
        Request $request,
        DataFileImportService $dataFileImportService
    ) {
        $request->validate([
            'file_csv' => [
                'bail',
                'required',
                'file',
                'mimes:csv,txt,xlsx,xls',
                'max:' . self::MAX_IMPORT_FILE_KB,
            ],
        ], [
            'file_csv.required' => 'File data wajib dipilih.',
            'file_csv.file' => 'File yang diupload tidak valid.',
            'file_csv.mimes' =>
                'File harus berformat CSV, TXT, XLSX, atau XLS.',
            'file_csv.max' =>
                'Ukuran file maksimal 5 MB.',
        ]);

        try {
            $uploadedFile = $request->file('file_csv');

            if (! $uploadedFile) {
                return back()->withErrors([
                    'file_csv' => 'File data tidak ditemukan.',
                ]);
            }

            $rows = $dataFileImportService->readRows($uploadedFile);
        } catch (\Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'file_csv' =>
                    'File tidak dapat dibaca. Pastikan format dan isi file sudah benar.',
            ]);
        }

        if ($rows === []) {
            return back()->withErrors([
                'file_csv' => 'File kosong atau tidak memiliki data.',
            ]);
        }

        try {
            [$headerRowIndex, $headerIndex] =
                $this->findCoaHeaderRow($rows);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'file_csv' =>
                    'Header file tidak dapat diproses. Pastikan file memiliki kolom kelompok_akun atau header_akun, kode_akun, dan nama_akun.',
            ]);
        }

        /*
         * Ambil hanya baris setelah header. Baris kosong sebelum header,
         * termasuk judul atau catatan pada bagian atas file, akan diabaikan.
         */
        $dataRows = array_slice($rows, $headerRowIndex + 1);

        /*
         * Abaikan baris yang benar-benar kosong agar batas maksimal import
         * dihitung berdasarkan data nyata, bukan area kosong pada Excel.
         */
        $dataRows = array_filter(
            $dataRows,
            fn ($row): bool => is_array($row)
                && ! $this->isEmptyImportRow($row)
        );

        if ($dataRows === []) {
            return back()->withErrors([
                'file_csv' =>
                    'File tidak memiliki baris data setelah header.',
            ]);
        }

        $totalDataRows = count($dataRows);

        if ($totalDataRows > self::MAX_IMPORT_ROWS) {
            return back()->withErrors([
                'file_csv' =>
                    'Jumlah data maksimal '
                    . number_format(
                        self::MAX_IMPORT_ROWS,
                        0,
                        ',',
                        '.'
                    )
                    . ' baris dalam satu kali import. '
                    . 'File yang dipilih berisi '
                    . number_format(
                        $totalDataRows,
                        0,
                        ',',
                        '.'
                    )
                    . ' baris data.',
            ]);
        }

        $hasHeaderAkun = isset($headerIndex['header_akun']);
        $hasKelompokAkun = isset($headerIndex['kelompok_akun']);

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $importErrors = [];

        DB::beginTransaction();

        try {
            foreach ($dataRows as $dataRowIndex => $row) {
                /*
                 * Nomor baris dibuat sesuai posisi aslinya pada file Excel.
                 * +1 untuk indeks berbasis nol dan +1 karena baris data
                 * dimulai setelah baris header.
                 */
                $rowNumber = $headerRowIndex + $dataRowIndex + 2;

                $kelompokAkun = $hasKelompokAkun
                    ? trim((string) (
                        $row[$headerIndex['kelompok_akun']] ?? ''
                    ))
                    : '';

                $headerAkunDariFile = $hasHeaderAkun
                    ? trim((string) (
                        $row[$headerIndex['header_akun']] ?? ''
                    ))
                    : '';

                /*
                 * Prioritaskan header_akun apabila tersedia dan berisi nilai.
                 * Jika kosong, konversikan nilai kelompok_akun menjadi 1-5.
                 */
                $headerAkun = $headerAkunDariFile !== ''
                    ? $this->resolveHeaderAkun($headerAkunDariFile)
                    : $this->resolveHeaderAkun($kelompokAkun);

                $data = [
                    'header_akun' => $headerAkun,
                    'kode_akun' => trim((string) (
                        $row[$headerIndex['kode_akun']] ?? ''
                    )),
                    'nama_akun' => trim((string) (
                        $row[$headerIndex['nama_akun']] ?? ''
                    )),
                ];

                $validator = Validator::make(
                    $data,
                    [
                        'header_akun' => [
                            'required',
                            'integer',
                            'in:1,2,3,4,5',
                        ],
                        'kode_akun' => [
                            'required',
                            'string',
                            'max:50',
                        ],
                        'nama_akun' => [
                            'required',
                            'string',
                            'max:255',
                        ],
                    ],
                    [
                        'header_akun.required' =>
                            'Kelompok akun tidak valid. '
                            . 'Gunakan Aset, Kewajiban, Dana, '
                            . 'Penerimaan, Pengeluaran, atau angka 1 sampai 5.',

                        'header_akun.integer' =>
                            'Kelompok akun harus menggunakan nilai yang valid.',

                        'header_akun.in' =>
                            'Kelompok akun hanya boleh Aset, '
                            . 'Kewajiban, Dana, Penerimaan, '
                            . 'Pengeluaran, atau angka 1 sampai 5.',

                        'kode_akun.required' =>
                            'Kode akun wajib diisi.',

                        'kode_akun.max' =>
                            'Kode akun maksimal 50 karakter.',

                        'nama_akun.required' =>
                            'Nama akun wajib diisi.',

                        'nama_akun.max' =>
                            'Nama akun maksimal 255 karakter.',
                    ]
                );

                if ($validator->fails()) {
                    $skipped++;

                    if (
                        count($importErrors)
                        < self::MAX_IMPORT_ERROR_MESSAGES
                    ) {
                        $importErrors[] =
                            'Baris '
                            . $rowNumber
                            . ': '
                            . implode(
                                ', ',
                                $validator->errors()->all()
                            );
                    }

                    continue;
                }

                $existingCoa = Coa::where(
                    'kode_akun',
                    $data['kode_akun']
                )->first();

                if ($existingCoa) {
                    $existingCoa->update([
                        'header_akun' =>
                            (int) $data['header_akun'],
                        'nama_akun' =>
                            $data['nama_akun'],
                    ]);

                    $updated++;

                    continue;
                }

                Coa::create([
                    'header_akun' =>
                        (int) $data['header_akun'],
                    'kode_akun' =>
                        $data['kode_akun'],
                    'nama_akun' =>
                        $data['nama_akun'],
                ]);

                $created++;
            }

            DB::commit();

            return redirect()
                ->route('admin.coa.index')
                ->with(
                    'success',
                    "Import COA selesai. "
                    . "Data baru: {$created}, "
                    . "diperbarui: {$updated}, "
                    . "dilewati: {$skipped}."
                )
                ->with('import_errors', $importErrors);
        } catch (\Throwable $exception) {
            DB::rollBack();

            report($exception);

            return back()->withErrors([
                'file_csv' =>
                    'Import COA gagal. '
                    . 'Tidak ada perubahan data yang disimpan. '
                    . 'Silakan periksa file dan coba kembali.',
            ]);
        }
    }

    /**
     * Download template CSV untuk import COA.
     */
    public function downloadTemplate(
        DataFileImportService $dataFileImportService
    ) {
        $rows = [
            [
                'kelompok_akun',
                'kode_akun',
                'nama_akun',
            ],
            [
                'Aset',
                '1101',
                'Kas',
            ],
            [
                'Aset',
                '1102',
                'Bank',
            ],
            [
                'Kewajiban',
                '2101',
                'Utang Usaha',
            ],
            [
                'Dana',
                '3101',
                'Dana Tidak Terikat',
            ],
            [
                'Penerimaan',
                '4101',
                'Infaq Kotak Amal',
            ],
            [
                'Penerimaan',
                '4102',
                'Infaq Layanan QRIS',
            ],
            [
                'Penerimaan',
                '4105',
                'Zakat',
            ],
            [
                'Penerimaan',
                '4106',
                'Infak',
            ],
            [
                'Penerimaan',
                '4107',
                'Wakaf',
            ],
            [
                'Pengeluaran',
                '5101',
                'Biaya Bidang Idaroh',
            ],
            [
                'Pengeluaran',
                '5102',
                'Biaya Bidang Imaroh',
            ],
            [
                'Pengeluaran',
                '5103',
                'Biaya Bidang Riayah',
            ],
            [
                'Pengeluaran',
                '5104',
                'Biaya Honorarium',
            ],
            [
                'Pengeluaran',
                '5105',
                'Biaya Konsumsi',
            ],
            [
                'Pengeluaran',
                '5106',
                'Biaya Administrasi Bank',
            ],
            [
                'Pengeluaran',
                '5107',
                'Biaya Pemeliharaan',
            ],
            [
                'Pengeluaran',
                '5108',
                'Biaya Kebersihan',
            ],
            [
                'Pengeluaran',
                '5109',
                'Biaya Kegiatan',
            ],
            [
                'Pengeluaran',
                '5110',
                'Biaya Pengadaan',
            ],
            [
                'Pengeluaran',
                '5111',
                'Penyaluran ZISWAF',
            ],
        ];

        return $dataFileImportService->streamCsvTemplate(
            'template-import-coa.csv',
            $rows
        );
    }

    /**
     * Mengubah kelompok akun menjadi nomor header akun.
     */
    private function resolveHeaderAkun(
        string $kelompokAkun
    ): ?int {
        $kelompok = strtolower(
            trim($kelompokAkun)
        );

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
    /**
     * Mencari posisi header COA pada maksimal 25 baris pertama.
     *
     * @return array{0:int, 1:array<string, int>}
     */
    private function findCoaHeaderRow(array $rows): array
    {
        $aliases = [
            'kelompok_akun' => [
                'kelompok_akun',
                'kelompok',
                'kelompok_coa',
                'jenis_akun',
                'jenis_coa',
            ],
            'header_akun' => [
                'header_akun',
                'header',
                'kode_header',
                'nomor_header',
            ],
            'kode_akun' => [
                'kode_akun',
                'kode',
                'kode_coa',
                'nomor_akun',
                'no_akun',
            ],
            'nama_akun' => [
                'nama_akun',
                'nama',
                'nama_coa',
                'akun',
            ],
        ];

        foreach (array_slice($rows, 0, 25, true) as $rowIndex => $row) {
            if (! is_array($row)) {
                continue;
            }

            $columns = [];
            $duplicates = [];

            foreach ($row as $columnIndex => $cell) {
                $normalizedHeader =
                    $this->normalizeImportHeader((string) $cell);

                if ($normalizedHeader === '') {
                    continue;
                }

                foreach ($aliases as $canonical => $acceptedHeaders) {
                    if (
                        ! in_array(
                            $normalizedHeader,
                            $acceptedHeaders,
                            true
                        )
                    ) {
                        continue;
                    }

                    if (isset($columns[$canonical])) {
                        $duplicates[] = $canonical;
                    } else {
                        $columns[$canonical] = (int) $columnIndex;
                    }

                    break;
                }
            }

            $hasRequiredHeaders =
                isset(
                    $columns['kode_akun'],
                    $columns['nama_akun']
                )
                && (
                    isset($columns['kelompok_akun'])
                    || isset($columns['header_akun'])
                );

            if (! $hasRequiredHeaders) {
                continue;
            }

            if ($duplicates !== []) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'file_csv' => [
                        'Terdapat kolom header yang sama atau duplikat: '
                        . implode(', ', array_unique($duplicates))
                        . '.',
                    ],
                ]);
            }

            return [(int) $rowIndex, $columns];
        }

        throw \Illuminate\Validation\ValidationException::withMessages([
            'file_csv' => [
                'Header file tidak ditemukan. '
                . 'Gunakan kolom kelompok_akun atau header_akun, '
                . 'kode_akun, dan nama_akun. '
                . 'Header boleh ditulis sebagai Kelompok Akun, '
                . 'Header Akun, Kode Akun, dan Nama Akun.',
            ],
        ]);
    }

    /**
     * Menormalkan nama header agar variasi spasi dan kapital tetap terbaca.
     */
    private function normalizeImportHeader(string $header): string
    {
        $header = preg_replace('/^\xEF\xBB\xBF/', '', $header) ?? $header;
        $header = strtolower(trim($header));
        $header = preg_replace('/[^a-z0-9]+/i', '_', $header) ?? '';

        return trim($header, '_');
    }

    /**
     * Memeriksa apakah seluruh sel dalam satu baris kosong.
     */
    private function isEmptyImportRow(array $row): bool
    {
        foreach ($row as $cell) {
            if (trim((string) $cell) !== '') {
                return false;
            }
        }

        return true;
    }

}