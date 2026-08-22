<?php

namespace App\Services;

use App\Models\Pegawai;
use App\Models\Penggajian;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PenggajianService
{
    /**
     * Membuat / memperbarui penggajian seluruh pegawai
     * untuk periode tertentu.
     */
    public function syncPeriode(string $periode): void
    {
        // Validasi format periode sekaligus.
        $this->periodeDate($periode);

        $pegawais = Pegawai::with('gajiJabatan')->get();

        foreach ($pegawais as $pegawai) {
            $this->syncPegawai($pegawai, $periode);
        }
    }

    /**
     * Sinkronisasi gaji satu pegawai.
     *
     * Selama belum dibayar:
     * - jumlah kehadiran mengikuti presensi ACC
     * - gaji per hari mengikuti Gaji Jabatan
     * - total gaji dihitung otomatis
     *
     * Setelah dibayar:
     * - data tidak dihitung ulang
     */
    public function syncPegawai(
        Pegawai $pegawai,
        string $periode
    ): Penggajian {
        $existing = Penggajian::where(
                'id_pegawai',
                $pegawai->id
            )
            ->where('periode', $periode)
            ->first();

        if (
            $existing
            && $existing->status_penggajian === 'sudah_dibayar'
        ) {
            return $existing;
        }

        $hasil = $this->hitung($pegawai, $periode);

        return Penggajian::updateOrCreate(
            [
                'id_pegawai' => $pegawai->id,
                'periode' => $periode,
            ],
            [
                'jumlah_hari' => $hasil['jumlah_hari'],
                'jumlah_kehadiran' => $hasil['jumlah_kehadiran'],
                'gaji_perhari' => $hasil['gaji_perhari'],
                'total_gaji' => $hasil['total_gaji'],
                'status_penggajian' => 'belum_dibayar',
                'tanggal' => null,
            ]
        );
    }

    /**
     * Tandai penggajian sebagai sudah dibayar.
     *
     * Tepat sebelum dibayar, data dihitung kembali
     * agar menggunakan presensi ACC paling terbaru.
     */
    public function tandaiDibayar(
        Penggajian $penggajian
    ): Penggajian {
        return DB::transaction(function () use ($penggajian) {
            $penggajian = Penggajian::lockForUpdate()
                ->findOrFail($penggajian->id);

            if (
                $penggajian->status_penggajian
                === 'sudah_dibayar'
            ) {
                return $penggajian;
            }

            $pegawai = Pegawai::with('gajiJabatan')
                ->findOrFail($penggajian->id_pegawai);

            $hasil = $this->hitung(
                $pegawai,
                $penggajian->periode
            );

            $penggajian->update([
                'jumlah_hari' => $hasil['jumlah_hari'],
                'jumlah_kehadiran' => $hasil['jumlah_kehadiran'],
                'gaji_perhari' => $hasil['gaji_perhari'],
                'total_gaji' => $hasil['total_gaji'],
                'status_penggajian' => 'sudah_dibayar',
                'tanggal' => now()->toDateString(),
            ]);

            return $penggajian->fresh();
        });
    }

    /**
     * Mengembalikan status pembayaran menjadi belum dibayar.
     */
    public function tandaiBelumDibayar(
        Penggajian $penggajian
    ): Penggajian {
        return DB::transaction(function () use ($penggajian) {
            $penggajian = Penggajian::lockForUpdate()
                ->findOrFail($penggajian->id);

            $pegawai = Pegawai::with('gajiJabatan')
                ->findOrFail($penggajian->id_pegawai);

            $hasil = $this->hitung(
                $pegawai,
                $penggajian->periode
            );

            $penggajian->update([
                'jumlah_hari' => $hasil['jumlah_hari'],
                'jumlah_kehadiran' => $hasil['jumlah_kehadiran'],
                'gaji_perhari' => $hasil['gaji_perhari'],
                'total_gaji' => $hasil['total_gaji'],
                'status_penggajian' => 'belum_dibayar',
                'tanggal' => null,
            ]);

            return $penggajian->fresh();
        });
    }

    /**
     * Digunakan untuk mengunci presensi yang periodenya
     * sudah selesai dibayar.
     */
    public function periodeSudahDibayar(
        int $pegawaiId,
        string $periode
    ): bool {
        return Penggajian::where(
                'id_pegawai',
                $pegawaiId
            )
            ->where('periode', $periode)
            ->where(
                'status_penggajian',
                'sudah_dibayar'
            )
            ->exists();
    }

    /**
     * Perhitungan gaji.
     */
    private function hitung(
        Pegawai $pegawai,
        string $periode
    ): array {
        $tanggalPeriode = $this->periodeDate($periode);

        $pegawai->loadMissing('gajiJabatan');

        $gajiPerhari = (int) (
            $pegawai->gajiJabatan?->gaji_perhari ?? 0
        );

        /*
         * Yang dihitung:
         * - status hadir
         * - sudah di-ACC admin
         * - approved_at sudah terisi
         * - satu tanggal hanya dihitung satu kali
         *
         * Jadi walaupun ada 2 atau 3 record "hadir"
         * pada tanggal yang sama, gajinya tetap satu hari.
         */
        $jumlahKehadiran = Presensi::query()
            ->where('id_pegawai', $pegawai->id)
            ->whereYear(
                'tanggal',
                $tanggalPeriode->year
            )
            ->whereMonth(
                'tanggal',
                $tanggalPeriode->month
            )
            ->where('status', 'hadir')
            ->where('is_approved', true)
            ->whereNotNull('approved_at')
            ->distinct()
            ->count('tanggal');

        return [
            'jumlah_hari' => $tanggalPeriode->daysInMonth,
            'jumlah_kehadiran' => $jumlahKehadiran,
            'gaji_perhari' => $gajiPerhari,
            'total_gaji' => $jumlahKehadiran * $gajiPerhari,
        ];
    }

    private function periodeDate(string $periode): Carbon
    {
        return Carbon::createFromFormat(
            'Y-m-d',
            $periode . '-01'
        )->startOfMonth();
    }
}