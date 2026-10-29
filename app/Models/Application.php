<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    use HasFactory;

    protected $guarded = [];

    public const STAGES = ['submitted', 'under_review', 'shortlisted', 'interview_scheduled', 'interview_completed', 'offer_extended', 'hired', 'rejected', 'withdrawn'];

    public const TRANSITIONS = ['submitted' => ['under_review', 'shortlisted', 'rejected', 'withdrawn'], 'under_review' => ['shortlisted', 'rejected', 'interview_scheduled', 'withdrawn'], 'shortlisted' => ['interview_scheduled', 'rejected', 'offer_extended', 'withdrawn'], 'interview_scheduled' => ['interview_completed', 'rejected', 'withdrawn'], 'interview_completed' => ['offer_extended', 'rejected', 'shortlisted', 'withdrawn'], 'offer_extended' => ['hired', 'rejected', 'withdrawn'], 'hired' => [], 'rejected' => [], 'withdrawn' => []];

    protected $casts = ['evaluation_score' => 'decimal:1', 'withdrawn_at' => 'datetime'];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function resume(): BelongsTo
    {
        return $this->belongsTo(CandidateResume::class, 'resume_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ApplicationAnswer::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(ApplicationStatusHistory::class)->orderByDesc('created_at');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ApplicationNote::class)->orderByDesc('created_at');
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class)->orderBy('scheduled_at');
    }

    public static function canTransition(string $from, string $to): bool
    {
        return in_array($to, self::TRANSITIONS[$from] ?? [], true);
    }

    public function stageLabel(): string
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }
}
