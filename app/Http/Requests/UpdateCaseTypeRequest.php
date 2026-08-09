<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCaseTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'kode' => ['required', 'string', 'max:20', Rule::unique('case_types', 'kode')->ignore($this->route('case_type'))],
            'nama' => ['required', 'string', 'max:255'],
            'kategori' => ['required', Rule::in(['pelanggaran', 'prestasi'])],
            'poin_default' => ['required', 'integer', 'min:1', 'max:1000'],
            'deskripsi' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
