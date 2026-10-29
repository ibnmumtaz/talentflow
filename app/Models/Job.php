<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = ['benefits' => 'array', 'salary_min' => 'decimal:2', 'salary_max' => 'decimal:2', 'is_featured' => 'boolean', 'is_remote' => 'boolean', 'salary_visible' => 'boolean', 'application_deadline' => 'date', 'expires_at' => 'date', 'published_at' => 'datetime'];

    public const EMPLOYMENT_TYPES = ['full_time' => 'Full-time', 'part_time' => 'Part-time', 'contract' => 'Contract', 'internship' => 'Internship', 'freelance' => 'Freelance', 'temporary' => 'Temporary'];

    public const WORKPLACE_TYPES = ['remote' => 'Remote', 'hybrid' => 'Hybrid', 'onsite' => 'On-site'];

    public const EXPERIENCE_LEVELS = ['entry' => 'Entry level', 'junior' => 'Junior', 'mid' => 'Mid level', 'senior' => 'Senior', 'lead' => 'Lead / Manager', 'executive' => 'Executive'];

    public const STATUSES = ['draft', 'pending', 'published', 'closed', 'archived', 'rejected'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'job_skills')->withPivot('is_required');
    }

    public function screeningQuestions(): HasMany
    {
        return $this->hasMany(JobScreeningQuestion::class)->orderBy('sort_order');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function savedBy(): HasMany
    {
        return $this->hasMany(SavedJob::class);
    }

    public function scopePublished($q)
    {
        return $q->where('status', 'published')->where(function ($qq) {
            $qq->whereNull('expires_at')->orWhere('expires_at', '>=', now()->toDateString());
        });
    }

    public function scopeVisible($q)
    {
        return $q->published()->whereHas('company', fn ($c) => $c->where('is_active', true));
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function salaryLabel(): string
    {
        if (! $this->salary_visible) {
            return 'Salary not disclosed';
        } $c = $this->salary_currency;
        if ($this->salary_min && $this->salary_max) {
            return $c.' '.number_format($this->salary_min).' - '.number_format($this->salary_max).' / '.$this->salary_period;
        } if ($this->salary_min) {
            return $c.' '.number_format($this->salary_min).'+ / '.$this->salary_period;
        }

return 'Competitive';
    }

    public function isSavedBy(?User $u): bool
    {
        return $u ? $this->savedBy()->where('user_id', $u->id)->exists() : false;
    }

    public function hasApplied(?User $u): bool
    {
        return $u ? $this->applications()->where('user_id', $u->id)->exists() : false;
    }
}
