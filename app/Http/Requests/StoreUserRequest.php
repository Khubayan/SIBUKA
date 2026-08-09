<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => ['required', Rule::enum(UserRole::class)],
            'nip' => ['nullable', 'string', 'max:30', 'unique:teachers,nip'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'hubungan' => ['nullable', Rule::in(['ayah', 'ibu', 'wali'])],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['exists:students,id'],
            'student_id' => [
                'nullable',
                Rule::requiredIf($this->input('role') === UserRole::Siswa->value),
                'exists:students,id',
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->input('role') !== UserRole::Siswa->value || ! $this->student_id) {
                return;
            }

            $linked = \App\Models\Student::where('id', $this->student_id)
                ->whereNotNull('user_id')
                ->exists();

            if ($linked) {
                $validator->errors()->add('student_id', 'Siswa sudah terhubung ke akun lain.');
            }
        });
    }
}
