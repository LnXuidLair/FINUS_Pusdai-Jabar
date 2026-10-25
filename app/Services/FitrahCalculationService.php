<?php
namespace App\Services;

use App\Models\BarangZakat;
use App\Models\HargaBarangZakat;
use App\Models\KetentuanPokokZakat;
use Illuminate\Validation\ValidationException;

class FitrahCalculationService
{
    public function activeRicePrice(): ?HargaBarangZakat
    {
        $beras = BarangZakat::query()->where('aktif', true)
            ->where(fn ($q) => $q->whereRaw('LOWER(nama) = ?', ['beras'])->orWhereRaw('LOWER(kode) = ?', ['beras']))->first();
        return $beras?->harga()->berlakuPada()->latest('berlaku_mulai')->latest('id')->first();
    }

    public function configuration(): array
    {
        $policy = KetentuanPokokZakat::untukJenis('fitrah');
        $price = $this->activeRicePrice();
        return [
            'ready' => $policy && $price && (float) $policy->berat_fitrah_kg > 0 && $price->harga_per_satuan > 0,
            'kg' => (float) ($policy?->berat_fitrah_kg ?? 0),
            'price' => (int) ($price?->harga_per_satuan ?? 0),
        ];
    }

    public function calculate(int $people): array
    {
        $policy = KetentuanPokokZakat::untukJenis('fitrah');
        $price = $this->activeRicePrice();
        if (! $policy || ! $price || (float) $policy->berat_fitrah_kg <= 0 || $price->harga_per_satuan <= 0) {
            throw ValidationException::withMessages(['jumlah_jiwa' => 'Ketentuan fitrah dan harga beras yang disetujui serta berlaku harus tersedia. Periksa pengaturan zakat.']);
        }
        $details = app(ZakatCalculatorService::class)->fitrah(['jumlah_jiwa' => $people], $policy, $price->harga_per_satuan);
        $snapshot = $policy->toSnapshot();
        $snapshot['harga_beras'] = [
            'harga_barang_zakat_id' => $price->id, 'barang_zakat_id' => $price->barang_zakat_id,
            'harga_per_kg' => (int) $price->harga_per_satuan, 'wilayah' => $price->wilayah,
            'berlaku_mulai' => $price->berlaku_mulai?->toDateString(), 'sumber_harga' => $price->sumber_harga,
        ];
        return ['nominal' => (int) round($details['jumlah_zakat']), 'details' => $details, 'snapshot' => $snapshot];
    }
}
