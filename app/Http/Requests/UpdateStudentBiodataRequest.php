<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentBiodataRequest extends FormRequest
{
    public function authorize(): bool
    {
        $student = $this->user()?->studentProfile;

        return $student !== null;
    }

    public function rules(): array
    {
        return [
            'alamat' => ['nullable', 'string', 'max:500'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
