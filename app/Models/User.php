<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'phone', 'avatar_path', 'is_suspended', 'suspended_at', 'suspension_reason', 'last_login_at', 'onboarding_completed_at', 'timezone', 'notification_preferences'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_suspended' => 'boolean',
            'suspended_at' => 'datetime',
            'last_login_at' => 'datetime',
            'onboarding_completed_at' => 'datetime',
            'notification_preferences' => 'array',
        ];
    }

    public const ROLES = ['admin' => 'Administrator', 'moderator' => 'Support / Moderator', 'employer' => 'Employer / Recruiter', 'candidate' => 'Job Seeker'];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isModerator(): bool
    {
        return in_array($this->role, ['admin', 'moderator'], true);
    }

    public function isEmployer(): bool
    {
        return in_array($this->role, ['employer', 'admin'], true);
    }

    public function isCandidate(): bool
    {
        return $this->role === 'candidate';
    }

    public function roleLabel(): string
    {
        return self::ROLES[$this->role] ?? ucfirst($this->role);
    }

    public function initials(): string
    {
        $p = preg_split('/\s+/', trim($this->name));
        $i = '';
        foreach (array_slice($p, 0, 2) as $w) {
            $i .= strtoupper(substr($w, 0, 1));
        }

return $i ?: 'U';
    }

    public function candidateProfile(): HasOne
    {
        return $this->hasOne(CandidateProfile::class);
    }

    public function resumes(): HasMany
    {
        return $this->hasMany(CandidateResume::class);
    }

    public function primaryResume(): ?CandidateResume
    {
        return $this->resumes()->where('is_primary', true)->first() ?? $this->resumes()->latest()->first();
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function savedJobs(): HasMany
    {
        return $this->hasMany(SavedJob::class);
    }

    public function jobAlerts(): HasMany
    {
        return $this->hasMany(JobAlert::class);
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(CompanyMembership::class);
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_memberships')->withPivot(['role', 'status', 'can_publish_jobs', 'can_view_candidates', 'can_manage_team']);
    }

    public function activeCompanies()
    {
        return $this->companies()->wherePivot('status', 'active');
    }

    public function postedJobs(): HasMany
    {
        return $this->hasMany(Job::class, 'posted_by_id');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    public function dashboardRoute(): string
    {
        return match ($this->role) {
            'admin' => 'admin.dashboard',
            'moderator' => 'admin.dashboard',
            'employer' => 'employer.dashboard',
            default => 'candidate.dashboard',
        };
    }

    public function dashboardUrl(): string
    {
        return route($this->dashboardRoute());
    }

    public function notifyPreference(string $key, bool $default = true): bool
    {
        $prefs = $this->notification_preferences ?? [];

        return $prefs[$key] ?? $default;
    }
}
