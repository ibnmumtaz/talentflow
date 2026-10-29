<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = ['benefits' => 'array', 'is_featured' => 'boolean', 'is_active' => 'boolean', 'verified_at' => 'datetime'];

    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }

    public function publishedJobs(): HasMany
    {
        return $this->hasMany(Job::class)->where('status', 'published');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(CompanyMembership::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'company_memberships')->withPivot(['role', 'status', 'can_publish_jobs', 'can_view_candidates', 'can_manage_team']);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(CompanyInvitation::class);
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(CompanyVerification::class);
    }

    public function scopeVerified($q)
    {
        return $q->where('verification_status', 'verified');
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function memberRole(User $user): ?string
    {
        $m = $this->memberships()->where('user_id', $user->id)->where('status', 'active')->first();

        return $m ? $m->role : null;
    }

    public function canBeManagedBy(User $user): bool
    {
        return $user->isAdmin() || $this->memberRole($user) !== null;
    }
}
