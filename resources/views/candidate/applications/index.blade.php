@extends('layouts.dashboard', ['heading' => 'My applications', 'subheading' => 'Track every stage, from submitted to hired.'])
@section('dashboard')
<form method="GET" class="glass flex flex-wrap gap-2 rounded-2xl p-3">
    <select name="status" class="input-glass w-auto"><option value="">All statuses</option>@foreach(\App\Models\Application::STAGES as $s)<option value="{{ $s }}" @selected(request('status') == $s)>{{ ucwords(str_replace('_', ' ', $s)) }}</option>@endforeach</select>
    <button class="btn-ghost rounded-xl px-4 py-2 text-sm">Filter</button>
</form>
@if($applications->isEmpty())<div class="mt-6"><x-empty-state title="No applications yet" message="Browse jobs and apply — your applications will appear here with their status timeline." /></div>
@else
<div class="glass mt-6 overflow-x-auto rounded-2xl"><table class="glass-table w-full min-w-[640px]">
    <thead><tr><th>Job</th><th>Company</th><th>Status</th><th>Applied</th><th></th></tr></thead>
    <tbody>@foreach($applications as $a)<tr>
        <td><a class="font-medium hover:text-cyan-300" href="{{ route('candidate.applications.show', $a) }}">{{ $a->job->title }}</a></td>
        <td>{{ $a->job->company->name }}</td><td><x-badge :status="$a->status" /></td><td class="text-sm text-slate-400">{{ $a->created_at->format('d M Y') }}</td>
        <td><a class="text-sm text-cyan-300 hover:underline" href="{{ route('candidate.applications.show', $a) }}">View →</a></td>
    </tr>@endforeach</tbody>
</table></div>
<div class="mt-4">{{ $applications->links() }}</div>
@endif
@endsection
