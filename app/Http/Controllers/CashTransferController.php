<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\JurnalUmum;
use App\Services\Accounting\CashTransferService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CashTransferController extends Controller
{
    public function balances(CashTransferService $service)
    {
        if (config('cash_transfers.enabled')
            && request()->user('pegawai')?->pegawai?->hasAksesRole(\App\Models\Pegawai::AKSES_KEUANGAN)) {
            return redirect()->route('pegawai.keuangan.cash-transfers.index');
        }
        $balances = $service->balances();
        $labels = config('transaction_categories.fund_types') + ['amil' => 'Dana Amil', 'nazhir' => 'Dana Nazhir', 'non_halal' => 'Dana Non-Halal'];
        return view('admin.cash-balances', compact('balances', 'labels'));
    }

    public function index(CashTransferService $service)
    {
        abort_unless(config('cash_transfers.enabled'), 404);
        $accounts = Coa::whereIn('kode_akun', ['1101', '1102'])->orderBy('kode_akun')->get();
        $balances = $service->balances();
        $labels = config('transaction_categories.fund_types') + ['amil' => 'Dana Amil', 'nazhir' => 'Dana Nazhir', 'non_halal' => 'Dana Non-Halal'];
        $journals = JurnalUmum::where('sumber_tabel', 'kas_bank_transfer')->with('detail.coa')->latest('id')->paginate(15);
        $cancelled = JurnalUmum::where('sumber_tabel', 'kas_bank_reversal')->pluck('sumber_id')->all();
        return view('admin.cash-transfers', compact('accounts', 'balances', 'labels', 'journals', 'cancelled'));
    }

    public function store(Request $request, CashTransferService $service)
    {
        abort_unless(config('cash_transfers.enabled'), 404);
        $data = $request->validate([
            'source' => ['required', 'integer'], 'target' => ['required', 'integer', 'different:source'],
            'fund' => ['required', Rule::in(array_merge(array_keys(config('transaction_categories.fund_types')), ['amil', 'nazhir', 'non_halal']))],
            'amount' => ['required', 'integer', 'min:1'], 'date' => ['required', 'date', 'before_or_equal:today'],
            'description' => ['required', 'string', 'max:255'],
        ]);
        $service->transfer($data);
        return back()->with('success', 'Saldo berhasil dipindahkan. Total golongan dana tetap.');
    }

    public function destroy(int $id, CashTransferService $service)
    {
        abort_unless(config('cash_transfers.enabled'), 404);
        $service->cancel($id);
        return back()->with('success', 'Pemindahan dibatalkan melalui jurnal balik.');
    }
}
