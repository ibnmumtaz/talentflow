@extends('layouts.dashboard', ['heading' => 'My profile', 'subheading' => 'Keep this current — employers see the public parts when you apply.'])
@section('dashboard')
@php $completeness = $profile->completeness(); @endphp
<div class="glass rounded-2xl p-5"><p class="text-sm">Profile completeness: <strong>{{ $completeness }}%</strong></p><div class="mt-2 h-2.5 overflow-hidden rounded-full bg-white/10"><div class="h-full bg-gradient-to-r from-violet-500 to-cyan-400" style="width: {{ $completeness }}%"></div></div></div>

<form method="POST" action="{{ route('candidate.profile.update') }}" class="glass mt-6 space-y-4 rounded-2xl p-6">
    @csrf @method('PUT')
    <h2 class="font-semibold">Personal &amp; professional summary</h2>
    <div class="grid gap-4 sm:grid-cols-2">
        <div><label class="text-sm" for="headline">Headline</label><input id="headline" name="headline" value="{{ old('headline', $profile->headline) }}" class="input-glass mt-1"></div>
        <div><label class="text-sm" for="current_title">Current title</label><input id="current_title" name="current_title" value="{{ old('current_title', $profile->current_title) }}" class="input-glass mt-1"></div>
        <div><label class="text-sm" for="phone">Phone</label><input id="phone" name="phone" value="{{ old('phone', $profile->phone) }}" class="input-glass mt-1"></div>
        <div><label class="text-sm" for="city">City</label><input id="city" name="city" value="{{ old('city', $profile->city) }}" class="input-glass mt-1"></div>
        <div><label class="text-sm" for="location_id">Location</label><select id="location_id" name="location_id" class="input-glass mt-1"><option value="">Select</option>@foreach($locations as $l)<option value="{{ $l->id }}" @selected(old('location_id', $profile->location_id) == $l->id)>{{ $l->name }}</option>@endforeach</select></div>
        <div><label class="text-sm" for="years_of_experience">Years of experience</label><input id="years_of_experience" type="number" min="0" max="60" name="years_of_experience" value="{{ old('years_of_experience', $profile->years_of_experience) }}" class="input-glass mt-1"></div>
        <div><label class="text-sm" for="expected_salary">Expected salary (PKR / month)</label><input id="expected_salary" type="number" min="0" name="expected_salary" value="{{ old('expected_salary', $profile->expected_salary) }}" class="input-glass mt-1"></div>
        <div><label class="text-sm" for="preferred_employment_type">Preferred employment</label><select id="preferred_employment_type" name="preferred_employment_type" class="input-glass mt-1"><option value="">Any</option>@foreach(\App\Models\Job::EMPLOYMENT_TYPES as $k => $v)<option value="{{ $k }}" @selected(old('preferred_employment_type', $profile->preferred_employment_type) == $k)>{{ $v }}</option>@endforeach</select></div>
    </div>
    <div><label class="text-sm" for="summary">Professional summary</label><textarea id="summary" name="summary" rows="4" class="input-glass mt-1">{{ old('summary', $profile->summary) }}</textarea></div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div><label class="text-sm" for="linkedin_url">LinkedIn URL</label><input id="linkedin_url" name="linkedin_url" value="{{ old('linkedin_url', $profile->linkedin_url) }}" class="input-glass mt-1"></div>
        <div><label class="text-sm" for="portfolio_url">Portfolio URL</label><input id="portfolio_url" name="portfolio_url" value="{{ old('portfolio_url', $profile->portfolio_url) }}" class="input-glass mt-1"></div>
        <div><label class="text-sm" for="github_url">GitHub URL</label><input id="github_url" name="github_url" value="{{ old('github_url', $profile->github_url) }}" class="input-glass mt-1"></div>
        <div><label class="text-sm" for="website_url">Website URL</label><input id="website_url" name="website_url" value="{{ old('website_url', $profile->website_url) }}" class="input-glass mt-1"></div>
    </div>
    <div class="flex flex-wrap gap-4 text-sm">
        <label class="flex items-center gap-2"><input type="checkbox" name="open_to_work" value="1" @checked(old('open_to_work', $profile->open_to_work))> Open to work</label>
        <label class="flex items-center gap-2"><input type="checkbox" name="open_to_remote" value="1" @checked(old('open_to_remote', $profile->open_to_remote))> Open to remote</label>
        <label class="flex items-center gap-2">Visibility <select name="visibility" class="input-glass w-auto py-1"><option value="public" @selected($profile->visibility === 'public')>Public</option><option value="private" @selected($profile->visibility === 'private')>Private</option></select></label>
    </div>
    <button class="btn-primary rounded-xl px-5 py-2.5 text-sm">Save profile</button>
</form>

