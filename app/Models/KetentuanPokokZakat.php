<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ketentuan pokok zakat — aturan syariat yang bersifat tetap.
 *
 * Kadar persentase zakat, kriteria asnaf, dan berat fitrah bersumber
 * dari Al-Qur'an, Sunnah, serta regulasi tetap (PMA 52/2014).
 *
 * Data ini menjadi pusat acuan utama untuk perhitungan zakat.
 * Jika harga beras/emas berubah, admin cukup mengubah field nisab_rupiah
 * pada form Ketentuan Pokok ini. Setiap transaksi ziswaf akan mengambil
 * snapshot otomatis dari tabel ini untuk menjaga riwayat transaksi.
 */
class KetentuanPokokZakat extends Model
{
    public const JENIS = [
        'penghasilan' => 'Zakat Penghasilan',
        'maal' => 'Zakat Maal',
        'fitrah' => 'Zakat Fitrah',
        'pertanian_berbiaya' => 'Pertanian (Berbiaya)',
        'pertanian_alami' => 'Pertanian (Alami)',
        'peternakan' => 'Zakat Peternakan',
    ];

    public const SATUAN_KADAR = [
        'persen' => 'Persen (%)',
        'berat' => 'Berat (kg/liter)',
        'tabel' => 'Tabel (peternakan)',
    ];

    protected $table = 'ketentuan_pokok_zakat';

    protected $fillable = [
        'kode',
        'nama',
        'jenis',
        'deskripsi',
        'kadar_persentase',
        'satuan_kadar',
        'nisab_pokok',
        'nisab_rupiah',
        'haul',
        'berat_fitrah_kg',
        'berat_fitrah_liter',
        'persentase_amil',
        'target_mustahik',
        'dasar_hukum',
        'dasar_regulasi',
        'terkunci',
        'aktif',
        'updated_by',
    ];

    protected $casts = [
        'kadar_persentase' => 'decimal:2',
        'berat_fitrah_kg' => 'decimal:2',
        'berat_fitrah_liter' => 'decimal:2',
        'nisab_rupiah' => 'decimal:2',
        'persentase_amil' => 'decimal:2',
        'target_mustahik' => 'array',
        'terkunci' => 'boolean',
        'aktif' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $ketentuan): void {
            $ketentuan->persentase_amil = 0;
        });
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope: hanya ketentuan yang aktif.
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('aktif', true);
    }

    /**
     * Ambil ketentuan pokok berdasarkan jenis zakat.
     */
    public static function untukJenis(string $jenis): ?self
    {
        return static::query()
            ->where('jenis', $jenis)
            ->aktif()
            ->first();
    }

    /**
     * Ambil semua ketentuan pokok yang aktif, berurutan.
     */
    public static function semuaAktif()
    {
        return static::query()
            ->aktif()
            ->orderBy('id')
            ->get();
    }

    /**
     * Snapshot ringkas untuk disimpan ke transaksi.
     */
    public function toSnapshot(): array
    {
        return [
            'ketentuan_pokok_id' => $this->id,
            'kode' => $this->kode,
            'jenis' => $this->jenis,
            'kadar_persentase' => (float) $this->kadar_persentase,
            'nisab_pokok' => $this->nisab_pokok,
            'nisab_rupiah' => (float) $this->nisab_rupiah,
            'haul' => $this->haul,
            'berat_fitrah_kg' => $this->berat_fitrah_kg ? (float) $this->berat_fitrah_kg : null,
            'berat_fitrah_liter' => $this->berat_fitrah_liter ? (float) $this->berat_fitrah_liter : null,
            'persentase_amil' => 0.0,
            'target_mustahik' => $this->target_mustahik ?? [],
        ];
    }
}
