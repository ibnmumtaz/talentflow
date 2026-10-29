<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobAlert extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = ['salary_min' => 'decimal:2', 'is_active' => 'boolean', 'last_sent_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function matches(Job $job): bool
    {
        if ($this->keywords && ! str_contains(strtolower($job->title.' '.$job->description.' '.($job->company->name ?? '')), strtolower($this->keywords))) {
            return false;
        } if ($this->category_id && $job->category_id != $this->category_id) {
            return false;
        } if ($this->location_id && $job->location_id != $this->location_id) {
            return false;
        } if ($this->workplace_type && $job->workplace_type != $this->workplace_type) {
            return false;
        } if ($this->employment_type && $job->employment_type != $this->employment_type) {
            return false;
        } if ($this->salary_min && (! $job->salary_min || $job->salary_min < $this->salary_min)) {
            return false;
        }

return true;
    }
}
