<?php

namespace App\Policies;

use App\Models\Interview;
use App\Models\User;

class InterviewPolicy
{
    public function view(User $user, Interview $interview): bool
    {
        if ($interview->application->user_id === $user->id) {
            return true;
        }
        if ($user->isAdmin()) {
            return true;
        }
        if ($interview->participants()->where('users.id', $user->id)->exists()) {
            return true;
        }

        return $user->can('viewCandidates', $interview->job->company);
    }

    public function update(User $user, Interview $interview): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->can('viewCandidates', $interview->job->company);
    }
}
