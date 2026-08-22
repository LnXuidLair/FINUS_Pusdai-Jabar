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

class PresensiController extends Controller
{
    /**
     * Halaman presensi ADMIN.
     */
    public function index()
    {
        $presensis = Presensi::with('pegawai')
            ->orderByDesc('tanggal')
            ->orderByDesc('created_at')
            ->get();

        return view('presensi.index', compact('presensis'));
    }

    /**
     * Form tambah presensi oleh ADMIN.
     */
    public function create()
    {
        $pegawais = Pegawai::orderBy('nama_pegawai')->get();

        return view('presensi.create', compact('pegawais'));
    }

    /**
     * Simpan presensi yang dibuat ADMIN.
     * Presensi admin otomatis disetujui.
     */
    public function store(
        Request $request,
        PenggajianService $penggajianService
    ) {
        $now = now()->format('H:i');
        $jamAdmin = config('presensi.jam_admin');

        if (
            is_array($jamAdmin)
            && isset($jamAdmin['start'], $jamAdmin['end'])
            && (
                $now < $jamAdmin['start']
                || $now > $jamAdmin['end']
            )
        ) {
            return back()
                ->withErrors([
                    'presensi' =>
                        'Admin hanya dapat input presensi di jam kerja.',
                ])
                ->withInput();
        }

        $validated = $request->validate([
            'id_pegawai' => [
                'required',
                'exists:pegawai,id',
            ],
            'tanggal' => [
                'required',
                'date',
            ],
            'status' => [
                'required',
                'in:hadir,izin,lembur,sakit,tidak hadir',
            ],
            'keterangan' => [
                'nullable',
                'string',
                'max:500',
                'required_if:status,izin,lembur,sakit',
            ],
        ]);

        $periode = Carbon::parse(
            $validated['tanggal']
        )->format('Y-m');

        if (
            $penggajianService->periodeSudahDibayar(
                (int) $validated['id_pegawai'],
                $periode
            )
        ) {
            return back()
                ->withErrors([
                    'presensi' =>
                        'Presensi tidak dapat ditambahkan karena gaji periode tersebut sudah dibayar.',
                ])
                ->withInput();
        }

        DB::transaction(function () use (
            $request,
            $validated,
            $periode,
            $penggajianService
        ): void {
            Presensi::updateOrCreate(
                [
                    'id_pegawai' => $validated['id_pegawai'],
                    'tanggal' => $validated['tanggal'],
                    'status' => $validated['status'],
                ],
                [
                    'keterangan' =>
                        $validated['keterangan'] ?? null,
                    'is_approved' => true,
                    'approved_by' => $request->user()->id,
                    'approved_at' => now(),
                ]
            );

            $pegawai = Pegawai::with('gajiJabatan')
                ->findOrFail($validated['id_pegawai']);

            $penggajianService->syncPegawai(
                $pegawai,
                $periode
            );
        });

        return redirect()
            ->route('admin.presensi.index')
            ->with(
                'success',
                'Presensi berhasil disimpan dan otomatis disetujui.'
            );
    }

    /**
     * Form edit presensi oleh ADMIN.
     */
    public function edit($id)
    {
        $presensi = Presensi::findOrFail($id);

        $pegawais = Pegawai::orderBy(
            'nama_pegawai'
        )->get();

        return view(
            'presensi.edit',
            compact('presensi', 'pegawais')
        );
    }

