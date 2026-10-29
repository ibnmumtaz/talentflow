<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:job_categories,id'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['required', 'string', 'min:50'],
            'responsibilities' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'employment_type' => ['required', 'in:full_time,part_time,contract,internship,freelance,temporary'],
            'workplace_type' => ['required', 'in:remote,hybrid,onsite'],
            'experience_level' => ['required', 'in:entry,junior,mid,senior,lead,executive'],
            'salary_min' => ['nullable', 'numeric', 'min:0', 'lte:salary_max'],
            'salary_max' => ['nullable', 'numeric', 'min:0'],
            'salary_currency' => ['required', 'in:PKR,USD,GBP,EUR,AED,INR'],
            'salary_period' => ['required', 'in:monthly,annually,hourly'],
            'salary_visible' => ['boolean'],
            'openings' => ['required', 'integer', 'min:1', 'max:100'],
            'application_deadline' => ['nullable', 'date', 'after:today'],
            'expires_at' => ['nullable', 'date', 'after:today'],
            'benefits' => ['nullable', 'string', 'max:1000'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['exists:skills,id'],
            'screening_questions' => ['nullable', 'array'],
            'screening_questions.*.question' => ['required_with:screening_questions', 'string', 'max:500'],
            'screening_questions.*.type' => ['in:text,yes_no,multiple_choice'],
            'screening_questions.*.is_required' => ['boolean'],
        ];
    }
}
