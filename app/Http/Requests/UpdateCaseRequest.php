<?php

namespace App\Http\Requests;

use App\Enums\CaseCategory;
use App\Enums\CaseStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('caseRecord'));
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'case_type_id' => ['required', 'exists:case_types,id'],
            'kategori' => ['required', Rule::enum(CaseCategory::class)],
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'poin' => ['required', 'integer', 'min:1', 'max:1000'],
            'tanggal_kejadian' => ['required', 'date', 'before_or_equal:today'],
            'bukti' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'status' => ['required', Rule::enum(CaseStatus::class)->only([CaseStatus::Draft, CaseStatus::Confirmed])],
        ];
    }
}
