<?php

namespace App\Http\Requests;

use App\Enums\StudentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Student::class);
    }

    public function rules(): array
    {
        return [
            'nis' => ['required', 'string', 'max:20', 'unique:students,nis'],
            'nisn' => ['nullable', 'string', 'max:20', 'unique:students,nisn'],
            'nama' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'alamat' => ['nullable', 'string'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'kelas_id' => ['required', 'exists:kelas,id'],
            'status' => ['required', Rule::enum(StudentStatus::class)],
            'foto' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
