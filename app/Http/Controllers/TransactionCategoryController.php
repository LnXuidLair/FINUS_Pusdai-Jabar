<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\TransactionCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TransactionCategoryController extends Controller
{
    public function index()
    {
        TransactionCategory::ensureDefaults();

        return view('transaction-categories.index', [
            'categories' => TransactionCategory::query()
                ->with('coa')
                ->orderBy('transaction_type')
                ->orderBy('group')
                ->orderBy('name')
                ->get(),
            'fundLabels' => config('transaction_categories.fund_types', []),
            'formLabels' => config('transaction_categories.form_types', []),
        ]);
    }

    public function create()
    {
        return view('transaction-categories.form', $this->formData(new TransactionCategory));
    }

    public function store(Request $request)
    {
        TransactionCategory::create($this->validatedData($request));

        return redirect()
            ->route('admin.transaction-categories.index')
            ->with('success', 'Kategori transaksi berhasil ditambahkan.');
    }

    public function edit(TransactionCategory $transactionCategory)
    {
        return view('transaction-categories.form', $this->formData($transactionCategory));
    }

    public function update(Request $request, TransactionCategory $transactionCategory)
    {
        $transactionCategory->update($this->validatedData($request, $transactionCategory));

        return redirect()
            ->route('admin.transaction-categories.index')
            ->with('success', 'Kategori transaksi berhasil diperbarui.');
    }

    public function destroy(TransactionCategory $transactionCategory)
    {
        if ($transactionCategory->pengeluaran()->exists()) {
            return back()->with('error', 'Kategori sudah digunakan. Nonaktifkan kategori agar histori transaksi tetap utuh.');
        }

        $transactionCategory->delete();

        return back()->with('success', 'Kategori transaksi berhasil dihapus.');
    }

    private function formData(TransactionCategory $category): array
    {
        return [
            'category' => $category,
            'accounts' => Coa::query()->pengeluaranManual()->orderBy('kode_akun')->get(),
            'fundLabels' => config('transaction_categories.fund_types', []),
            'formLabels' => config('transaction_categories.form_types', []),
        ];
    }

    private function validatedData(Request $request, ?TransactionCategory $category = null): array
    {
        $organizationId = (int) $request->user('admin')->organization_id;
        $formTypes = array_keys(config('transaction_categories.form_types', []));

        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('transaction_categories', 'code')
                    ->where(fn ($query) => $query->where('organization_id', $organizationId))
                    ->ignore($category?->id),
            ],
            'name' => ['required', 'string', 'max:150'],
            'coa_id' => [
                'required',
                'integer',
                Rule::exists('coa', 'id')->where(
                    fn ($query) => $query
                        ->where('organization_id', $organizationId)
                        ->whereIn('kode_akun', $this->allowedAccountCodes())
                ),
            ],
            'form_type' => ['required', Rule::in($formTypes)],
            'requires_asnaf' => ['nullable', 'boolean'],
            'requires_employee' => ['nullable', 'boolean'],
            'requires_assignment_proof' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $coa = Coa::query()->findOrFail($validated['coa_id']);
        $fundType = TransactionCategory::fundTypeForAccountCode($coa->kode_akun);
        if (! $fundType) {
            throw ValidationException::withMessages([
                'coa_id' => 'Kode akun belum memiliki aturan sumber dana. Hubungi administrator.',
            ]);
        }
        $validated['transaction_type'] = 'pengeluaran';
        $validated['group'] = $fundType;
        $validated['default_fund_type'] = $fundType;
        $validated['allowed_fund_types'] = [$fundType];

        foreach (['requires_asnaf', 'requires_employee', 'requires_assignment_proof', 'is_active'] as $field) {
            $validated[$field] = $request->boolean($field);
        }
        $validated['requires_restriction'] = false;
        if ($validated['form_type'] === 'zakat_distribution') {
            $validated['requires_asnaf'] = true;
        }
        if ($validated['form_type'] === 'honorarium') {
            $validated['requires_employee'] = true;
            $validated['requires_assignment_proof'] = true;
        }
        $validated['is_system'] = $category?->is_system ?? false;

        return $validated;
    }

    private function allowedAccountCodes(): array
    {
        return collect(config('coa.manual_expense_accounts', []))
            ->flatMap(fn (array $accounts) => array_keys($accounts))
            ->values()
            ->all();
    }
}
