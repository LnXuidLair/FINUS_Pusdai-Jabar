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
            'kode_akun' => ['required', 'string', 'max:50', Rule::unique('coas', 'kode_akun')->ignore($this->route('coa'))],
            'nama_akun' => ['required', 'string', 'max:255'],
            'header_akun' => ['required', 'integer', 'between:1,5'],
        ];
    }
}
