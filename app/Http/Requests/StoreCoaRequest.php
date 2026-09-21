<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCoaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'kode_akun' => ['required', 'string', 'max:50', 'unique:coa,kode_akun'],
            'nama_akun' => ['required', 'string', 'max:255', Rule::unique('coa', 'nama_akun')],
            'header_akun' => ['required', 'integer', 'between:1,5'],
        ];
    }
}
