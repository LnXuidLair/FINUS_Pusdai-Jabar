<?php

namespace App\Http\Controllers;

use App\Models\Penggajian;
use App\Services\PenggajianService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PenggajianController extends Controller
{
    public function index(
        Request $request,
        PenggajianService $penggajianService
    ) {
        $validated = $request->validate([
            'periode' => [
                'nullable',
                'date_format:Y-m',
            ],
        ]);

        $periode = $validated['periode']
            ?? now()->format('Y-m');

        /*
         * Admin tidak membuat penggajian.
         * Sistem otomatis membuat / memperbarui
         * data seluruh pegawai.
         */
        $penggajianService->syncPeriode($periode);

        $penggajians = Penggajian::with([
                'pegawai.gajiJabatan',
            ])
            ->where('periode', $periode)
            ->get()
            ->sortBy(
                fn ($item) =>
                    $item->pegawai?->nama_pegawai ?? ''
            )
            ->values();

        return view('penggajian.index', [
            'penggajians' => $penggajians,
            'periode' => $periode,
        ]);
    }

    public function updateStatus(
        Request $request,
        Penggajian $penggajian,
        PenggajianService $penggajianService
    ) {
        $validated = $request->validate([
            'status_penggajian' => [
                'required',
                Rule::in([
                    'belum_dibayar',
                    'sudah_dibayar',
                ]),
            ],
        ]);

        $penggajian->load('pegawai.gajiJabatan');

        if (
            ! $penggajian->pegawai
            || ! $penggajian->pegawai->gajiJabatan
        ) {
            return back()->with(
                'error',
                'Gaji jabatan pegawai belum diatur.'
            );
        }

        if (
            $validated['status_penggajian']
            === 'sudah_dibayar'
        ) {
            $penggajianService->tandaiDibayar(
                $penggajian
            );

            return redirect()
                ->route($this->indexRoute($request), [
                    'periode' => $penggajian->periode,
                ])
                ->with(
                    'success',
                    'Gaji berhasil ditandai sebagai Sudah Dibayar.'
                );
        }

        $penggajianService->tandaiBelumDibayar(
            $penggajian
        );

        return redirect()
            ->route($this->indexRoute($request), [
                'periode' => $penggajian->periode,
            ])
            ->with(
                'success',
                'Status gaji berhasil diubah menjadi Belum Dibayar.'
            );
    }

    private function indexRoute(Request $request): string
    {
        return $request->routeIs('pegawai.keuangan.*')
            ? 'pegawai.keuangan.penggajian.index'
            : 'admin.penggajian.index';
    }
}
