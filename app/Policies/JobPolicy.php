<?php

namespace App\Policies;

use App\Models\Job;
use App\Models\User;

class JobPolicy
{
    public function view(User $user, Job $job): bool
    {
        if ($job->status === 'published') {
            return true;
        }

        return $user->isAdmin() || $job->posted_by_id === $user->id || $job->company->memberRole($user) !== null;
    }

    public function update(User $user, Job $job): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        if ($job->posted_by_id === $user->id) {
            return true;
        }
        $m = $job->company->memberships()->where('user_id', $user->id)->where('status', 'active')->first();

        return $m && $m->can_publish_jobs;
    }

    public function publish(User $user, Job $job): bool
    {
        return $this->update($user, $job);
    }

    public function viewApplicants(User $user, Job $job): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->can('viewCandidates', $job->company);
    }
}
