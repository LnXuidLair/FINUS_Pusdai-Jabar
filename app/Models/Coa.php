<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coa extends Model
{
    protected $table = 'coa';

    protected $fillable = [
        'kode_akun',
        'nama_akun',
        'header_akun',
    ];

    public function jurnalDetail()
    {
        return $this->hasMany(JurnalDetail::class, 'coa_id');
    }

    public function scopePengeluaranManual($query)
    {
        $kodeAkun = collect(config('coa.manual_expense_accounts', []))
            ->flatMap(fn (array $accounts) => array_keys($accounts))
            ->values()
            ->all();

        return $query
            ->where('header_akun', 5)
            ->whereIn('kode_akun', $kodeAkun);
    }

    public function getKelompokPengeluaranAttribute(): ?string
    {
        foreach (config('coa.manual_expense_accounts', []) as $group => $accounts) {
            if (array_key_exists($this->kode_akun, $accounts)) {
                return $group;
            }
        }

        return null;
    }

    public function getPenjelasanPengeluaranAttribute(): ?string
    {
        $group = $this->kelompok_pengeluaran;

        return $group
            ? config("coa.manual_expense_accounts.{$group}.{$this->kode_akun}")
            : null;
    }

    public function getLabelHeaderAkunAttribute()
    {
        return match ((int) $this->header_akun) {
            1 => 'Aset',
            2 => 'Kewajiban',
            3 => 'Ekuitas',
            4 => 'Pendapatan',
            5 => 'Beban',
            default => 'Kosong',
        };
    }

    public function getWarnaHeaderAkunAttribute()
    {
        return match ((int) $this->header_akun) {
            1 => 'green',
            2 => 'red',
            3 => 'blue',
            4 => 'purple',
            5 => 'orange',
            default => 'gray',
        };
    }

    public function getIconHeaderAkunAttribute()
    {
        return match ((int) $this->header_akun) {
            1 => 'fa-landmark',
            2 => 'fa-file-invoice-dollar',
            3 => 'fa-balance-scale',
            4 => 'fa-arrow-trend-up',
            5 => 'fa-money-bill-wave',
            default => 'fa-circle',
        };
    }
}
