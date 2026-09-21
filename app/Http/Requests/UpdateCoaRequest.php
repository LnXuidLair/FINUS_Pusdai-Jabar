<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCoaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'kode_akun' => ['required', 'string', 'max:50', Rule::unique('coa', 'kode_akun')->ignore($this->route('coa'))],
            'nama_akun' => ['required', 'string', 'max:255', Rule::unique('coa', 'nama_akun')->ignore($this->route('coa'))],
            'header_akun' => ['required', 'integer', 'between:1,5'],
        ];
    }
}
