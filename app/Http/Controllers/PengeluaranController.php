<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\KetentuanPokokZakat;
use App\Models\MasterAsnaf;
use App\Models\Pegawai;
use App\Models\Pengeluaran;
use App\Models\Penggajian;
use App\Models\PeriodePenyaluranZakat;
use App\Models\TransactionCategory;
use App\Services\Accounting\Psak109PostingService;
use App\Services\TransactionProofStorage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PengeluaranController extends Controller
{
    public function index()
    {
        /*
         * Pengeluaran manual/operasional diambil dari tabel pengeluaran.
         * Record gaji lama yang mungkin pernah disimpan di tabel ini
         * dikecualikan agar tidak tampil dua kali dengan data penggajian.
         */
        $pengeluaranManual = Pengeluaran::query()
            ->with(['zakatPenyaluran', 'periodePenyaluranZakat', 'transactionCategory', 'coaDebit', 'coaKredit', 'pegawai'])
            ->whereNull('id_penggajian')
            ->whereNull('referensi_penggajian_id')
            ->where(function ($query): void {
                $query->whereNull('jenis')
                    ->orWhere('jenis', '!=', 'gaji');
            })
            ->orderByDesc('tanggal')
            ->orderByDesc('created_at')
            ->get()
            ->map(function (Pengeluaran $item) {
                $item->is_gaji = false;

                return $item;
            });

        /*
         * Gaji hanya menjadi pengeluaran setelah statusnya Sudah Dibayar.
         * Data ini tidak diduplikasi ke tabel pengeluaran; ditampilkan sebagai
         * representasi dari tabel penggajian agar sumber datanya tetap satu.
         */
        $pengeluaranGaji = Penggajian::query()
            ->where('status_penggajian', 'sudah_dibayar')
            ->whereNotNull('tanggal')
            ->with('pegawai')
            ->orderByDesc('tanggal')
            ->orderByDesc('created_at')
            ->get()
            ->map(function (Penggajian $gaji): object {
                return (object) [
                    'id' => 'gaji_'.$gaji->id,
                    'kategori' => 'Beban Gaji dan Honorarium',
                    'deskripsi' => 'Gaji '
                        .($gaji->pegawai?->nama_pegawai ?? 'Pegawai')
                        .' periode '
                        .$gaji->periode,
                    'jumlah' => (int) $gaji->total_gaji,
                    'tanggal' => $gaji->tanggal,
                    'bukti_pembayaran' => $gaji->bukti_pembayaran,
                    'status_verifikasi' => 'diterima',
                    'is_gaji' => true,
                    'created_at' => $gaji->created_at,
                    'updated_at' => $gaji->updated_at,
                ];
            });

        $allPengeluaran = $pengeluaranManual
            ->concat($pengeluaranGaji)
            ->sortByDesc(function ($item): string {
                $tanggal = (string) ($item->tanggal ?? '');
                $createdAt = (string) ($item->created_at ?? '');

                return $tanggal.' '.$createdAt;
            })
            ->values();

        $asnafLabels = MasterAsnaf::labels();

        return view('pengeluaran.index', compact('allPengeluaran', 'asnafLabels'));
    }

    public function create()
    {
        TransactionCategory::ensureDefaults();
        $transactionCategories = TransactionCategory::query()
            ->expense()
            ->active()
            ->whereHas('coa', fn ($query) => $query->pengeluaranManual())
            ->with('coa')
            ->orderBy('name')
            ->get()
            ->filter(fn (TransactionCategory $category) => (bool) $category->resolvedFundType());
        // Kelompokkan berdasarkan kode akun yang dipetakan, bukan kolom group yang bisa usang.
        $transactionCategoriesGrouped = $transactionCategories
            ->sortBy(fn (TransactionCategory $category) => ($category->coa?->kode_akun ?? '').' '.$category->name)
            ->groupBy(fn (TransactionCategory $category) => $category->resolvedFundType());
        $akunPembayaran = Coa::query()
            ->whereIn('kode_akun', ['1101', '1102'])
            ->orderBy('kode_akun')
            ->get();
        $pegawaiList = Pegawai::query()
            ->orderBy('nama_pegawai')
            ->get();
        $fundTypeLabels = config('transaction_categories.fund_types', []);
        $asnafLabels = collect(MasterAsnaf::labels())->except('amil')->all();
        $postingService = app(Psak109PostingService::class);
        // Saldo yang benar-benar tercatat di jurnal (bukan sekadar transaksi pending).
        $saldoDanaJurnal = $postingService->getSaldoDana();
        $saldoKasBankJurnal = $akunPembayaran->mapWithKeys(
            fn (Coa $akun): array => [
                $akun->id => (int) round($postingService->getSaldoKasBank($akun)),
            ]
        )->all();
        $saldoZakat = max(0, (int) round($saldoDanaJurnal['zakat'] ?? 0));
        $ketentuanZakat = KetentuanPokokZakat::untukJenis('maal')
            ?? KetentuanPokokZakat::query()->aktif()->first();
        $targetAsnaf = $this->normalizeTargetAsnaf(
            (array) ($ketentuanZakat?->target_mustahik ?? []),
            array_keys($asnafLabels)
        );

        return view('pengeluaran.create', compact(
            'transactionCategoriesGrouped',
            'akunPembayaran',
            'pegawaiList',
            'fundTypeLabels',
            'asnafLabels',
            'saldoZakat',
            'saldoDanaJurnal',
            'saldoKasBankJurnal',
            'targetAsnaf'
        ));
    }

    public function store(Request $request)
    {
        $asnafLabels = collect(MasterAsnaf::labels())->except('amil')->all();
        $organizationId = (int) ($request->user('admin')?->organization_id
            ?? $request->user('pegawai')?->organization_id);

        $validated = $request->validate([
            'transaction_category_id' => [
                'required',
                'integer',
                Rule::exists('transaction_categories', 'id')->where(
                    fn ($query) => $query
                        ->where('organization_id', $organizationId)
                        ->where('transaction_type', 'pengeluaran')
                        ->where('is_active', true)
                ),
            ],
            'coa_kredit_id' => [
                'nullable',
                'integer',
                Rule::exists('coa', 'id')->where(
                    fn ($query) => $query->whereIn('kode_akun', ['1101', '1102'])
                        ->where('organization_id', $organizationId)
                ),
            ],
            'id_pegawai' => [
                'nullable',
                'integer',
                Rule::exists('pegawai', 'id')->where(
                    fn ($query) => $query->where('organization_id', $organizationId)
                ),
            ],
            'deskripsi' => ['required', 'string', 'max:255'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'tanggal' => ['required', 'date'],
            'bukti_pembayaran' => [
                'nullable',
                'file',
                'mimes:jpeg,jpg,png,pdf',
                'max:2048',
            ],
            'bukti_surat_tugas' => [
                'nullable',
                'file',
                'mimes:jpeg,jpg,png,pdf',
                'max:2048',
            ],
            'periode_zakat' => ['nullable', 'date_format:Y-m'],
            'target_asnaf' => ['nullable', 'array'],
            'target_asnaf.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'zakat_details' => ['nullable', 'array', 'max:100'],
            'zakat_details.*.asnaf' => ['nullable', 'string'],
            'zakat_details.*.nama_penerima' => ['nullable', 'string', 'max:150'],
            'zakat_details.*.nik_penerima' => ['nullable', 'string', 'max:30'],
            'zakat_details.*.alamat_penerima' => ['nullable', 'string', 'max:500'],
            'zakat_details.*.no_hp_penerima' => ['nullable', 'string', 'max:30'],
            'zakat_details.*.nominal' => ['nullable', 'integer'],
        ]);

        $transactionCategory = TransactionCategory::query()
            ->expense()
            ->active()
            ->whereHas('coa', fn ($query) => $query->pengeluaranManual())
            ->with('coa')
            ->findOrFail($validated['transaction_category_id']);
        $coaDebit = $transactionCategory->coa;
        if (! $coaDebit) {
            throw ValidationException::withMessages([
                'transaction_category_id' => 'Kategori belum memiliki pemetaan akun debit. Hubungi administrator.',
            ]);
        }
        $jenisDana = $transactionCategory->resolvedFundType();
        if (! $jenisDana) {
            throw ValidationException::withMessages([
                'transaction_category_id' => 'Kode akun kategori belum memiliki aturan sumber dana.',
            ]);
        }
        $purpose = $request->validate([
            'wakaf_purpose' => [Rule::requiredIf($jenisDana === 'wakaf'), 'nullable', Rule::in(['pembangunan', 'mushaf'])],
        ]);
        $validated['wakaf_purpose'] = $jenisDana === 'wakaf' ? $purpose['wakaf_purpose'] : null;
        $source = $request->validate(['zakat_source' => [Rule::requiredIf($jenisDana === 'zakat'), 'nullable', Rule::in(['fitrah', 'lainnya'])]]);
        $validated['zakat_source'] = $jenisDana === 'zakat' ? $source['zakat_source'] : null;
        $validated['jenis_dana'] = $jenisDana;
        $coaKredit = ! empty($validated['coa_kredit_id'])
            ? Coa::query()
                ->whereIn('kode_akun', ['1101', '1102'])
                ->findOrFail($validated['coa_kredit_id'])
            : Coa::query()->where('kode_akun', '1101')->firstOrFail();
        // Honorarium menerima dokumen pendukung dari kampus, PUSDAI, atau pemberi tugas lain.
        // Flag wajib surat tugas lama tidak lagi memaksa seluruh kategori honorarium,
        // termasuk data kategori yang dibuat sebelum pembaruan ini.
        $isHonorarium = $transactionCategory->form_type === 'honorarium';
        $jenisPenerima = $isHonorarium
            ? $request->input('jenis_penerima', 'pegawai')
            : 'pegawai';
        $isExternal = $isHonorarium && $jenisPenerima === 'eksternal';
        $requiresEmployee = $isHonorarium
            ? ! $isExternal
            : (bool) $transactionCategory->requires_employee;
        $requiresAssignmentProof = ! $isHonorarium
            && (bool) $transactionCategory->requires_assignment_proof;
        $pegawai = null;

        if ($isHonorarium || $requiresEmployee || $requiresAssignmentProof) {
            $honorariumValidated = $request->validate([
                'jenis_penerima' => [
                    'nullable',
                    Rule::in(['pegawai', 'eksternal']),
                ],
                'nama_penerima_eksternal' => [
                    Rule::requiredIf($isExternal),
                    'nullable',
                    'string',
                    'max:150',
                ],
                'id_pegawai' => [
                    Rule::requiredIf($requiresEmployee),
                    'nullable',
                    'integer',
                    Rule::exists('pegawai', 'id')->where(
                        fn ($query) => $query->where('organization_id', $organizationId)
                    ),
                ],
            ]);
            $validated = array_merge($validated, $honorariumValidated);
            // Jangan terima ID pegawai tersembunyi untuk honorarium penerima eksternal.
            $pegawai = ! $isExternal && ! empty($validated['id_pegawai'])
                ? Pegawai::query()->findOrFail($validated['id_pegawai'])
                : null;

            if ($isExternal) {
                // Tidak membuat tabel baru: nama penerima masuk ke deskripsi dan
                // keterangan yang sudah ada, sehingga ikut tampil pada laporan.
                $deskripsi = 'Honorarium '.trim($validated['nama_penerima_eksternal'])
                    .' - '.$validated['deskripsi'];
                if (mb_strlen($deskripsi) > 255) {
                    throw ValidationException::withMessages([
                        'deskripsi' => 'Deskripsi beserta nama penerima maksimal 255 karakter.',
                    ]);
                }
                $validated['deskripsi'] = $deskripsi;
            }
        }

        if ($requiresAssignmentProof && ! $request->hasFile('bukti_surat_tugas')) {
            throw ValidationException::withMessages([
                'bukti_surat_tugas' => 'Dokumen pendukung diwajibkan untuk kategori ini.',
            ]);
        }
        $zakatDetails = [];
        $periodeStart = null;
        $periodeEnd = null;
        $targetAsnaf = [];
        $saldoZakat = 0;

        if ($transactionCategory->requires_asnaf || $transactionCategory->form_type === 'zakat_distribution') {
            $percentageData = $request->validate([
                'target_asnaf' => ['required', 'array'],
                'target_asnaf.*' => ['required', 'numeric', 'min:0', 'max:100'],
            ]);
            $targetAsnaf = collect($asnafLabels)->map(fn ($label, $asnaf) =>
                $percentageData['target_asnaf'][$asnaf] ?? 0)->all();
            $allocations = app(\App\Services\ZakatDistributionAllocator::class)
                ->allocate((int) $validated['jumlah'], $targetAsnaf);
            $details = collect($request->input('zakat_details', []));
            $request->merge(['zakat_details' => $details->filter(fn ($detail) =>
                is_array($detail) && ($targetAsnaf[$detail['asnaf'] ?? ''] ?? 0) > 0
            )->values()->all()]);
            $zakatValidated = $request->validate([
                'periode_zakat' => ['required', 'date_format:Y-m'],
                'zakat_details' => ['required', 'array', 'min:1'],
                'zakat_details.*.asnaf' => ['required', 'string', 'distinct', Rule::in(array_keys($asnafLabels))],
                'zakat_details.*.nama_penerima' => ['required', 'string', 'max:150'],
                'zakat_details.*.nik_penerima' => ['nullable', 'string', 'max:30'],
                'zakat_details.*.alamat_penerima' => ['nullable', 'string', 'max:500'],
                'zakat_details.*.no_hp_penerima' => ['nullable', 'string', 'max:30'],
            ], [
                'zakat_details.required' => 'Isi penanggung jawab untuk setiap asnaf yang disalurkan.',
                'zakat_details.*.asnaf.distinct' => 'Setiap asnaf hanya boleh dicatat satu kali.',
                'zakat_details.*.nama_penerima.required' => 'Nama penanggung jawab wajib diisi untuk asnaf yang disalurkan.',
            ]);
            $zakatDetails = $zakatValidated['zakat_details'];
            $submittedAsnaf = array_column($zakatDetails, 'asnaf');
            foreach ($allocations as $asnaf => $nominal) {
                if ($nominal > 0 && ! in_array($asnaf, $submittedAsnaf, true)) {
                    throw ValidationException::withMessages(['zakat_details' => 'Penanggung jawab untuk '.$asnafLabels[$asnaf].' wajib diisi.']);
                }
            }
            foreach ($zakatDetails as &$detail) {
                $detail['nominal'] = $allocations[$detail['asnaf']];
            }
            unset($detail);

            $periodeStart = Carbon::createFromFormat('Y-m-d', $zakatValidated['periode_zakat'].'-01')->startOfDay();
            $periodeEnd = $periodeStart->copy()->endOfMonth()->startOfDay();
            if ($validated['zakat_source'] !== 'fitrah' && ! Carbon::parse($validated['tanggal'])->isSameDay($periodeEnd)) {
                throw ValidationException::withMessages([
                    'tanggal' => 'Penyaluran reguler harus dicatat pada tanggal akhir periode, yaitu '.$periodeEnd->format('d/m/Y').'.',
                ]);
            }

            $periodeTerpakai = PeriodePenyaluranZakat::query()
                ->where('periode', $zakatValidated['periode_zakat'])->where('zakat_source', $validated['zakat_source'])
                ->where(function ($query): void {
                    $query->where('status', PeriodePenyaluranZakat::STATUS_DITUTUP)
                        ->orWhereHas('pengeluaran');
                })
                ->exists();
            if ($periodeTerpakai) {
                throw ValidationException::withMessages([
                    'periode_zakat' => 'Periode ini sudah memiliki transaksi penyaluran zakat dan telah ditutup.',
                ]);
            }

            $saldoZakat = (int) app(Psak109PostingService::class)->getSaldoZakatSources()[$validated['zakat_source']];
            if ((int) $validated['jumlah'] > $saldoZakat) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Jumlah penyaluran melebihi saldo dana zakat yang tersedia (Rp '.number_format($saldoZakat, 0, ',', '.').').',
                ]);
            }

        } else {
            $jenisDana = $validated['jenis_dana'];
            $saldoDanaAll = app(Psak109PostingService::class)->getSaldoDana();
            $saldoTersedia = (int) round((float) ($saldoDanaAll[$jenisDana] ?? 0));

            if ((int) $validated['jumlah'] > $saldoTersedia) {
                $namaDana = ucwords(str_replace('_', ' ', $jenisDana));
                throw ValidationException::withMessages([
                    'jumlah' => "Jumlah pengeluaran melebihi saldo dana {$namaDana} yang tersedia (Rp ".number_format($saldoTersedia, 0, ',', '.').').',
                ]);
            }
        }

        $saldoAkunPembayaran = app(Psak109PostingService::class)->getSaldoKasBank($coaKredit);
        if ((int) $validated['jumlah'] > (int) round($saldoAkunPembayaran)) {
            throw ValidationException::withMessages([
                'coa_kredit_id' => 'Saldo '.$coaKredit->nama_akun.' tidak mencukupi untuk pembayaran ini (tersedia Rp '
                    .number_format($saldoAkunPembayaran, 0, ',', '.').').',
            ]);
        }

        $path = null;
        $assignmentPath = null;
        $failureStep = 'simpan';

        try {
            $failureStep = 'upload_bukti_pembayaran';
            if ($request->hasFile('bukti_pembayaran')) {
                $path = app(TransactionProofStorage::class)->store(
                    $request->file('bukti_pembayaran'),
                    'bukti_pengeluaran'
                );
                if ($path === false) {
                    throw new \RuntimeException('Gagal menyimpan bukti pembayaran. Transaksi dibatalkan.');
                }
            }

            $failureStep = 'upload_dokumen_pendukung';
            if ($request->hasFile('bukti_surat_tugas')) {
                $assignmentPath = $request->file('bukti_surat_tugas')->store(
                    $isHonorarium ? 'dokumen_pendukung_honorarium' : 'bukti_surat_tugas',
                    app(TransactionProofStorage::class)->disk()
                );
                if ($assignmentPath === false) {
                    throw new \RuntimeException('Gagal menyimpan dokumen pendukung. Transaksi dibatalkan.');
                }
            }

            $failureStep = 'pencatatan_jurnal';
            DB::transaction(function () use (
                $asnafLabels,
                $assignmentPath,
                $isExternal,
                $coaDebit,
                $coaKredit,
                $path,
                $pegawai,
                $periodeEnd,
                $periodeStart,
                $saldoZakat,
                $targetAsnaf,
                $transactionCategory,
                $validated,
                $zakatDetails
            ): void {
                if ($validated['jenis_dana'] === 'zakat') {
                    Coa::whereIn('kode_akun', ['1101', '1102'])->orderBy('id')->lockForUpdate()->get();
                    $posting = app(Psak109PostingService::class);
                    $saldoZakat = (int) $posting->getSaldoZakatSources()[$validated['zakat_source']];
                    $saldoSource = $posting->getSaldoZakatSources($coaKredit)[$validated['zakat_source']];
                    if ((int) $validated['jumlah'] > min($saldoZakat, $saldoSource, $posting->getSaldoKasBank($coaKredit))) {
                        throw ValidationException::withMessages(['jumlah' => 'Saldo sumber dana zakat yang dipilih pada Kas/Bank ini tidak mencukupi.']);
                    }
                }
                if ($validated['jenis_dana'] === 'wakaf') {
                    Coa::query()->whereIn('kode_akun', ['1101', '1102'])->orderBy('id')->lockForUpdate()->get();
                    $rekap = app(Psak109PostingService::class)->getRekapWakaf();
                    $saldo = $rekap[$validated['wakaf_purpose']]['saldo'];
                    if ((int) $validated['jumlah'] > $saldo) {
                        throw ValidationException::withMessages(['jumlah' => 'Saldo wakaf untuk peruntukan yang dipilih tidak mencukupi.']);
                    }
                }

                if (config('cash_transfers.enabled')) {
                    Coa::whereIn('kode_akun', ['1101', '1102'])->orderBy('id')->lockForUpdate()->get();
                    app(\App\Services\Accounting\CashTransferService::class)->assertAvailable($coaKredit, $validated['jenis_dana'] === 'zakat' && $validated['zakat_source'] === 'fitrah' ? 'zakat_fitrah' : $validated['jenis_dana'], (int) $validated['jumlah']);
                }
                $periodeZakat = null;
                if ($transactionCategory->requires_asnaf || $transactionCategory->form_type === 'zakat_distribution') {
                    $periodeZakat = PeriodePenyaluranZakat::query()
                        ->where('periode', $validated['periode_zakat'])->where('zakat_source', $validated['zakat_source'])
                        ->lockForUpdate()
                        ->first();

                    if ($periodeZakat?->status === PeriodePenyaluranZakat::STATUS_DITUTUP
                        || $periodeZakat?->pengeluaran()->exists()) {
                        throw ValidationException::withMessages([
                            'periode_zakat' => 'Periode ini sudah ditutup atau telah memiliki transaksi penyaluran.',
                        ]);
                    }

                    $periodeZakat ??= new PeriodePenyaluranZakat;
                    $periodeZakat->fill([
                        'periode' => $validated['periode_zakat'],
                        'zakat_source' => $validated['zakat_source'],
                        'tanggal_mulai' => $periodeStart,
                        'tanggal_selesai' => $periodeEnd,
                        'persentase_amil' => 0,
                        'target_asnaf' => $targetAsnaf,
                        'saldo_sebelum_penyaluran' => $saldoZakat,
                        'status' => PeriodePenyaluranZakat::STATUS_AKTIF,
                        'created_by' => $periodeZakat->created_by ?: $this->actorId(),
                    ]);
                    $periodeZakat->save();
                }

                $pengeluaran = new Pengeluaran;
                $pengeluaran->transaction_category_id = $transactionCategory->id;
                $pengeluaran->kategori = $isExternal ? 'Honorarium Kegiatan' : $transactionCategory->name;
                $pengeluaran->jenis_dana = $validated['jenis_dana'];
                $pengeluaran->zakat_source = $validated['zakat_source'];
                $pengeluaran->wakaf_purpose = $validated['wakaf_purpose'];
                $pengeluaran->id_pegawai = $pegawai?->id;
                $pengeluaran->restriction_type = null;
                $pengeluaran->periode_penyaluran_zakat_id = $periodeZakat?->id;
                $pengeluaran->deskripsi = $validated['deskripsi'];
                $pengeluaran->jumlah = (int) $validated['jumlah'];
                $pengeluaran->tanggal = $validated['tanggal'];
                $pengeluaran->bukti_pembayaran = $path;
                $pengeluaran->bukti_surat_tugas = $assignmentPath;
                $pengeluaran->coa_debit_id = $coaDebit->id;
                $pengeluaran->coa_kredit_id = $coaKredit->id;

                $pengeluaran->jenis = 'operasional';
                $pengeluaran->nominal = (int) $validated['jumlah'];
                $pengeluaran->keterangan = $validated['deskripsi'];
                $pengeluaran->status_verifikasi = 'diterima';
                $pengeluaran->save();

                if ($periodeZakat) {
                    $pengeluaran->nomor_batch = 'ZKT-'
                        .str_replace('-', '', $periodeZakat->periode)
                        .'-'.str_pad((string) $pengeluaran->id, 5, '0', STR_PAD_LEFT);
                    $pengeluaran->saveQuietly();
                }

                foreach ($zakatDetails as $detail) {
                    $label = $asnafLabels[$detail['asnaf']];
                    $pengeluaran->zakatPenyaluran()->create([
                        'tanggal' => $validated['tanggal'],
                        'kategori_program' => 'Penyaluran Zakat - '.$label,
                        'penerima_manfaat' => $detail['nama_penerima'],
                        'jumlah_penerima' => 0,
                        'nominal' => $detail['nominal'],
                        'jenis_ziswaf_asal' => 'zakat',
                        'bukti_penyaluran' => $path,
                        'keterangan' => $validated['deskripsi'],
                        'asnaf' => $detail['asnaf'],
                        'nama_penerima' => $detail['nama_penerima'],
                        'nik_penerima' => $detail['nik_penerima'] ?? null,
                        'alamat_penerima' => $detail['alamat_penerima'] ?? null,
                        'no_hp_penerima' => $detail['no_hp_penerima'] ?? null,
                    ]);
                }

                app(Psak109PostingService::class)->postPengeluaran($pengeluaran);

                if ($periodeZakat) {
                    $periodeZakat->update([
                        'status' => PeriodePenyaluranZakat::STATUS_DITUTUP,
                        'total_disalurkan' => (int) $validated['jumlah'],
                        'saldo_akhir' => $saldoZakat - (int) $validated['jumlah'],
                        'ditutup_at' => now(),
                        'ditutup_by' => $this->actorId(),
                    ]);
                }
            });
        } catch (\Throwable $exception) {
            // Operasi SQL sudah dibatalkan oleh DB::transaction; bersihkan hanya
            // file yang BARU diunggah dalam request ini, bukan berkas lama.
            foreach ([$path, $assignmentPath] as $uploadedPath) {
                if (! $uploadedPath) {
                    continue;
                }
                try {
                    app(TransactionProofStorage::class)->delete($uploadedPath);
                } catch (\Throwable $cleanupError) {
                    report($cleanupError); // Jangan sampai menutupi penyebab asli.
                }
            }

            if ($exception instanceof ValidationException) {
                throw $exception;
            }

            report($exception); // Detail teknis dicatat pada log, bukan ditampilkan kepada pengguna.

            $message = match (true) {
                $exception instanceof \DomainException => $exception->getMessage(),
                $failureStep === 'upload_bukti_pembayaran' => 'Bukti pembayaran gagal diunggah ke penyimpanan. Periksa disk penyimpanan/R2 dan coba lagi. Tidak ada transaksi yang disimpan.',
                $failureStep === 'upload_dokumen_pendukung' => 'Dokumen pendukung gagal diunggah ke penyimpanan. Periksa disk penyimpanan/R2 dan coba lagi. Tidak ada transaksi yang disimpan.',
                default => 'Pencatatan pengeluaran atau jurnal gagal diproses. Tidak ada transaksi yang disimpan. Hubungi administrator untuk memeriksa log Laravel (tahap: pencatatan_jurnal).',
            };

            return back()
                ->withInput($request->except(['bukti_pembayaran', 'bukti_surat_tugas']))
                ->withErrors(['pengeluaran' => $message]);
        }

        return redirect()
            ->route($this->indexRoute($request))
            ->with(
                'success',
                $transactionCategory->form_type === 'zakat_distribution'
                    ? 'Batch penyaluran zakat akhir periode berhasil disalurkan, dijurnal, dan periodenya ditutup.'
                    : 'Data pengeluaran berhasil ditambahkan dan dijurnal.'
            );
    }

    public function destroy($id)
    {
        /*
         * Status gaji hanya boleh dikelola dari menu Penggajian.
         * Menghapus item virtual gaji dari halaman Pengeluaran tidak boleh
         * mengubah status pembayaran.
         */
        if (str_starts_with((string) $id, 'gaji_')) {
            return response()->json([
                'success' => false,
                'message' => 'Pengeluaran gaji tidak dapat dihapus dari menu Pengeluaran. Ubah status pembayaran melalui menu Penggajian.',
            ], 422);
        }

        $pengeluaran = Pengeluaran::findOrFail($id);
        $buktiPembayaran = $pengeluaran->bukti_pembayaran;
        $buktiSuratTugas = $pengeluaran->bukti_surat_tugas;
        $periodeZakat = $pengeluaran->periodePenyaluranZakat;

        if ($pengeluaran->jurnal_id) {
            app(Psak109PostingService::class)->reverseJurnal($pengeluaran->jurnal_id, 'Pengeluaran dihapus');
        }

        $pengeluaran->zakatPenyaluran()->delete();
        $pengeluaran->delete();

        if ($periodeZakat) {
            $periodeZakat->update([
                'status' => PeriodePenyaluranZakat::STATUS_AKTIF,
                'total_disalurkan' => 0,
                'saldo_akhir' => $periodeZakat->saldo_sebelum_penyaluran,
                'ditutup_at' => null,
                'ditutup_by' => null,
            ]);
        }

        if ($buktiPembayaran) {
            app(TransactionProofStorage::class)->delete($buktiPembayaran);
        }
        if ($buktiSuratTugas) {
            app(TransactionProofStorage::class)->delete($buktiSuratTugas);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    private function indexRoute(Request $request): string
    {
        return $request->routeIs('pegawai.keuangan.*')
            ? 'pegawai.keuangan.pengeluaran.index'
            : 'admin.pengeluaran.index';
    }

    private function normalizeTargetAsnaf(array $targets, array $allowedAsnaf): array
    {
        $normalized = collect($allowedAsnaf)
            ->mapWithKeys(fn (string $asnaf): array => [$asnaf => max(0, (float) ($targets[$asnaf] ?? 0))])
            ->all();
        $total = array_sum($normalized);

        if ($total <= 0) {
            return $normalized;
        }

        $normalized = collect($normalized)
            ->map(fn (float $value): float => round(($value / $total) * 100, 2))
            ->all();
        $difference = round(100 - array_sum($normalized), 2);
        $firstTarget = array_key_first(array_filter($normalized, fn (float $value): bool => $value > 0));

        if ($firstTarget !== null && $difference !== 0.0) {
            $normalized[$firstTarget] = round($normalized[$firstTarget] + $difference, 2);
        }

        return $normalized;
    }

    private function actorId(): ?int
    {
        return auth('admin')->id() ?? auth('pegawai')->id() ?? auth()->id();
    }
}