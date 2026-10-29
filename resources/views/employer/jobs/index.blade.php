@extends('layouts.dashboard', ['heading' => 'My jobs', 'subheading' => 'Draft, publish, close and archive your listings.'])
@section('dashboard')
<div class="flex flex-wrap gap-2">
    <a href="{{ route('employer.jobs.create') }}" class="btn-primary rounded-xl px-4 py-2 text-sm">+ Post a job</a>
    <form method="GET" class="flex gap-2"><input name="q" value="{{ request('q') }}" placeholder="Search jobs" class="input-glass w-56"><select name="status" class="input-glass w-auto"><option value="">All statuses</option>@foreach(\App\Models\Job::STATUSES as $s)<option @selected(request('status') == $s)>{{ $s }}</option>@endforeach</select><button class="btn-ghost rounded-xl px-3 py-2 text-sm">Filter</button></form>
</div>
@if($jobs->isEmpty())<div class="mt-6"><x-empty-state title="No jobs yet" message="Create your first job draft and publish it when ready." /></div>
@else
<div class="glass mt-6 overflow-x-auto rounded-2xl"><table class="glass-table w-full min-w-[680px]"><thead><tr><th>Job</th><th>Company</th><th>Status</th><th>Applications</th><th>Views</th><th>Deadline</th><th>Actions</th></tr></thead><tbody>
@foreach($jobs as $job)<tr>
    <td><a class="font-medium hover:text-cyan-300" href="{{ route('employer.jobs.show', $job) }}">{{ $job->title }}</a><p class="text-xs text-slate-400">{{ $job->employment_type }} · {{ $job->workplace_type }}</p></td>
    <td>{{ $job->company->name }}</td><td><x-badge :status="$job->status" /></td><td>{{ $job->applications_count }}</td><td>{{ $job->views_count }}</td><td class="text-sm">{{ $job->application_deadline?->format('d M Y') ?? '—' }}</td>
    <td class="whitespace-nowrap"><a class="text-sm text-cyan-300 hover:underline" href="{{ route('employer.jobs.edit', $job) }}">Edit</a> <a class="ml-2 text-sm text-cyan-300 hover:underline" href="{{ route('employer.applicants.index', ['job_id' => $job->id]) }}">Applicants</a></td>
</tr>@endforeach</tbody></table></div>
<div class="mt-4">{{ $jobs->links() }}</div>
@endif
@endsection
