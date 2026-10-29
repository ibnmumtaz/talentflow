<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'description' => $this->description,
            'employment_type' => $this->employment_type,
            'workplace_type' => $this->workplace_type,
            'experience_level' => $this->experience_level,
            'salary' => ['min' => $this->salary_min, 'max' => $this->salary_max, 'currency' => $this->salary_currency, 'period' => $this->salary_period, 'visible' => $this->salary_visible, 'label' => $this->salaryLabel()],
            'company' => ['id' => $this->company->id, 'name' => $this->company->name, 'slug' => $this->company->slug, 'verified' => $this->company->verification_status === 'verified'],
            'category' => $this->category?->only(['id', 'name', 'slug']),
            'location' => $this->location?->only(['id', 'name']),
            'skills' => $this->skills->pluck('name'),
            'application_deadline' => $this->application_deadline?->toDateString(),
            'published_at' => $this->published_at?->toIso8601String(),
            'url' => route('jobs.show', $this),
        ];
    }
}
