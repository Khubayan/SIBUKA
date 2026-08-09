<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJurusanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'kode' => ['required', 'string', 'max:10', Rule::unique('jurusan', 'kode')->ignore($this->route('jurusan'))],
            'nama' => ['required', 'string', 'max:255'],
        ];
    }
}
