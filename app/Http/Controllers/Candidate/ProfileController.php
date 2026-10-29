<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Http\Requests\CandidateProfileRequest;
use App\Models\CandidateCertification;
use App\Models\CandidateEducation;
use App\Models\CandidateExperience;
use App\Models\CandidateLanguage;
use App\Models\Location;
use App\Models\Skill;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();
        $profile = $user->candidateProfile()->firstOrCreate(['user_id' => $user->id], ['open_to_work' => true]);
        $profile->load(['educations', 'experiences', 'candidateSkills.skill', 'certifications', 'languages']);

        return view('candidate.profile.edit', ['profile' => $profile, 'locations' => Location::orderBy('name')->get(), 'skills' => Skill::orderBy('name')->get(), 'user' => $user]);
    }

    public function update(CandidateProfileRequest $request)
    {
        $profile = $request->user()->candidateProfile()->firstOrCreate(['user_id' => $request->user()->id]);
        $profile->update($request->validated() + ['open_to_work' => $request->boolean('open_to_work'), 'open_to_remote' => $request->boolean('open_to_remote')]);
        $request->user()->update(['phone' => $request->input('phone')]);

        return back()->with('status', 'Profile updated.');
    }

    public function storeEducation(Request $request)
    {
        $data = $request->validate(['institution' => ['required', 'string', 'max:255'], 'degree' => ['required', 'string', 'max:255'], 'field_of_study' => ['nullable', 'string', 'max:255'], 'start_date' => ['nullable', 'date'], 'end_date' => ['nullable', 'date'], 'is_current' => ['boolean'], 'description' => ['nullable', 'string', 'max:2000']]);
        $profile = $request->user()->candidateProfile()->firstOrCreate(['user_id' => $request->user()->id]);
        $profile->educations()->create($data + ['is_current' => $request->boolean('is_current')]);

        return back()->with('status', 'Education added.');
    }

    public function destroyEducation(Request $request, CandidateEducation $education)
    {
        abort_unless($education->candidate_profile_id === $request->user()->candidateProfile?->id, 403);
        $education->delete();

        return back()->with('status', 'Education removed.');
    }

    public function storeExperience(Request $request)
    {
        $data = $request->validate(['job_title' => ['required', 'string', 'max:255'], 'company_name' => ['required', 'string', 'max:255'], 'location' => ['nullable', 'string', 'max:255'], 'employment_type' => ['nullable', 'string', 'max:30'], 'start_date' => ['nullable', 'date'], 'end_date' => ['nullable', 'date'], 'is_current' => ['boolean'], 'description' => ['nullable', 'string', 'max:3000']]);
        $profile = $request->user()->candidateProfile()->firstOrCreate(['user_id' => $request->user()->id]);
        $profile->experiences()->create($data + ['is_current' => $request->boolean('is_current')]);

        return back()->with('status', 'Experience added.');
    }

    public function destroyExperience(Request $request, CandidateExperience $experience)
    {
        abort_unless($experience->candidate_profile_id === $request->user()->candidateProfile?->id, 403);
        $experience->delete();

        return back()->with('status', 'Experience removed.');
    }

    public function storeSkill(Request $request)
    {
        $data = $request->validate(['skill_id' => ['required', 'exists:skills,id'], 'level' => ['required', 'in:beginner,intermediate,advanced,expert'], 'years' => ['nullable', 'integer', 'min:0', 'max:60']]);
        $profile = $request->user()->candidateProfile()->firstOrCreate(['user_id' => $request->user()->id]);
        $profile->candidateSkills()->updateOrCreate(['skill_id' => $data['skill_id']], ['level' => $data['level'], 'years' => $data['years']]);

        return back()->with('status', 'Skill added.');
    }

    public function destroySkill(Request $request, $skillId)
    {
        $profile = $request->user()->candidateProfile;
        abort_unless($profile, 403);
        $profile->candidateSkills()->where('skill_id', $skillId)->delete();

        return back()->with('status', 'Skill removed.');
    }

    public function storeCertification(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'issuer' => ['nullable', 'string', 'max:255'], 'issued_date' => ['nullable', 'date'], 'expiry_date' => ['nullable', 'date'], 'credential_url' => ['nullable', 'url', 'max:255']]);
        $profile = $request->user()->candidateProfile()->firstOrCreate(['user_id' => $request->user()->id]);
        $profile->certifications()->create($data);

        return back()->with('status', 'Certification added.');
    }

    public function destroyCertification(Request $request, CandidateCertification $certification)
    {
        abort_unless($certification->candidate_profile_id === $request->user()->candidateProfile?->id, 403);
        $certification->delete();

        return back()->with('status', 'Certification removed.');
    }

    public function storeLanguage(Request $request)
    {
        $data = $request->validate(['language' => ['required', 'string', 'max:100'], 'proficiency' => ['required', 'in:beginner,intermediate,advanced,native']]);
        $profile = $request->user()->candidateProfile()->firstOrCreate(['user_id' => $request->user()->id]);
        $profile->languages()->create($data);

        return back()->with('status', 'Language added.');
    }

    public function destroyLanguage(Request $request, CandidateLanguage $language)
    {
        abort_unless($language->candidate_profile_id === $request->user()->candidateProfile?->id, 403);
        $language->delete();

        return back()->with('status', 'Language removed.');
    }
}
