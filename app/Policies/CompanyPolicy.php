<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    public function view(User $user, Company $company): bool
    {
        return $user->isAdmin() || $company->memberRole($user) !== null;
    }

    public function update(User $user, Company $company): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        $m = $company->memberships()->where('user_id', $user->id)->where('status', 'active')->first();

        return $m && in_array($m->role, ['owner', 'hiring_manager'], true);
    }

    public function manageTeam(User $user, Company $company): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        $m = $company->memberships()->where('user_id', $user->id)->where('status', 'active')->first();

        return $m && ($m->role === 'owner' || $m->can_manage_team);
    }

    public function manageJobs(User $user, Company $company): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $company->memberRole($user) !== null;
    }

    public function viewCandidates(User $user, Company $company): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        $m = $company->memberships()->where('user_id', $user->id)->where('status', 'active')->first();

        return $m && $m->can_view_candidates;
    }
}
