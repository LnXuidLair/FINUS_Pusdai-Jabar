<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCoaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user(User::ROLE_ADMIN)?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $organizationId = (int) ($this->user(User::ROLE_ADMIN)?->organization_id ?? 0);

        return [
            'kode_akun' => [
                'required',
                'string',
                'max:50',
                Rule::unique('coa', 'kode_akun')
                    ->where(fn ($query) => $query->where('organization_id', $organizationId)),
            ],
            'nama_akun' => [
                'required',
                'string',
                'max:255',
                Rule::unique('coa', 'nama_akun')
                    ->where(fn ($query) => $query->where('organization_id', $organizationId)),
            ],
            'header_akun' => ['required', 'integer', 'between:1,5'],
        ];
    }
}