    /**
     * Update presensi oleh ADMIN.
     * Hasil edit otomatis dianggap disetujui admin.
     */
    public function update(
        Request $request,
        $id,
        PenggajianService $penggajianService
    ) {
        $presensi = Presensi::findOrFail($id);

        $validated = $request->validate([
            'id_pegawai' => [
                'required',
                'exists:pegawai,id',
            ],
            'tanggal' => [
                'required',
                'date',
            ],
            'status' => [
                'required',
                'in:hadir,izin,lembur,sakit,tidak hadir',
            ],
            'keterangan' => [
                'nullable',
                'string',
                'max:500',
                'required_if:status,izin,lembur,sakit',
            ],
        ]);

        $pegawaiLamaId = (int) $presensi->id_pegawai;
        $periodeLama = Carbon::parse(
            $presensi->tanggal
        )->format('Y-m');

        $pegawaiBaruId = (int) $validated['id_pegawai'];
        $periodeBaru = Carbon::parse(
            $validated['tanggal']
        )->format('Y-m');

        if (
            $penggajianService->periodeSudahDibayar(
                $pegawaiLamaId,
                $periodeLama
            )
        ) {
            return back()
                ->with('error', 'Presensi tidak dapat diedit karena gaji periode tersebut sudah dibayar.')
                ->withInput();
        }

        if (
            (
                $pegawaiBaruId !== $pegawaiLamaId
                || $periodeBaru !== $periodeLama
            )
            && $penggajianService->periodeSudahDibayar(
                $pegawaiBaruId,
                $periodeBaru
            )
        ) {
            return back()
                ->with('error', 'Presensi tidak dapat dipindahkan ke periode yang sudah dibayar.')
                ->withInput();
        }

        DB::transaction(function () use (
            $request,
            $presensi,
            $validated,
            $pegawaiLamaId,
            $periodeLama,
            $pegawaiBaruId,
            $periodeBaru,
            $penggajianService
        ): void {
            $presensi->update([
                'id_pegawai' => $validated['id_pegawai'],
                'tanggal' => $validated['tanggal'],
                'status' => $validated['status'],
                'keterangan' =>
                    $validated['keterangan'] ?? null,
                'is_approved' => true,
                'approved_by' => $request->user()->id,
                'approved_at' => now(),
            ]);

            $pegawaiLama = Pegawai::with('gajiJabatan')
                ->find($pegawaiLamaId);

            if ($pegawaiLama) {
                $penggajianService->syncPegawai(
                    $pegawaiLama,
                    $periodeLama
                );
            }

            if (
                $pegawaiBaruId !== $pegawaiLamaId
                || $periodeBaru !== $periodeLama
            ) {
                $pegawaiBaru = Pegawai::with('gajiJabatan')
                    ->find($pegawaiBaruId);

                if ($pegawaiBaru) {
                    $penggajianService->syncPegawai(
                        $pegawaiBaru,
                        $periodeBaru
                    );
                }
            }
        });

        return redirect()
            ->route('admin.presensi.index')
            ->with(
                'success',
                'Presensi berhasil diperbarui dan disetujui.'
            );
    }

    /**
     * ACC satu presensi pegawai.
     */
    public function approve(
        Request $request,
        Presensi $presensi,
        PenggajianService $penggajianService
    ) {
        if ($presensi->is_approved) {
            return back()->with(
                'success',
                'Presensi tersebut sudah disetujui.'
            );
        }

        $periode = Carbon::parse(
            $presensi->tanggal
        )->format('Y-m');

        if (
            $penggajianService->periodeSudahDibayar(
                (int) $presensi->id_pegawai,
                $periode
            )
        ) {
            return back()->with(
                'error',
                'Presensi tidak dapat di-ACC karena gaji periode tersebut sudah dibayar.'
            );
        }

        DB::transaction(function () use (
            $request,
            $presensi,
            $periode,
            $penggajianService
        ): void {
            $presensi->update([
                'is_approved' => true,
                'approved_by' => $request->user()->id,
                'approved_at' => now(),
            ]);

            $pegawai = Pegawai::with('gajiJabatan')
                ->find($presensi->id_pegawai);

            if ($pegawai) {
                $penggajianService->syncPegawai(
                    $pegawai,
                    $periode
                );
            }
        });

        return back()->with(
            'success',
            'Presensi berhasil di-ACC.'
        );
    }

