<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'role' => ['required', Rule::enum(UserRole::class)],
            'is_active' => ['sometimes', 'boolean'],
            'nip' => ['nullable', 'string', 'max:30', Rule::unique('teachers', 'nip')->ignore(optional($user->teacher)->id)],
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

            $user = $this->route('user');
            $linked = \App\Models\Student::where('id', $this->student_id)
                ->whereNotNull('user_id')
                ->where('user_id', '!=', $user->id)
                ->exists();

            if ($linked) {
                $validator->errors()->add('student_id', 'Siswa sudah terhubung ke akun lain.');
            }
        });
    }
}
