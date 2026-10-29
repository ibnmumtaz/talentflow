<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CandidateProfile extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = ['open_to_work' => 'boolean', 'open_to_remote' => 'boolean', 'expected_salary' => 'decimal:2'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function educations(): HasMany
    {
        return $this->hasMany(CandidateEducation::class);
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(CandidateExperience::class)->orderByDesc('start_date');
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'candidate_skills')->withPivot(['level', 'years']);
    }

    public function candidateSkills(): HasMany
    {
        return $this->hasMany(CandidateSkill::class);
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(CandidateCertification::class);
    }

    public function languages(): HasMany
    {
        return $this->hasMany(CandidateLanguage::class);
    }

    public function completeness(): int
    {
        $fields = ['headline', 'summary', 'phone', 'city', 'current_title', 'years_of_experience', 'linkedin_url'];
        $filled = 0;
        foreach ($fields as $f) {
            if (! empty($this->$f)) {
                $filled++;
            }
        } $score = round($filled / count($fields) * 55);
        if ($this->educations()->exists()) {
            $score += 10;
        } if ($this->experiences()->exists()) {
            $score += 15;
        } if ($this->skills()->exists()) {
            $score += 10;
        } if ($this->user->resumes()->exists()) {
            $score += 10;
        }

return min(100, $score);
    }
}