    /**
     * ACC beberapa presensi sekaligus.
     * Mendukung checkbox dan Select All.
     */
    public function approveBulk(
        Request $request,
        PenggajianService $penggajianService
    ) {
        $validated = $request->validate([
            'presensi_ids' => [
                'required',
                'array',
                'min:1',
            ],
            'presensi_ids.*' => [
                'required',
                'integer',
                'exists:presensi,id',
            ],
        ], [
            'presensi_ids.required' =>
                'Pilih minimal satu presensi.',
            'presensi_ids.min' =>
                'Pilih minimal satu presensi.',
        ]);

        $presensis = Presensi::with('pegawai')
            ->whereIn(
                'id',
                $validated['presensi_ids']
            )
            ->where('is_approved', false)
            ->get();

        if ($presensis->isEmpty()) {
            return back()->with(
                'error',
                'Tidak ada presensi yang dapat di-ACC.'
            );
        }

        $adminId = $request->user()->id;
        $approvedCount = 0;
        $skippedCount = 0;

        DB::transaction(function () use (
            $presensis,
            $penggajianService,
            $adminId,
            &$approvedCount,
            &$skippedCount
        ): void {
            $syncTargets = [];

            foreach ($presensis as $presensi) {
                $periode = Carbon::parse(
                    $presensi->tanggal
                )->format('Y-m');

                if (
                    $penggajianService->periodeSudahDibayar(
                        (int) $presensi->id_pegawai,
                        $periode
                    )
                ) {
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
                    $key =
                        $presensi->id_pegawai
                        . '|'
                        . $periode;

                    $syncTargets[$key] = [
                        'pegawai' => $presensi->pegawai,
                        'periode' => $periode,
                    ];
                }
            }

            /*
             * Sinkronisasi penggajian berada dalam transaksi yang sama.
             * Jika perhitungan gagal, approval presensi ikut dibatalkan.
             */
            foreach ($syncTargets as $target) {
                $penggajianService->syncPegawai(
                    $target['pegawai'],
                    $target['periode']
                );
            }
        });

        $message =
            "{$approvedCount} presensi berhasil di-ACC.";

        if ($skippedCount > 0) {
            $message .=
                " {$skippedCount} presensi dilewati "
                . 'karena periode penggajian sudah dibayar.';
        }

        return back()->with(
            'success',
            $message
        );
    }

    /**
     * Hapus presensi ADMIN.
     */
    public function destroy(
        $id,
        PenggajianService $penggajianService
    ) {
        $presensi = Presensi::with('pegawai')
            ->findOrFail($id);

        $periode = Carbon::parse(
            $presensi->tanggal
        )->format('Y-m');

        if (
            $penggajianService->periodeSudahDibayar(
                (int) $presensi->id_pegawai,
                $periode
            )
        ) {
            return redirect()
                ->route('admin.presensi.index')
                ->with(
                    'error',
                    'Presensi tidak dapat dihapus karena gaji periode tersebut sudah dibayar.'
                );
        }

        $pegawai = $presensi->pegawai;
        $buktiKehadiran = $presensi->bukti_kehadiran;

        DB::transaction(function () use (
            $presensi,
            $pegawai,
            $periode,
            $penggajianService
        ): void {
            $presensi->delete();

            if ($pegawai) {
                $penggajianService->syncPegawai(
                    $pegawai,
                    $periode
                );
            }
        });

        if ($buktiKehadiran) {
            Storage::disk('public')->delete(
                $buktiKehadiran
            );
        }

        return redirect()
            ->route('admin.presensi.index')
            ->with(
                'success',
                'Data presensi berhasil dihapus.'
            );
    }

    /**
     * Halaman presensi PEGAWAI.
     */
    public function pegawaiIndex(Request $request)
    {
        $pegawai = $request->user()->pegawai;

        abort_unless(
            $pegawai,
            404,
            'Data pegawai belum terhubung dengan akun ini.'
        );

        $query = Presensi::where(
            'id_pegawai',
            $pegawai->id
        );

        $totalPresensi = (clone $query)->count();

        $totalDisetujui = (clone $query)
            ->where('is_approved', true)
            ->count();

        $totalMenunggu = (clone $query)
            ->where('is_approved', false)
            ->count();

        $totalHadirDisetujui = (clone $query)
            ->where('status', 'hadir')
            ->where('is_approved', true)
            ->whereNotNull('approved_at')
            ->distinct()
            ->count('tanggal');

        $presensis = $query
            ->orderByDesc('tanggal')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view(
            'pegawai.presensi.index',
            compact(
                'presensis',
                'totalPresensi',
                'totalDisetujui',
                'totalMenunggu',
                'totalHadirDisetujui'
            )
        );
    }

