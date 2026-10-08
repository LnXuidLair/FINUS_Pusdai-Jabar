<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Presensi;
use App\Services\PenggajianService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PresensiController extends Controller
{
    /**
     * Halaman presensi ADMIN.
     */
    public function index()
    {
        $presensis = Presensi::with([
                'pegawai',
                'approver',
                'inputDatangBy',
                'inputPulangBy',
                'inputStatusBy',
            ])
            ->orderByDesc('tanggal')
            ->orderByDesc('created_at')
            ->get();

        return view('presensi.index', compact('presensis'));
    }

    /**
     * Admin dapat memasukkan / melengkapi presensi tanpa dibatasi jam saat ini.
     * Waktu yang dicatat tetap divalidasi agar masuk akal untuk jenis presensinya.
     */
    public function create()
    {
        $pegawais = Pegawai::orderBy('nama_pegawai')->get();

        return view('presensi.create', [
            'pegawais' => $pegawais,
            'schedule' => $this->schedulePayload(),
        ]);
    }

    /**
     * Jalur koreksi presensi oleh Admin.
     * Admin wajib memberi bukti dan alasan koreksi.
     */
    public function store(Request $request, PenggajianService $penggajianService)
    {
        $organizationId = (int) $request->user()->organization_id;

        $base = $request->validate([
            'id_pegawai' => [
                'required',
                'integer',
                Rule::exists('pegawai', 'id')->where(
                    fn ($query) => $query->where('organization_id', $organizationId)
                ),
            ],
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'aksi_admin' => ['required', 'in:datang,pulang,lengkap,izin,sakit'],
            'keterangan' => ['required', 'string', 'max:500'],
        ], [
            'keterangan.required' => 'Alasan/keterangan input Admin wajib diisi untuk kebutuhan audit.',
        ]);

        $pegawai = Pegawai::with('gajiJabatan')->findOrFail((int) $base['id_pegawai']);
        $tanggal = Carbon::parse($base['tanggal'])->toDateString();
        $periode = Carbon::parse($tanggal)->format('Y-m');

        if ($penggajianService->periodeSudahDibayar($pegawai->id, $periode)) {
            return back()->withErrors([
                'presensi' => 'Presensi tidak dapat diubah karena gaji periode tersebut sudah dibayar.',
            ])->withInput();
        }

        $existing = Presensi::where('id_pegawai', $pegawai->id)
            ->whereDate('tanggal', $tanggal)
            ->first();

        return match ($base['aksi_admin']) {
            'datang' => $this->adminStoreDatang($request, $pegawai, $tanggal, $periode, $base['keterangan'], $existing, $penggajianService),
            'pulang' => $this->adminStorePulang($request, $pegawai, $tanggal, $periode, $base['keterangan'], $existing, $penggajianService),
            'lengkap' => $this->adminStoreLengkap($request, $pegawai, $tanggal, $periode, $base['keterangan'], $existing, $penggajianService),
            'izin', 'sakit' => $this->adminStoreKetidakhadiran($request, $pegawai, $tanggal, $periode, $base['keterangan'], $base['aksi_admin'], $existing, $penggajianService),
        };
    }

    private function adminStoreDatang(
        Request $request,
        Pegawai $pegawai,
        string $tanggal,
        string $periode,
        string $keterangan,
        ?Presensi $existing,
        PenggajianService $penggajianService
    ) {
        $validated = $request->validate([
            'jam_datang' => ['required', 'date_format:H:i'],
            'bukti_datang' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:1024'],
        ]);

        if ($existing && ($existing->status !== Presensi::STATUS_HADIR || filled($existing->jam_datang))) {
            return back()->withErrors([
                'presensi' => 'Presensi datang pada Pegawai/tanggal tersebut sudah ada atau status harinya bukan Hadir.',
            ])->withInput();
        }

        if (! $this->timeWithin($validated['jam_datang'], config('presensi.jam_kerja'))) {
            return back()->withErrors([
                'jam_datang' => 'Jam datang koreksi Admin harus berada pada jam kerja '.config('presensi.jam_kerja.start').'–'.config('presensi.jam_kerja.end').'.',
            ])->withInput();
        }

        $path = $this->storeProof($request, 'bukti_datang', $pegawai, $tanggal, 'datang', 'admin');

        try {
            DB::transaction(function () use ($request, $pegawai, $tanggal, $periode, $keterangan, $validated, $path, $penggajianService): void {
                $record = Presensi::where('id_pegawai', $pegawai->id)
                    ->whereDate('tanggal', $tanggal)
                    ->lockForUpdate()
                    ->first();

                if ($record && ($record->status !== Presensi::STATUS_HADIR || filled($record->jam_datang))) {
                    throw new \RuntimeException('Presensi datang pada Pegawai/tanggal tersebut sudah ada.');
                }

                $record ??= new Presensi([
                    'id_pegawai' => $pegawai->id,
                    'tanggal' => $tanggal,
                    'status' => Presensi::STATUS_HADIR,
                ]);

                $record->fill([
                    'status' => Presensi::STATUS_HADIR,
                    'jam_datang' => $validated['jam_datang'].':00',
                    'bukti_datang' => $path,
                    'input_datang_by' => $request->user()->id,
                    'input_datang_role' => 'admin',
                    'keterangan' => $this->appendAuditNote($record->keterangan, 'Admin (datang): '.$keterangan),
                    'is_approved' => false,
                    'approved_by' => null,
                    'approved_at' => null,
                ]);
                $record->save();

                $penggajianService->syncPegawai($pegawai, $periode);
            });
        } catch (\Throwable $exception) {
            Storage::disk($this->attendanceDisk())->delete($path);

            if ($exception instanceof \RuntimeException) {
                return back()->withErrors(['presensi' => $exception->getMessage()])->withInput();
            }

            throw $exception;
        }

        return redirect()->route('admin.presensi.index')
            ->with('success', 'Presensi datang berhasil dicatat. Record masih menunggu presensi pulang sebelum dapat di-ACC.');
    }

    private function adminStorePulang(
        Request $request,
        Pegawai $pegawai,
        string $tanggal,
        string $periode,
        string $keterangan,
        ?Presensi $existing,
        PenggajianService $penggajianService
    ) {
        $validated = $request->validate([
            'jam_pulang' => ['required', 'date_format:H:i'],
            'kondisi' => ['required', 'in:normal,pulang_awal,tugas_dinas,lembur'],
            'bukti_pulang' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:1024'],
        ]);

        if (! $existing || $existing->status !== Presensi::STATUS_HADIR || blank($existing->jam_datang)) {
            return back()->withErrors([
                'presensi' => 'Presensi datang belum tersedia. Isi presensi datang terlebih dahulu.',
            ])->withInput();
        }

        if (filled($existing->jam_pulang)) {
            return back()->withErrors(['presensi' => 'Presensi pulang pada tanggal tersebut sudah ada.'])->withInput();
        }

        $timeError = $this->validateDepartureTime(
            $this->hm($existing->jam_datang),
            $validated['jam_pulang'],
            $validated['kondisi'],
            true
        );

        if ($timeError) {
            return back()->withErrors(['jam_pulang' => $timeError])->withInput();
        }

        $path = $this->storeProof($request, 'bukti_pulang', $pegawai, $tanggal, 'pulang_'.$validated['kondisi'], 'admin');

        try {
            DB::transaction(function () use ($request, $pegawai, $tanggal, $periode, $keterangan, $validated, $path, $penggajianService): void {
                $record = Presensi::where('id_pegawai', $pegawai->id)
                    ->whereDate('tanggal', $tanggal)
                    ->lockForUpdate()
                    ->first();

                if (! $record || $record->status !== Presensi::STATUS_HADIR || blank($record->jam_datang) || filled($record->jam_pulang)) {
                    throw new \RuntimeException('Presensi tidak dapat dilengkapi. Muat ulang halaman dan periksa data terbaru.');
                }

                $record->fill([
                    'jam_pulang' => $validated['jam_pulang'].':00',
                    'kondisi' => $validated['kondisi'],
                    'bukti_pulang' => $path,
                    'input_pulang_by' => $request->user()->id,
                    'input_pulang_role' => 'admin',
                    'keterangan' => $this->appendAuditNote($record->keterangan, 'Admin (pulang): '.$keterangan),
                    'is_approved' => true,
                    'approved_by' => $request->user()->id,
                    'approved_at' => now(),
                ]);
                $record->save();

                $penggajianService->syncPegawai($pegawai, $periode);
            });
        } catch (\Throwable $exception) {
            Storage::disk($this->attendanceDisk())->delete($path);

            if ($exception instanceof \RuntimeException) {
                return back()->withErrors(['presensi' => $exception->getMessage()])->withInput();
            }

            throw $exception;
        }

        return redirect()->route('admin.presensi.index')
            ->with('success', 'Presensi pulang berhasil dilengkapi dan record otomatis di-ACC.');
    }

    private function adminStoreLengkap(
        Request $request,
        Pegawai $pegawai,
        string $tanggal,
        string $periode,
        string $keterangan,
        ?Presensi $existing,
        PenggajianService $penggajianService
    ) {
        $validated = $request->validate([
            'jam_datang' => ['required', 'date_format:H:i'],
            'jam_pulang' => ['required', 'date_format:H:i'],
            'kondisi' => ['required', 'in:normal,pulang_awal,tugas_dinas,lembur'],
            'bukti_datang' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:1024'],
            'bukti_pulang' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:1024'],
        ]);

        if ($existing) {
            return back()->withErrors([
                'presensi' => 'Data tanggal tersebut sudah ada. Gunakan aksi Isi Datang atau Isi Pulang untuk melengkapi bagian yang masih kosong.',
            ])->withInput();
        }

        if (! $this->timeWithin($validated['jam_datang'], config('presensi.jam_kerja'))) {
            return back()->withErrors([
                'jam_datang' => 'Jam datang harus berada pada jam kerja '.config('presensi.jam_kerja.start').'–'.config('presensi.jam_kerja.end').'.',
            ])->withInput();
        }

        $timeError = $this->validateDepartureTime($validated['jam_datang'], $validated['jam_pulang'], $validated['kondisi'], true);
        if ($timeError) {
            return back()->withErrors(['jam_pulang' => $timeError])->withInput();
        }

        $pathDatang = $this->storeProof($request, 'bukti_datang', $pegawai, $tanggal, 'datang', 'admin');
        $pathPulang = $this->storeProof($request, 'bukti_pulang', $pegawai, $tanggal, 'pulang_'.$validated['kondisi'], 'admin');

        try {
            DB::transaction(function () use ($request, $pegawai, $tanggal, $periode, $keterangan, $validated, $pathDatang, $pathPulang, $penggajianService): void {
                $exists = Presensi::where('id_pegawai', $pegawai->id)
                    ->whereDate('tanggal', $tanggal)
                    ->lockForUpdate()
                    ->exists();

                if ($exists) {
                    throw new \RuntimeException('Data presensi pada Pegawai/tanggal tersebut sudah ada.');
                }

                Presensi::create([
                    'id_pegawai' => $pegawai->id,
                    'tanggal' => $tanggal,
                    'status' => Presensi::STATUS_HADIR,
                    'kondisi' => $validated['kondisi'],
                    'jam_datang' => $validated['jam_datang'].':00',
                    'jam_pulang' => $validated['jam_pulang'].':00',
                    'keterangan' => 'Admin (lengkap): '.$keterangan,
                    'bukti_datang' => $pathDatang,
                    'bukti_pulang' => $pathPulang,
                    'input_datang_by' => $request->user()->id,
                    'input_datang_role' => 'admin',
                    'input_pulang_by' => $request->user()->id,
                    'input_pulang_role' => 'admin',
                    'is_approved' => true,
                    'approved_by' => $request->user()->id,
                    'approved_at' => now(),
                ]);

                $penggajianService->syncPegawai($pegawai, $periode);
            });
        } catch (\Throwable $exception) {
            Storage::disk($this->attendanceDisk())->delete([$pathDatang, $pathPulang]);

            if ($exception instanceof \RuntimeException) {
                return back()->withErrors(['presensi' => $exception->getMessage()])->withInput();
            }

            throw $exception;
        }

        return redirect()->route('admin.presensi.index')
            ->with('success', 'Presensi datang dan pulang berhasil dicatat serta otomatis di-ACC.');
    }

    private function adminStoreKetidakhadiran(
        Request $request,
        Pegawai $pegawai,
        string $tanggal,
        string $periode,
        string $keterangan,
        string $status,
        ?Presensi $existing,
        PenggajianService $penggajianService
    ) {
        $request->validate([
            'bukti_status' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:1024'],
        ]);

        if ($existing) {
            return back()->withErrors([
                'presensi' => 'Data presensi pada Pegawai/tanggal tersebut sudah ada dan tidak dapat ditimpa menjadi '.$status.'.',
            ])->withInput();
        }

        $path = $this->storeProof($request, 'bukti_status', $pegawai, $tanggal, $status, 'admin');

        try {
            DB::transaction(function () use ($request, $pegawai, $tanggal, $periode, $keterangan, $status, $path, $penggajianService): void {
                if (Presensi::where('id_pegawai', $pegawai->id)->whereDate('tanggal', $tanggal)->lockForUpdate()->exists()) {
                    throw new \RuntimeException('Data presensi pada Pegawai/tanggal tersebut sudah ada.');
                }

                Presensi::create([
                    'id_pegawai' => $pegawai->id,
                    'tanggal' => $tanggal,
                    'status' => $status,
                    'keterangan' => 'Admin ('.$status.'): '.$keterangan,
                    'bukti_status' => $path,
                    'input_status_by' => $request->user()->id,
                    'input_status_role' => 'admin',
                    'is_approved' => true,
                    'approved_by' => $request->user()->id,
                    'approved_at' => now(),
                ]);

                $penggajianService->syncPegawai($pegawai, $periode);
            });
        } catch (\Throwable $exception) {
            Storage::disk($this->attendanceDisk())->delete($path);

            if ($exception instanceof \RuntimeException) {
                return back()->withErrors(['presensi' => $exception->getMessage()])->withInput();
            }

            throw $exception;
        }

        return redirect()->route('admin.presensi.index')
            ->with('success', ucfirst($status).' Pegawai berhasil dicatat dan otomatis di-ACC.');
    }

    /** Bukti presensi untuk Admin. */
    public function adminBukti(Presensi $presensi, string $jenis)
    {
        return $this->streamProof($presensi, $jenis);
    }

    /** ACC satu record yang sudah lengkap. */
    public function approve(Request $request, Presensi $presensi, PenggajianService $penggajianService)
    {
        if ($presensi->is_approved) {
            return back()->with('success', 'Presensi tersebut sudah disetujui.');
        }

        if (! $presensi->isComplete()) {
            return back()->with('error', 'Presensi belum lengkap sehingga belum dapat di-ACC.');
        }

        $periode = Carbon::parse($presensi->tanggal)->format('Y-m');

        if ($penggajianService->periodeSudahDibayar((int) $presensi->id_pegawai, $periode)) {
            return back()->with('error', 'Presensi tidak dapat di-ACC karena gaji periode tersebut sudah dibayar.');
        }

        DB::transaction(function () use ($request, $presensi, $periode, $penggajianService): void {
            $presensi->update([
                'is_approved' => true,
                'approved_by' => $request->user()->id,
                'approved_at' => now(),
            ]);

            $pegawai = Pegawai::with('gajiJabatan')->find($presensi->id_pegawai);
            if ($pegawai) {
                $penggajianService->syncPegawai($pegawai, $periode);
            }
        });

        return back()->with('success', 'Presensi berhasil di-ACC.');
    }

    /** ACC beberapa record lengkap sekaligus. */
    public function approveBulk(Request $request, PenggajianService $penggajianService)
    {
        $validated = $request->validate([
            'presensi_ids' => ['required', 'array', 'min:1'],
            'presensi_ids.*' => ['required', 'integer', 'exists:presensi,id'],
        ], [
            'presensi_ids.required' => 'Pilih minimal satu presensi.',
            'presensi_ids.min' => 'Pilih minimal satu presensi.',
        ]);

        $presensis = Presensi::with('pegawai')
            ->whereIn('id', $validated['presensi_ids'])
            ->where('is_approved', false)
            ->get();

        if ($presensis->isEmpty()) {
            return back()->with('error', 'Tidak ada presensi yang dapat di-ACC.');
        }

        $adminId = $request->user()->id;
        $approvedCount = 0;
        $skippedCount = 0;
        $incompleteCount = 0;

        DB::transaction(function () use ($presensis, $penggajianService, $adminId, &$approvedCount, &$skippedCount, &$incompleteCount): void {
            $syncTargets = [];

            foreach ($presensis as $presensi) {
                if (! $presensi->isComplete()) {
                    $incompleteCount++;
                    continue;
                }

                $periode = Carbon::parse($presensi->tanggal)->format('Y-m');

                if ($penggajianService->periodeSudahDibayar((int) $presensi->id_pegawai, $periode)) {
                    $skippedCount++;
                    continue;
                }

                $presensi->update([
                    'is_approved' => true,
                    'approved_by' => $adminId,
                    'approved_at' => now(),
                ]);
                $approvedCount++;

                if ($presensi->pegawai) {
                    $syncTargets[$presensi->id_pegawai.'|'.$periode] = [
                        'pegawai' => $presensi->pegawai,
                        'periode' => $periode,
                    ];
                }
            }

            foreach ($syncTargets as $target) {
                $penggajianService->syncPegawai($target['pegawai'], $target['periode']);
            }
        });

        $message = "{$approvedCount} presensi berhasil di-ACC.";
        if ($incompleteCount > 0) {
            $message .= " {$incompleteCount} dilewati karena belum lengkap.";
        }
        if ($skippedCount > 0) {
            $message .= " {$skippedCount} dilewati karena periode gaji sudah dibayar.";
        }

        return back()->with('success', $message);
    }

    public function destroy($id, PenggajianService $penggajianService)
    {
        $presensi = Presensi::with('pegawai')->findOrFail($id);
        $periode = Carbon::parse($presensi->tanggal)->format('Y-m');

        if ($penggajianService->periodeSudahDibayar((int) $presensi->id_pegawai, $periode)) {
            return redirect()->route('admin.presensi.index')
                ->with('error', 'Presensi tidak dapat dihapus karena gaji periode tersebut sudah dibayar.');
        }

        $pegawai = $presensi->pegawai;
        $paths = array_filter([$presensi->bukti_datang, $presensi->bukti_pulang, $presensi->bukti_status, $presensi->bukti_kehadiran]);

        DB::transaction(function () use ($presensi, $pegawai, $periode, $penggajianService): void {
            $presensi->delete();
            if ($pegawai) {
                $penggajianService->syncPegawai($pegawai, $periode);
            }
        });

        if ($paths) {
            Storage::disk($this->attendanceDisk())->delete($paths);
        }

        return redirect()->route('admin.presensi.index')->with('success', 'Data presensi berhasil dihapus.');
    }

    /** Halaman presensi Pegawai. */
    public function pegawaiIndex(Request $request)
    {
        $pegawai = $request->user()->pegawai;
        abort_unless($pegawai, 404, 'Data pegawai belum terhubung dengan akun ini.');

        $query = Presensi::where('id_pegawai', $pegawai->id);
        $totalPresensi = (clone $query)->count();
        $totalDisetujui = (clone $query)->where('is_approved', true)->count();
        $totalMenunggu = (clone $query)->where('is_approved', false)->count();
        $totalHadirDisetujui = (clone $query)
            ->where('status', Presensi::STATUS_HADIR)
            ->whereNotNull('jam_datang')
            ->whereNotNull('jam_pulang')
            ->where('is_approved', true)
            ->whereNotNull('approved_at')
            ->distinct()
            ->count('tanggal');

        $presensis = $query->orderByDesc('tanggal')->orderByDesc('created_at')->paginate(15)->withQueryString();
        $todayPresensi = Presensi::where('id_pegawai', $pegawai->id)->whereDate('tanggal', now()->toDateString())->first();
        $actions = $this->employeeAvailableActions($pegawai, $todayPresensi);

        return view('pegawai.presensi.index', [
            'presensis' => $presensis,
            'totalPresensi' => $totalPresensi,
            'totalDisetujui' => $totalDisetujui,
            'totalMenunggu' => $totalMenunggu,
            'totalHadirDisetujui' => $totalHadirDisetujui,
            'todayPresensi' => $todayPresensi,
            'availableActions' => $actions,
            'schedule' => $this->schedulePayload(),
        ]);
    }

    public function pegawaiBukti(Request $request, Presensi $presensi, string $jenis)
    {
        $pegawai = $request->user()->pegawai;
        abort_unless($pegawai && (int) $presensi->id_pegawai === (int) $pegawai->id, 403, 'Anda tidak memiliki akses ke bukti presensi ini.');

        return $this->streamProof($presensi, $jenis);
    }

    public function pegawaiCreate(Request $request)
    {
        $pegawai = $request->user()->pegawai;
        abort_unless($pegawai, 404, 'Data pegawai belum terhubung dengan akun ini.');

        $todayPresensi = Presensi::where('id_pegawai', $pegawai->id)->whereDate('tanggal', now()->toDateString())->first();
        $available = $this->employeeAvailableActions($pegawai, $todayPresensi);
        $aksi = (string) $request->query('aksi', '');

        if (! isset($available[$aksi])) {
            return redirect()->route('pegawai.presensi.index')
                ->with('error', 'Aksi presensi tersebut sedang tidak tersedia untuk kondisi/jam saat ini.');
        }

        return view('pegawai.presensi.create', [
            'aksi' => $aksi,
            'action' => $available[$aksi],
            'todayPresensi' => $todayPresensi,
            'schedule' => $this->schedulePayload(),
        ]);
    }

    public function pegawaiStore(Request $request, PenggajianService $penggajianService)
    {
        $pegawai = $request->user()->pegawai;
        abort_unless($pegawai, 404, 'Data pegawai belum terhubung dengan akun ini.');

        $validated = $request->validate([
            'aksi' => ['required', 'in:datang,pulang,pulang_awal,tugas_dinas,lembur,izin,sakit'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'bukti' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:1024'],
        ]);

        if (in_array($validated['aksi'], ['pulang_awal', 'tugas_dinas', 'lembur', 'izin', 'sakit'], true) && blank($validated['keterangan'] ?? null)) {
            return back()->withErrors(['keterangan' => 'Keterangan wajib diisi untuk aksi presensi ini.'])->withInput();
        }

        $today = now()->toDateString();
        $periode = now()->format('Y-m');

        if ($penggajianService->periodeSudahDibayar($pegawai->id, $periode)) {
            return back()->withErrors(['presensi' => 'Presensi periode ini sudah ditutup karena gaji sudah dibayar.'])->withInput();
        }

        $todayPresensi = Presensi::where('id_pegawai', $pegawai->id)->whereDate('tanggal', $today)->first();
        $available = $this->employeeAvailableActions($pegawai, $todayPresensi);

        if (! isset($available[$validated['aksi']])) {
            return back()->withErrors(['presensi' => 'Aksi presensi tersebut sudah tidak tersedia. Muat ulang halaman presensi.'])->withInput();
        }

        $path = $this->storeProof($request, 'bukti', $pegawai, $today, $validated['aksi'], 'pegawai');

        try {
            DB::transaction(function () use ($request, $pegawai, $validated, $today, $periode, $path, $penggajianService): void {
                $record = Presensi::where('id_pegawai', $pegawai->id)
                    ->whereDate('tanggal', $today)
                    ->lockForUpdate()
                    ->first();

                $aksi = $validated['aksi'];
                $userId = $request->user()->id;
                $nowTime = now()->format('H:i:s');

                if ($aksi === 'datang') {
                    if ($record) {
                        throw new \RuntimeException('Presensi hari ini sudah memiliki data.');
                    }

                    Presensi::create([
                        'id_pegawai' => $pegawai->id,
                        'tanggal' => $today,
                        'status' => Presensi::STATUS_HADIR,
                        'jam_datang' => $nowTime,
                        'keterangan' => $validated['keterangan'] ?? null,
                        'bukti_datang' => $path,
                        'input_datang_by' => $userId,
                        'input_datang_role' => 'pegawai',
                        'is_approved' => false,
                    ]);
                } elseif (in_array($aksi, ['izin', 'sakit'], true)) {
                    if ($record) {
                        throw new \RuntimeException('Presensi hari ini sudah memiliki data sehingga tidak dapat diubah menjadi '.$aksi.'.');
                    }

                    Presensi::create([
                        'id_pegawai' => $pegawai->id,
                        'tanggal' => $today,
                        'status' => $aksi,
                        'keterangan' => $validated['keterangan'],
                        'bukti_status' => $path,
                        'input_status_by' => $userId,
                        'input_status_role' => 'pegawai',
                        'is_approved' => false,
                    ]);
                } else {
                    if (! $record || $record->status !== Presensi::STATUS_HADIR || blank($record->jam_datang) || filled($record->jam_pulang)) {
                        throw new \RuntimeException('Presensi pulang tidak dapat disimpan karena data datang belum ada atau pulang sudah tercatat.');
                    }

                    $condition = match ($aksi) {
                        'pulang_awal' => Presensi::KONDISI_PULANG_AWAL,
                        'tugas_dinas' => Presensi::KONDISI_TUGAS_DINAS,
                        'lembur' => Presensi::KONDISI_LEMBUR,
                        default => Presensi::KONDISI_NORMAL,
                    };

                    $record->update([
                        'jam_pulang' => $nowTime,
                        'kondisi' => $condition,
                        'bukti_pulang' => $path,
                        'input_pulang_by' => $userId,
                        'input_pulang_role' => 'pegawai',
                        'keterangan' => $this->appendAuditNote($record->keterangan, $validated['keterangan'] ?? null),
                        'is_approved' => false,
                        'approved_by' => null,
                        'approved_at' => null,
                    ]);
                }

                $penggajianService->syncPegawai($pegawai, $periode);
            });
        } catch (\Throwable $exception) {
            Storage::disk($this->attendanceDisk())->delete($path);

            if ($exception instanceof \RuntimeException) {
                return back()->withErrors(['presensi' => $exception->getMessage()])->withInput();
            }

            throw $exception;
        }

        $message = match ($validated['aksi']) {
            'datang' => 'Presensi datang berhasil disimpan. Jangan lupa melakukan presensi pulang.',
            'pulang' => 'Presensi pulang berhasil disimpan dan menunggu ACC Admin.',
            'pulang_awal' => 'Permohonan pulang lebih awal berhasil dikirim dan menunggu ACC Admin.',
            'tugas_dinas' => 'Tugas/panggilan dinas berhasil dicatat dan menunggu ACC Admin.',
            'lembur' => 'Presensi pulang lembur berhasil dicatat dan menunggu ACC Admin.',
            'izin' => 'Pengajuan izin berhasil dikirim dan menunggu ACC Admin.',
            'sakit' => 'Pengajuan sakit berhasil dikirim dan menunggu ACC Admin.',
        };

        return redirect()->route('pegawai.presensi.index')->with('success', $message);
    }

    /**
     * Aksi yang boleh dilakukan Pegawai saat ini.
     */
    private function employeeAvailableActions(Pegawai $pegawai, ?Presensi $todayPresensi): array
    {
        $periode = now()->format('Y-m');
        if (app(PenggajianService::class)->periodeSudahDibayar($pegawai->id, $periode)) {
            return [];
        }

        $actions = [];

        if (! $todayPresensi) {
            if ($this->isNowWithinWindow(config('presensi.datang'))) {
                $actions['datang'] = [
                    'label' => 'Presensi Datang',
                    'description' => 'Catat waktu masuk dan unggah bukti kehadiran.',
                    'icon' => 'fa-right-to-bracket',
                ];
            }

            if ($this->isNowWithinWindow(config('presensi.izin_sakit'))) {
                $actions['izin'] = [
                    'label' => 'Ajukan Izin',
                    'description' => 'Untuk izin satu hari sebelum melakukan presensi datang.',
                    'icon' => 'fa-envelope-open-text',
                ];
                $actions['sakit'] = [
                    'label' => 'Ajukan Sakit',
                    'description' => 'Laporkan sakit satu hari beserta bukti.',
                    'icon' => 'fa-notes-medical',
                ];
            }

            return $actions;
        }

        if ($todayPresensi->status !== Presensi::STATUS_HADIR || blank($todayPresensi->jam_datang) || filled($todayPresensi->jam_pulang)) {
            return [];
        }

        $now = now()->format('H:i');
        $pulangStart = config('presensi.pulang.start');
        $workEnd = config('presensi.jam_kerja.end');

        if ($now < $pulangStart && $now >= $this->hm($todayPresensi->jam_datang)) {
            $actions['pulang_awal'] = [
                'label' => 'Pulang Lebih Awal',
                'description' => 'Untuk kebutuhan mendadak/izin sebelum jam pulang. Alasan dan bukti wajib.',
                'icon' => 'fa-person-walking-arrow-right',
            ];
            $actions['tugas_dinas'] = [
                'label' => 'Tugas / Panggilan Dinas',
                'description' => 'Catat jika harus meninggalkan lokasi karena tugas resmi masjid.',
                'icon' => 'fa-briefcase',
            ];
        }

        if ($this->isNowWithinWindow(config('presensi.pulang'))) {
            $actions['pulang'] = [
                'label' => 'Presensi Pulang',
                'description' => 'Catat waktu selesai kerja dan unggah bukti.',
                'icon' => 'fa-right-from-bracket',
            ];
            $actions['tugas_dinas'] = [
                'label' => 'Tugas / Panggilan Dinas',
                'description' => 'Gunakan jika meninggalkan lokasi karena tugas resmi masjid.',
                'icon' => 'fa-briefcase',
            ];
        }

        if ($now > $workEnd && $this->isNowWithinWindow(config('presensi.lembur'))) {
            $actions['lembur'] = [
                'label' => 'Pulang Lembur',
                'description' => 'Catat waktu pulang setelah jam kerja normal.',
                'icon' => 'fa-moon',
            ];
        }

        return $actions;
    }

    private function schedulePayload(): array
    {
        return [
            'datang' => config('presensi.datang'),
            'pulang' => config('presensi.pulang'),
            'izin_sakit' => config('presensi.izin_sakit'),
            'lembur' => config('presensi.lembur'),
            'jam_kerja' => config('presensi.jam_kerja'),
        ];
    }

    private function validateDepartureTime(string $arrival, string $departure, string $condition, bool $adminCorrection = false): ?string
    {
        if ($departure <= $arrival) {
            return 'Jam pulang harus lebih akhir dari jam datang.';
        }

        if ($condition === Presensi::KONDISI_NORMAL && ! $this->timeWithin($departure, config('presensi.pulang'))) {
            return 'Pulang normal harus berada pada '.config('presensi.pulang.start').'–'.config('presensi.pulang.end').'.';
        }

        if ($condition === Presensi::KONDISI_PULANG_AWAL) {
            $work = config('presensi.jam_kerja');
            if ($departure < $work['start'] || $departure >= config('presensi.pulang.start')) {
                return 'Pulang lebih awal harus terjadi setelah jam kerja dimulai dan sebelum '.config('presensi.pulang.start').'.';
            }
        }

        if ($condition === Presensi::KONDISI_TUGAS_DINAS && ! $this->timeWithin($departure, config('presensi.jam_kerja'))) {
            return 'Waktu tugas/panggilan dinas harus berada pada jam kerja '.config('presensi.jam_kerja.start').'–'.config('presensi.jam_kerja.end').'.';
        }

        if ($condition === Presensi::KONDISI_LEMBUR && ! $this->timeWithin($departure, config('presensi.lembur'))) {
            return 'Pulang lembur harus berada pada '.config('presensi.lembur.start').'–'.config('presensi.lembur.end').'.';
        }

        return null;
    }

    private function timeWithin(string $time, ?array $window): bool
    {
        if (! is_array($window) || ! isset($window['start'], $window['end'])) {
            return false;
        }

        $hm = $this->hm($time);
        return $hm >= $window['start'] && $hm <= $window['end'];
    }

    private function isNowWithinWindow(?array $window): bool
    {
        return $this->timeWithin(now()->format('H:i'), $window);
    }

    private function hm(?string $time): string
    {
        return substr((string) $time, 0, 5);
    }

    private function appendAuditNote(?string $existing, ?string $note): ?string
    {
        $note = trim((string) $note);
        if ($note === '') {
            return $existing;
        }

        $existing = trim((string) $existing);
        return $existing === '' ? $note : $existing."\n".$note;
    }

    private function storeProof(
        Request $request,
        string $field,
        Pegawai $pegawai,
        string $tanggal,
        string $type,
        string $source
    ): string {
        $file = $request->file($field);
        $tanggalFile = Carbon::parse($tanggal)->format('dmY');
        $nama = Str::slug($pegawai->nama_pegawai, '_');
        $typeSlug = Str::slug($type, '_');
        $unique = now()->format('His').'_' . Str::lower(Str::random(6));
        $extension = $file->extension();
        $filename = "Presensi{$tanggalFile}_{$nama}_{$typeSlug}_{$source}_{$unique}.{$extension}";

        return $file->storeAs('bukti_kehadiran', $filename, $this->attendanceDisk());
    }

    private function streamProof(Presensi $presensi, string $jenis)
    {
        $path = match ($jenis) {
            'datang' => $presensi->bukti_datang ?: (($presensi->status === Presensi::STATUS_HADIR) ? $presensi->bukti_kehadiran : null),
            'pulang' => $presensi->bukti_pulang,
            'status' => $presensi->bukti_status ?: $presensi->bukti_kehadiran,
            default => null,
        };

        abort_unless($path && Storage::disk($this->attendanceDisk())->exists($path), 404, 'File bukti presensi tidak ditemukan.');

        return Storage::disk($this->attendanceDisk())->response($path, basename($path), [], 'inline');
    }

    /** Disk penyimpanan bukti presensi sesuai environment. */
    private function attendanceDisk(): string
    {
        return (string) config('filesystems.attendance_disk', 'public');
    }
}