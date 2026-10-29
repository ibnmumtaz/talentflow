<?php

namespace App\Http\Controllers;

use App\Models\CandidateProfile;
use App\Models\Company;
use App\Models\CompanyMembership;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OnboardingController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        if ($user->onboarding_completed_at) {
            return redirect($user->dashboardUrl());
        }

        return view('auth.onboarding', ['user' => $user]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if ($user->role === 'candidate') {
            $data = $request->validate([
                'headline' => ['nullable', 'string', 'max:255'],
                'city' => ['nullable', 'string', 'max:100'],
                'years_of_experience' => ['nullable', 'integer', 'min:0', 'max:60'],
                'open_to_remote' => ['boolean'],
            ]);
            CandidateProfile::updateOrCreate(['user_id' => $user->id], $data + ['open_to_work' => true]);
        } else {
            $data = $request->validate([
                'company_name' => ['required', 'string', 'max:255'],
                'company_description' => ['nullable', 'string', 'max:2000'],
                'industry_id' => ['nullable', 'exists:industries,id'],
                'location_id' => ['nullable', 'exists:locations,id'],
            ]);
            $company = Company::firstOrCreate(
                ['slug' => Str::slug($data['company_name'])],
                ['name' => $data['company_name'], 'description' => $data['company_description'] ?? null, 'industry_id' => $data['industry_id'] ?? null, 'location_id' => $data['location_id'] ?? null]
            );
            CompanyMembership::firstOrCreate(['company_id' => $company->id, 'user_id' => $user->id], ['role' => 'owner', 'can_publish_jobs' => true, 'can_view_candidates' => true, 'can_manage_team' => true, 'status' => 'active', 'joined_at' => now()]);
        }
        $user->update(['onboarding_completed_at' => now()]);

        return redirect($user->dashboardUrl())->with('status', 'Onboarding complete. Welcome aboard!');
    }
}
