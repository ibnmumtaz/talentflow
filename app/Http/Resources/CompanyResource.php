<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, 'name' => $this->name, 'slug' => $this->slug, 'description' => $this->description,
            'industry' => $this->industry?->name, 'location' => $this->location?->name, 'size' => $this->size,
            'website' => $this->website, 'verified' => $this->verification_status === 'verified',
            'open_jobs_count' => $this->publishedJobs()->count(),
            'url' => route('companies.show', $this),
        ];
    }
}
