<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
{
    public function view(User $user, Application $application): bool
    {
        if ($application->user_id === $user->id) {
            return true;
        }
        if ($user->isAdmin()) {
            return true;
        }

        return $user->can('viewCandidates', $application->job->company);
    }

    public function update(User $user, Application $application): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->can('viewCandidates', $application->job->company);
    }

    public function withdraw(User $user, Application $application): bool
    {
        return $application->user_id === $user->id && ! in_array($application->status, ['hired', 'rejected', 'withdrawn'], true);
    }
}
