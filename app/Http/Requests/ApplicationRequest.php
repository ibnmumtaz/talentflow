<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->isCandidate();
    }

    public function rules(): array
    {
        return [
            'resume_id' => ['nullable', 'exists:candidate_resumes,id'],
            'resume_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'cover_letter' => ['nullable', 'string', 'max:5000'],
            'answers' => ['nullable', 'array'],
            'answers.*' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            if (! $this->resume_id && ! $this->hasFile('resume_file') && ! $this->user()->resumes()->exists()) {
                $v->errors()->add('resume_file', 'Please select an existing CV or upload one.');
            }
        });
    }
}
