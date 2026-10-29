<?php

namespace App\Policies;

use App\Models\CandidateResume;
use App\Models\User;

class ResumePolicy
{
    public function view(User $user, CandidateResume $resume): bool
    {
        if ($resume->user_id === $user->id || $user->isAdmin()) {
            return true;
        }

        // Employers who received an application with this resume may view it.
        return $resume->user->applications()->where('resume_id', $resume->id)->whereHas('job.company.memberships', function ($q) use ($user) {
            $q->where('user_id', $user->id)->where('status', 'active')->where('can_view_candidates', true);
        })->exists();
    }

    public function delete(User $user, CandidateResume $resume): bool
    {
        return $resume->user_id === $user->id || $user->isAdmin();
    }
}
