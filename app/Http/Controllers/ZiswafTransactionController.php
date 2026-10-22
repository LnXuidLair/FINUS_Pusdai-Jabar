<?php

namespace App\Http\Controllers;

use App\Models\ZiswafPenerimaan;
use Illuminate\Http\Request;

// Pertahankan URL lama untuk bookmark dan notifikasi yang sudah tersimpan.
class ZiswafTransactionController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['q', 'payment_status', 'status_verifikasi']);
        if ($request->filled('jenis')) $filters['golongan'] = $request->input('jenis');
        return redirect()->route('admin.pemasukan.index', $filters);
    }

    public function terima(Request $request, ZiswafPenerimaan $transaksi)
    {
        $request->merge(['action' => 'terima']);
        return app(PemasukanController::class)->verifikasi($request, $transaksi);
    }

    public function tolak(Request $request, ZiswafPenerimaan $transaksi)
    {
        $request->merge(['action' => 'tolak']);
        return app(PemasukanController::class)->verifikasi($request, $transaksi);
    }
}