    /**
     * Form presensi PEGAWAI.
     */
    public function pegawaiCreate()
    {
        return view('pegawai.presensi.create');
    }

    /**
     * Simpan presensi PEGAWAI.
     * Selalu is_approved = false.
     */
    public function pegawaiStore(
        Request $request,
        PenggajianService $penggajianService
    ) {
        $pegawai = $request->user()->pegawai;

        abort_unless(
            $pegawai,
            404,
            'Data pegawai belum terhubung dengan akun ini.'
        );

        $validated = $request->validate([
            'status' => [
                'required',
                'in:hadir,izin,lembur',
            ],
            'keterangan' => [
                'nullable',
                'string',
                'max:500',
                'required_if:status,izin,lembur',
            ],
            'bukti_kehadiran' => [
                'required',
                'image',
                'mimes:jpeg,jpg,png',
                'max:1024',
            ],
        ]);

        $today = now()->toDateString();
        $now = now()->format('H:i');
        $periode = now()->format('Y-m');

        if (
            $penggajianService->periodeSudahDibayar(
                $pegawai->id,
                $periode
            )
        ) {
            return back()->withErrors([
                'presensi' =>
                    'Presensi periode ini sudah ditutup karena gaji sudah dibayar.',
            ]);
        }

        $count = Presensi::where(
                'id_pegawai',
                $pegawai->id
            )
            ->whereDate('tanggal', $today)
            ->count();

        if ($count >= 3) {
            return back()->withErrors([
                'presensi' =>
                    'Presensi hari ini sudah lengkap.',
            ]);
        }

        $allowed = false;

        foreach ((array) config('presensi', []) as $key => $jam) {
            if ($key === 'jam_admin' || ! is_array($jam)) {
                continue;
            }

            if (! isset($jam['start'], $jam['end'])) {
                continue;
            }

            if (
                $now >= $jam['start']
                && $now <= $jam['end']
            ) {
                $allowed = true;
                break;
            }
        }

        if (
            ! $allowed
            && $validated['status'] !== 'lembur'
        ) {
            return back()->withErrors([
                'presensi' =>
                    'Presensi di luar jam yang diizinkan.',
            ]);
        }

        $tanggalFile = now()->format('dmY');
        $nama = Str::slug(
            $pegawai->nama_pegawai,
            '_'
        );
        $jamKe = $count + 1;

        $extension = $request
            ->file('bukti_kehadiran')
            ->extension();

        $filename =
            "Presensi{$tanggalFile}_"
            . "{$nama}_"
            . "{$validated['status']}{$jamKe}."
            . $extension;

        $path = $request
            ->file('bukti_kehadiran')
            ->storeAs(
                'bukti_kehadiran',
                $filename,
                'public'
            );

        try {
            DB::transaction(function () use (
                $pegawai,
                $validated,
                $today,
                $path,
                $periode,
                $penggajianService
            ): void {
                Presensi::create([
                    'id_pegawai' => $pegawai->id,
                    'tanggal' => $today,
                    'status' => $validated['status'],
                    'keterangan' =>
                        $validated['keterangan'] ?? null,
                    'bukti_kehadiran' => $path,
                    'is_approved' => false,
                    'approved_by' => null,
                    'approved_at' => null,
                ]);

                /*
                 * Penggajian boleh disiapkan, tetapi record pending
                 * belum dihitung karena is_approved masih false.
                 */
                $penggajianService->syncPegawai(
                    $pegawai,
                    $periode
                );
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($path);
            throw $exception;
        }

        return redirect()
            ->route('pegawai.presensi.index')
            ->with(
                'success',
                'Presensi berhasil dikirim dan menunggu persetujuan admin.'
            );
    }
}
