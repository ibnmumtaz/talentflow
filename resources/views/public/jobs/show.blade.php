@extends('layouts.app')
@section('title', $job->title.' at '.$job->company->name)
@section('description', $job->summary ?? \Illuminate\Support\Str::limit(strip_tags($job->description), 160))
@section('content')
@php $canonical = route('jobs.show', $job); @endphp
<div class="mt-8 grid gap-6 lg:grid-cols-[1fr_320px]">
    <article class="glass rounded-2xl p-6 sm:p-8">
        <p class="text-xs font-semibold uppercase tracking-wider text-cyan-300">{{ $job->category->name ?? 'General' }} · {{ \App\Models\Job::EMPLOYMENT_TYPES[$job->employment_type] ?? '' }}</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight">{{ $job->title }}</h1>
        <p class="mt-2 text-slate-300"><a class="hover:text-white" href="{{ route('companies.show', $job->company) }}">{{ $job->company->name }}</a> · {{ $job->location->name ?? 'Remote' }} · {{ \App\Models\Job::WORKPLACE_TYPES[$job->workplace_type] ?? '' }} · {{ \App\Models\Job::EXPERIENCE_LEVELS[$job->experience_level] ?? '' }}</p>
        <p class="mt-2 text-sm text-slate-400">Posted {{ $job->published_at?->diffForHumans() ?? $job->created_at->diffForHumans() }} · {{ $job->views_count }} views · {{ $job->applications_count_cached }} applications @if($job->application_deadline) · Apply by {{ $job->application_deadline->format('d M Y') }} @endif</p>

        <div class="mt-5 flex flex-wrap gap-2">
            @foreach($job->skills as $skill)<span class="rounded-full bg-white/5 px-3 py-1 text-xs">{{ $skill->name }}</span>@endforeach
        </div>

        <h2 class="mt-8 text-lg font-semibold">About this role</h2>
        <div class="mt-2 whitespace-pre-line text-slate-200">{!! nl2br(e($job->description)) !!}</div>
        @if($job->responsibilities)<h2 class="mt-8 text-lg font-semibold">Responsibilities</h2><div class="mt-2 whitespace-pre-line text-slate-200">{!! nl2br(e($job->responsibilities)) !!}</div>@endif
        @if($job->requirements)<h2 class="mt-8 text-lg font-semibold">Requirements</h2><div class="mt-2 whitespace-pre-line text-slate-200">{!! nl2br(e($job->requirements)) !!}</div>@endif
        @if($job->benefits)<h2 class="mt-8 text-lg font-semibold">Benefits</h2><ul class="mt-2 list-disc pl-5 text-slate-200">@foreach((array) $job->benefits as $b)<li>{{ $b }}</li>@endforeach</ul>@endif

        <div class="mt-8 border-t border-white/10 pt-5">
            <form method="POST" action="{{ route('reports.store') }}" class="text-sm text-slate-400">
                @csrf<input type="hidden" name="reportable_type" value="job"><input type="hidden" name="reportable_id" value="{{ $job->id }}">
                <label class="sr-only" for="reason">Report reason</label>
                <select id="reason" name="reason" class="input-glass w-auto text-sm"><option value="spam">Spam</option><option value="misleading">Misleading</option><option value="inappropriate">Inappropriate</option><option value="scam">Suspected scam</option><option value="other">Other</option></select>
                <button class="ml-2 underline hover:text-white">Report this job</button>
            </form>
        </div>
    </article>

    <aside class="space-y-4">
        <div class="glass-strong rounded-2xl p-5">
            <p class="text-sm text-slate-400">Salary</p>
            <p class="mt-1 text-xl font-bold">{{ $job->salaryLabel() }}</p>
            <p class="mt-1 text-sm text-slate-400">{{ $job->openings }} opening(s)</p>
            @guest
                <p class="mt-4 text-sm text-slate-300">Log in as a candidate to apply and save this job.</p>
                <a href="{{ route('login') }}" class="btn-primary mt-3 block rounded-xl px-4 py-2.5 text-center text-sm">Log in to apply</a>
                <a href="{{ route('register', ['role' => 'candidate']) }}" class="btn-ghost mt-2 block rounded-xl px-4 py-2.5 text-center text-sm">Create free account</a>
            @elseif(auth()->user()->isCandidate())
                @if($job->hasApplied(auth()->user()))
                    <p class="mt-4 rounded-xl bg-emerald-400/10 px-3 py-2 text-sm text-emerald-300">✓ You have already applied for this job.</p>
                    <a href="{{ route('candidate.applications.index') }}" class="btn-ghost mt-3 block rounded-xl px-4 py-2.5 text-center text-sm">Track my applications</a>
                @else
                    <form method="POST" action="{{ route('jobs.apply', $job) }}" enctype="multipart/form-data" class="mt-4 space-y-3">
                        @csrf
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wider text-slate-400" for="resume_id">Choose a CV</label>
                            <select id="resume_id" name="resume_id" class="input-glass mt-1"><option value="">— Use my primary CV —</option>@foreach(auth()->user()->resumes as $r)<option value="{{ $r->id }}">{{ $r->title }} ({{ $r->original_filename }})</option>@endforeach</select>
                            <p class="mt-1 text-xs text-slate-500">Or upload a new CV below. <a class="underline" href="{{ route('candidate.resumes.index') }}">Manage CVs</a></p>
                        </div>
                        <div><label class="text-xs font-semibold uppercase tracking-wider text-slate-400" for="resume_file">Upload CV (PDF/DOC, max 5 MB)</label><input id="resume_file" type="file" name="resume_file" accept=".pdf,.doc,.docx" class="input-glass mt-1"></div>
                        <div><label class="text-xs font-semibold uppercase tracking-wider text-slate-400" for="cover_letter">Cover letter (optional)</label><textarea id="cover_letter" name="cover_letter" rows="4" class="input-glass mt-1" placeholder="Why are you a good fit?">{{ old('cover_letter') }}</textarea></div>
                        @foreach($job->screeningQuestions as $q)
                            <div>
                                <label class="text-sm" for="ans-{{ $q->id }}">{{ $q->question }} @if($q->is_required)<span class="text-rose-300">*</span>@endif</label>
                                @if($q->type === 'yes_no')
                                    <select id="ans-{{ $q->id }}" name="answers[{{ $q->id }}]" class="input-glass mt-1" @if($q->is_required) required @endif><option value="">Select</option><option>Yes</option><option>No</option></select>
                                @else
                                    <input id="ans-{{ $q->id }}" name="answers[{{ $q->id }}]" class="input-glass mt-1" @if($q->is_required) required @endif>
                                @endif
                            </div>
                        @endforeach
                        <button class="btn-primary w-full rounded-xl px-4 py-2.5 text-sm">Apply now</button>
                    </form>
                @endif
                <form method="POST" action="{{ route('candidate.saved.toggle', $job) }}" class="mt-2">@csrf<button class="btn-ghost w-full rounded-xl px-4 py-2.5 text-sm">{{ $job->isSavedBy(auth()->user()) ? '♥ Saved — click to unsave' : '♡ Save this job' }}</button></form>
            @else
                <p class="mt-4 text-sm text-slate-300">You are signed in as {{ auth()->user()->roleLabel() }}. Candidates apply from a candidate account.</p>
            @endguest
        </div>
        <div class="glass rounded-2xl p-5">
            <h3 class="font-semibold">About {{ $job->company->name }}</h3>
            <p class="mt-2 line-clamp-3 text-sm text-slate-400">{{ $job->company->description }}</p>
            <a class="mt-3 inline-block text-sm text-cyan-300 hover:underline" href="{{ route('companies.show', $job->company) }}">View company profile →</a>
            <br><a class="mt-1 inline-block text-sm text-cyan-300 hover:underline" href="{{ route('companies.careers', $job->company) }}">All careers at {{ $job->company->name }} →</a>
        </div>
    </aside>
</div>

@if($related->isNotEmpty())
<section class="mt-10">
    <h2 class="text-xl font-bold">Similar jobs</h2>
    <div class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-4">@foreach($related as $job)<x-job-card :job="$job" />@endforeach</div>
</section>
@endif
@endsection
