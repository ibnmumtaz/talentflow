<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'job' => ['id' => $this->job->id, 'title' => $this->job->title],
            'candidate' => ['id' => $this->user->id, 'name' => $this->user->name],
            'status' => $this->status,
            'evaluation_score' => $this->evaluation_score,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
