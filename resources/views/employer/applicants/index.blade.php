@extends('layouts.dashboard', ['heading' => 'Applicants & ATS pipeline', 'subheading' => 'Move candidates through stages. Invalid transitions are blocked on the server.'])
@section('dashboard')
<form method="GET" class="glass flex flex-wrap gap-2 rounded-2xl p-3">
    <select name="job_id" class="input-glass w-auto"><option value="">All jobs</option>@foreach($jobs as $j)<option value="{{ $j->id }}" @selected(request('job_id') == $j->id)>{{ $j->title }}</option>@endforeach</select>
    <select name="status" class="input-glass w-auto"><option value="">All stages</option>@foreach(\App\Models\Application::STAGES as $s)<option value="{{ $s }}" @selected(request('status') == $s)>{{ ucwords(str_replace('_', ' ', $s)) }}</option>@endforeach</select>
    <input name="q" value="{{ request('q') }}" placeholder="Search candidate" class="input-glass w-56">
    <button class="btn-ghost rounded-xl px-4 py-2 text-sm">Filter</button>
    <a href="{{ route('employer.applicants.index', ['view' => $view === 'pipeline' ? 'table' : 'pipeline']) }}" class="btn-ghost rounded-xl px-4 py-2 text-sm">{{ $view === 'pipeline' ? 'Table view' : 'Pipeline view' }}</a>
</form>

<div class="mt-4 flex flex-wrap gap-2 text-xs">@foreach(\App\Models\Application::STAGES as $s)<span class="rounded-full bg-white/5 px-3 py-1">{{ ucwords(str_replace('_', ' ', $s)) }}: {{ $stageCounts[$s] ?? 0 }}</span>@endforeach</div>

@if($view === 'pipeline')
    <div class="mt-6 grid gap-4 md:grid-cols-3 xl:grid-cols-5">
        @foreach(['submitted', 'under_review', 'shortlisted', 'interview_scheduled', 'hired'] as $stage)
            <div class="glass rounded-2xl p-3">
                <h3 class="px-1 text-sm font-semibold">{{ ucwords(str_replace('_', ' ', $stage)) }} ({{ $stageCounts[$stage] ?? 0 }})</h3>
                <div class="mt-3 space-y-2">
                    @foreach($applications->where('status', $stage) as $a)
                        <a href="{{ route('employer.applicants.show', $a) }}" class="block rounded-xl bg-white/5 p-3 hover:bg-white/10"><p class="text-sm font-medium">{{ $a->user->name }}</p><p class="truncate text-xs text-slate-400">{{ $a->job->title }}</p><p class="text-xs text-slate-500">{{ $a->created_at->format('d M') }} @if($a->evaluation_score)· Score {{ $a->evaluation_score }}@endif</p></a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    <p class="mt-3 text-xs text-slate-500">Pipeline shows the first page of results for five key stages. Use the table view for rejected, withdrawn, interview-completed and offer stages, search and bulk actions.</p>
    <div class="mt-4">{{ $applications->links() }}</div>
@else
    <form method="POST" action="{{ route('employer.applicants.bulk') }}" class="mt-6">
        @csrf
        <div class="mb-3 flex flex-wrap gap-2"><select name="status" required class="input-glass w-auto"><option value="">Bulk move to…</option>@foreach(\App\Models\Application::STAGES as $s)<option value="{{ $s }}">{{ ucwords(str_replace('_', ' ', $s)) }}</option>@endforeach</select><button class="btn-ghost rounded-xl px-4 py-2 text-sm" data-confirm="Move selected applications? Invalid transitions will be skipped.">Apply to selected</button></div>
        @if($applications->isEmpty())<x-empty-state title="No applicants yet" message="Applications will appear here once candidates apply to your published jobs." />
        @else<div class="glass overflow-x-auto rounded-2xl"><table class="glass-table w-full min-w-[720px]"><thead><tr><th></th><th>Candidate</th><th>Job</th><th>Stage</th><th>Score</th><th>Applied</th><th></th></tr></thead><tbody>
        @foreach($applications as $a)<tr>
            <td><input type="checkbox" name="ids[]" value="{{ $a->id }}" aria-label="Select {{ $a->user->name }}"></td>
            <td><a class="font-medium hover:text-cyan-300" href="{{ route('employer.applicants.show', $a) }}">{{ $a->user->name }}</a><p class="text-xs text-slate-400">{{ $a->user->email }}</p></td>
            <td>{{ $a->job->title }}</td><td><x-badge :status="$a->status" /></td><td>{{ $a->evaluation_score ?? '—' }}</td><td class="text-sm text-slate-400">{{ $a->created_at->format('d M Y') }}</td>
            <td><a class="text-sm text-cyan-300 hover:underline" href="{{ route('employer.applicants.show', $a) }}">Review →</a></td>
        </tr>@endforeach</tbody></table></div><div class="mt-4">{{ $applications->links() }}</div>@endif
    </form>
@endif
@endsection
