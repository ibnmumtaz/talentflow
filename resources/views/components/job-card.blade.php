@props(['job'])
@php $company = $job->company; @endphp
<article class="glass glass-hover rounded-2xl p-5">
    <div class="flex items-start justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500/80 to-cyan-400/80 text-sm font-bold">{{ strtoupper(substr($company->name ?? 'T', 0, 1)) }}</span>
            <div class="min-w-0">
                <h3 class="truncate font-semibold"><a class="hover:text-cyan-300" href="{{ route('jobs.show', $job) }}">{{ $job->title }}</a></h3>
                <p class="truncate text-sm text-slate-400"><a class="hover:text-white" href="{{ $company ? route('companies.show', $company) : '#' }}">{{ $company->name ?? '' }}</a>
                    @if($company && $company->verification_status === 'verified') <span class="text-cyan-300" title="Verified employer">✓ Verified</span> @endif
                </p>
            </div>
        </div>
        @if($job->is_featured)<span class="shrink-0 rounded-full bg-amber-400/15 px-2.5 py-1 text-xs font-semibold text-amber-300">Featured</span>@endif
    </div>
    <p class="mt-3 line-clamp-2 text-sm text-slate-300">{{ $job->summary ?? \Illuminate\Support\Str::limit(strip_tags($job->description), 140) }}</p>
    <div class="mt-4 flex flex-wrap gap-1.5 text-xs">
        <span class="rounded-full bg-white/5 px-2.5 py-1 text-slate-300">{{ \App\Models\Job::EMPLOYMENT_TYPES[$job->employment_type] ?? $job->employment_type }}</span>
        <span class="rounded-full bg-white/5 px-2.5 py-1 text-slate-300">{{ \App\Models\Job::WORKPLACE_TYPES[$job->workplace_type] ?? $job->workplace_type }}</span>
        <span class="rounded-full bg-white/5 px-2.5 py-1 text-slate-300">{{ $job->location->name ?? ($job->workplace_type === 'remote' ? 'Remote' : '—') }}</span>
        <span class="rounded-full bg-white/5 px-2.5 py-1 text-slate-300">{{ \App\Models\Job::EXPERIENCE_LEVELS[$job->experience_level] ?? '' }}</span>
    </div>
    <div class="mt-4 flex items-center justify-between gap-3">
        <span class="text-sm font-semibold text-cyan-300">{{ $job->salaryLabel() }}</span>
        <span class="text-xs text-slate-500">{{ $job->published_at?->diffForHumans() ?? $job->created_at->diffForHumans() }} · {{ $job->applications_count_cached }} applicants</span>
    </div>
    <div class="mt-4 flex gap-2">
        <a href="{{ route('jobs.show', $job) }}" class="btn-primary flex-1 rounded-xl px-3 py-2 text-center text-sm">View job</a>
        @auth
            @if(auth()->user()->isCandidate())
                <form method="POST" action="{{ route('candidate.saved.toggle', $job) }}">@csrf<button class="btn-ghost rounded-xl px-3 py-2 text-sm">{{ $job->isSavedBy(auth()->user()) ? '♥ Saved' : '♡ Save' }}</button></form>
            @endif
        @endauth
    </div>
</article>
