<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKelasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'jurusan_id' => ['required', 'exists:jurusan,id'],
            'nama' => ['required', 'string', 'max:50'],
            'tingkat' => ['required', Rule::in(['X', 'XI', 'XII'])],
            'wali_kelas_id' => ['nullable', 'exists:users,id'],
            'tahun_ajaran' => ['required', 'string', 'max:9'],
        ];
    }
}
