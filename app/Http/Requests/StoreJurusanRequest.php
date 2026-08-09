<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJurusanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'kode' => ['required', 'string', 'max:10', 'unique:jurusan,kode'],
            'nama' => ['required', 'string', 'max:255'],
        ];
    }
}