<div class="grid gap-6 lg:grid-cols-2">
    <section class="glass mt-6 rounded-2xl p-6">
        <h2 class="font-semibold">Education</h2>
        <ul class="mt-3 space-y-2 text-sm">@forelse($profile->educations as $e)<li class="flex justify-between gap-3"><span>{{ $e->degree }} — {{ $e->institution }} ({{ $e->start_date?->format('Y') }}–{{ $e->is_current ? 'Present' : $e->end_date?->format('Y') }})</span><form method="POST" action="{{ route('candidate.profile.education.destroy', $e) }}" data-confirm="Remove this education entry?">@csrf @method('DELETE')<button class="text-rose-300">Remove</button></form></li>@empty<li class="text-slate-400">No education added yet.</li>@endforelse</ul>
        <form method="POST" action="{{ route('candidate.profile.education.store') }}" class="mt-4 space-y-2">@csrf
            <input name="institution" required placeholder="Institution" class="input-glass"><input name="degree" required placeholder="Degree / qualification" class="input-glass"><input name="field_of_study" placeholder="Field of study" class="input-glass">
            <div class="grid grid-cols-2 gap-2"><input type="date" name="start_date" class="input-glass"><input type="date" name="end_date" class="input-glass"></div>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_current" value="1"> Currently studying</label>
            <button class="btn-ghost rounded-xl px-4 py-2 text-sm">Add education</button>
        </form>
    </section>
    <section class="glass mt-6 rounded-2xl p-6">
        <h2 class="font-semibold">Experience</h2>
        <ul class="mt-3 space-y-2 text-sm">@forelse($profile->experiences as $e)<li class="flex justify-between gap-3"><span>{{ $e->job_title }} — {{ $e->company_name }} ({{ $e->start_date?->format('M Y') }}–{{ $e->is_current ? 'Present' : $e->end_date?->format('M Y') }})</span><form method="POST" action="{{ route('candidate.profile.experience.destroy', $e) }}" data-confirm="Remove this experience?">@csrf @method('DELETE')<button class="text-rose-300">Remove</button></form></li>@empty<li class="text-slate-400">No experience added yet.</li>@endforelse</ul>
        <form method="POST" action="{{ route('candidate.profile.experience.store') }}" class="mt-4 space-y-2">@csrf
            <input name="job_title" required placeholder="Job title" class="input-glass"><input name="company_name" required placeholder="Company" class="input-glass">
            <div class="grid grid-cols-2 gap-2"><input type="date" name="start_date" class="input-glass"><input type="date" name="end_date" class="input-glass"></div>
            <textarea name="description" rows="2" placeholder="What did you do?" class="input-glass"></textarea>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_current" value="1"> I currently work here</label>
            <button class="btn-ghost rounded-xl px-4 py-2 text-sm">Add experience</button>
        </form>
    </section>
    <section class="glass rounded-2xl p-6">
        <h2 class="font-semibold">Skills</h2>
        <div class="mt-3 flex flex-wrap gap-2">@forelse($profile->candidateSkills as $cs)<span class="rounded-full bg-white/5 px-3 py-1 text-xs">{{ $cs->skill->name }} ({{ $cs->level }}) <form method="POST" action="{{ route('candidate.profile.skills.destroy', $cs->skill_id) }}" class="inline" data-confirm="Remove this skill?">@csrf @method('DELETE')<button class="text-rose-300">×</button></form></span>@empty<span class="text-sm text-slate-400">No skills added yet.</span>@endforelse</div>
        <form method="POST" action="{{ route('candidate.profile.skills.store') }}" class="mt-4 flex flex-wrap gap-2">@csrf
            <select name="skill_id" required class="input-glass w-auto flex-1"><option value="">Choose a skill</option>@foreach($skills as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select>
            <select name="level" class="input-glass w-auto"><option>intermediate</option><option>beginner</option><option>advanced</option><option>expert</option></select>
            <button class="btn-ghost rounded-xl px-4 py-2 text-sm">Add skill</button>
        </form>
    </section>
    <section class="glass rounded-2xl p-6">
        <h2 class="font-semibold">Certifications &amp; languages</h2>
        <ul class="mt-3 space-y-2 text-sm">@forelse($profile->certifications as $c)<li class="flex justify-between"><span>{{ $c->name }} @if($c->issuer)— {{ $c->issuer }}@endif</span><form method="POST" action="{{ route('candidate.profile.certifications.destroy', $c) }}" data-confirm="Remove?">@csrf @method('DELETE')<button class="text-rose-300">Remove</button></form></li>@empty<li class="text-slate-400">No certifications yet.</li>@endforelse</ul>
        <form method="POST" action="{{ route('candidate.profile.certifications.store') }}" class="mt-3 flex flex-wrap gap-2">@csrf<input name="name" required placeholder="Certification name" class="input-glass flex-1"><input name="issuer" placeholder="Issuer" class="input-glass w-40"><button class="btn-ghost rounded-xl px-4 py-2 text-sm">Add</button></form>
        <ul class="mt-4 space-y-2 text-sm">@forelse($profile->languages as $l)<li class="flex justify-between"><span>{{ $l->language }} — {{ $l->proficiency }}</span><form method="POST" action="{{ route('candidate.profile.languages.destroy', $l) }}" data-confirm="Remove?">@csrf @method('DELETE')<button class="text-rose-300">Remove</button></form></li>@empty<li class="text-slate-400">No languages yet.</li>@endforelse</ul>
        <form method="POST" action="{{ route('candidate.profile.languages.store') }}" class="mt-3 flex flex-wrap gap-2">@csrf<input name="language" required placeholder="Language" class="input-glass flex-1"><select name="proficiency" class="input-glass w-auto"><option>beginner</option><option selected>intermediate</option><option>advanced</option><option>native</option></select><button class="btn-ghost rounded-xl px-4 py-2 text-sm">Add language</button></form>
    </section>
</div>
@endsection
