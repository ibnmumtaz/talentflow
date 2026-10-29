@extends('layouts.dashboard', ['heading' => $job->title, 'subheading' => $job->company->name.' · '.$job->status])
@section('dashboard')
<div class="flex flex-wrap gap-2">
    <a href="{{ route('employer.jobs.edit', $job) }}" class="btn-ghost rounded-xl px-4 py-2 text-sm">Edit job</a>
    @if(in_array($job->status, ['draft', 'rejected', 'closed'], true))<form method="POST" action="{{ route('employer.jobs.publish', $job) }}">@csrf<button class="btn-primary rounded-xl px-4 py-2 text-sm">Submit for publication</button></form>@endif
    @if($job->status === 'published')<form method="POST" action="{{ route('employer.jobs.close', $job) }}" data-confirm="Close this job? It will no longer be publicly visible.">@csrf<button class="btn-ghost rounded-xl px-4 py-2 text-sm">Close job</button></form>@endif
    <form method="POST" action="{{ route('employer.jobs.archive', $job) }}" data-confirm="Archive this job?">@csrf<button class="btn-ghost rounded-xl px-4 py-2 text-sm">Archive</button></form>
    <a href="{{ route('jobs.show', $job) }}" class="btn-ghost rounded-xl px-4 py-2 text-sm" target="_blank" rel="noopener">Public preview ↗</a>
    <a href="{{ route('employer.applicants.index', ['job_id' => $job->id]) }}" class="btn-ghost rounded-xl px-4 py-2 text-sm">View applicants ({{ $job->applications()->count() }})</a>
</div>
@if($job->rejection_reason)<div class="glass mt-4 rounded-2xl border-rose-400/30 p-4 text-sm text-rose-200"><strong>Moderation note:</strong> {{ $job->rejection_reason }}</div>@endif
<div class="glass mt-6 space-y-4 rounded-2xl p-6">
    <p class="text-sm text-slate-400">{{ \App\Models\Job::EMPLOYMENT_TYPES[$job->employment_type] ?? '' }} · {{ \App\Models\Job::WORKPLACE_TYPES[$job->workplace_type] ?? '' }} · {{ $job->location->name ?? 'Remote' }} · {{ $job->salaryLabel() }} · {{ $job->views_count }} views</p>
    <div class="whitespace-pre-line text-slate-200">{{ $job->description }}</div>
    @if($job->requirements)<h2 class="font-semibold">Requirements</h2><div class="whitespace-pre-line text-slate-300">{{ $job->requirements }}</div>@endif
    <div class="flex flex-wrap gap-2">@foreach($job->skills as $s)<span class="rounded-full bg-white/5 px-3 py-1 text-xs">{{ $s->name }}</span>@endforeach</div>
    <form method="POST" action="{{ route('employer.jobs.destroy', $job) }}" data-confirm="Delete this job? This is a soft delete; applications remain in the database.">@csrf @method('DELETE')<button class="text-sm text-rose-300">Delete job</button></form>
</div>
@endsection
