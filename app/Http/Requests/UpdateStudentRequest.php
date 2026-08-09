<?php

namespace App\Http\Requests;

use App\Enums\StudentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('student'));
    }

    public function rules(): array
    {
        $student = $this->route('student');

        return [
            'nis' => ['required', 'string', 'max:20', Rule::unique('students', 'nis')->ignore($student)],
            'nisn' => ['nullable', 'string', 'max:20', Rule::unique('students', 'nisn')->ignore($student)],
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
