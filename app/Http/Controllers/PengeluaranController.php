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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
        $saldoZakat = $this->saldoZakatTersedia();
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
            'zakat_details.*.jumlah_penerima' => ['nullable', 'integer'],
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
        $validated['jenis_dana'] = $jenisDana;
        $coaKredit = ! empty($validated['coa_kredit_id'])
            ? Coa::query()
                ->whereIn('kode_akun', ['1101', '1102'])
                ->findOrFail($validated['coa_kredit_id'])
            : Coa::query()->where('kode_akun', '1101')->firstOrFail();
        $requiresEmployee = $transactionCategory->requires_employee
            || $transactionCategory->form_type === 'honorarium';
        $requiresAssignmentProof = $transactionCategory->requires_assignment_proof
            || $transactionCategory->form_type === 'honorarium';
        $pegawai = null;

        if ($requiresEmployee || $requiresAssignmentProof) {
            $honorariumValidated = $request->validate([
                'id_pegawai' => [
                    Rule::requiredIf($requiresEmployee),
                    'nullable',
                    'integer',
                    Rule::exists('pegawai', 'id')->where(
                        fn ($query) => $query->where('organization_id', $organizationId)
                    ),
                ],
                'bukti_surat_tugas' => [
                    Rule::requiredIf($requiresAssignmentProof),
                    'nullable',
                    'file',
                    'mimes:jpeg,jpg,png,pdf',
                    'max:2048',
                ],
            ]);
            $validated = array_merge($validated, $honorariumValidated);
            $pegawai = ! empty($validated['id_pegawai'])
                ? Pegawai::query()->findOrFail($validated['id_pegawai'])
                : null;
        }
        $zakatDetails = [];
        $periodeStart = null;
        $periodeEnd = null;
        $targetAsnaf = [];
        $saldoZakat = 0;

        if ($transactionCategory->requires_asnaf || $transactionCategory->form_type === 'zakat_distribution') {
            $zakatValidated = $request->validate([
                'periode_zakat' => ['required', 'date_format:Y-m'],
                'target_asnaf' => ['nullable', 'array'],
                'target_asnaf.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
                'zakat_details' => ['required', 'array', 'min:1', 'max:100'],
                'zakat_details.*.asnaf' => [
                    'required',
                    'string',
                    Rule::in(array_keys($asnafLabels)),
                ],
                'zakat_details.*.nama_penerima' => ['required', 'string', 'max:150'],
                'zakat_details.*.nik_penerima' => ['nullable', 'string', 'max:30'],
                'zakat_details.*.alamat_penerima' => ['nullable', 'string', 'max:500'],
                'zakat_details.*.no_hp_penerima' => ['nullable', 'string', 'max:30'],
                'zakat_details.*.jumlah_penerima' => ['required', 'integer', 'min:1'],
                'zakat_details.*.nominal' => ['required', 'integer', 'min:1'],
            ]);
            $zakatDetails = $zakatValidated['zakat_details'];
            $targetAsnaf = collect($zakatValidated['target_asnaf'] ?? [])
                ->only(array_keys($asnafLabels))
                ->map(fn ($value): float => (float) ($value ?? 0))
                ->all();

            if ((int) collect($zakatDetails)->sum('nominal') !== (int) $validated['jumlah']) {
                throw ValidationException::withMessages([
                    'zakat_details' => 'Total nominal seluruh penerima harus sama dengan jumlah pengeluaran.',
                ]);
            }

            $totalTarget = array_sum($targetAsnaf);
            if ($totalTarget > 0 && abs($totalTarget - 100.0) > 0.01) {
                throw ValidationException::withMessages([
                    'target_asnaf' => 'Total target seluruh asnaf harus 100% atau dikosongkan jika periode tidak menggunakan target.',
                ]);
            }

            $periodeStart = Carbon::createFromFormat('Y-m-d', $zakatValidated['periode_zakat'].'-01')->startOfDay();
            $periodeEnd = $periodeStart->copy()->endOfMonth()->startOfDay();
            if (! Carbon::parse($validated['tanggal'])->isSameDay($periodeEnd)) {
                throw ValidationException::withMessages([
                    'tanggal' => 'Penyaluran reguler harus dicatat pada tanggal akhir periode, yaitu '.$periodeEnd->format('d/m/Y').'.',
                ]);
            }

            $periodeTerpakai = PeriodePenyaluranZakat::query()
                ->where('periode', $zakatValidated['periode_zakat'])
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

            $saldoZakat = $this->saldoZakatTersedia();
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

        if ($request->hasFile('bukti_pembayaran')) {
            $path = $request
                ->file('bukti_pembayaran')
                ->store('bukti_pembayaran', 'public');
        }

        if ($request->hasFile('bukti_surat_tugas')) {
            $assignmentPath = $request
                ->file('bukti_surat_tugas')
                ->store('bukti_surat_tugas', 'public');
        }

        try {
            DB::transaction(function () use (
                $asnafLabels,
                $assignmentPath,
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
                $periodeZakat = null;
                if ($transactionCategory->requires_asnaf || $transactionCategory->form_type === 'zakat_distribution') {
                    $periodeZakat = PeriodePenyaluranZakat::query()
                        ->where('periode', $validated['periode_zakat'])
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
                $pengeluaran->kategori = $transactionCategory->name;
                $pengeluaran->jenis_dana = $validated['jenis_dana'];
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
                        'jumlah_penerima' => $detail['jumlah_penerima'],
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
            if ($path) {
                Storage::disk('public')->delete($path);
            }
            if ($assignmentPath) {
                Storage::disk('public')->delete($assignmentPath);
            }

            throw $exception;
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
            Storage::disk('public')->delete($buktiPembayaran);
        }
        if ($buktiSuratTugas) {
            Storage::disk('public')->delete($buktiSuratTugas);
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

    private function saldoZakatTersedia(): int
    {
        $saldo = app(Psak109PostingService::class)->getSaldoDana()['zakat'] ?? 0;

        return max(0, (int) round((float) $saldo));
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
