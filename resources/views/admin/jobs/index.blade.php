@extends('layouts.dashboard', ['heading' => 'Job moderation', 'subheading' => 'Approve or reject pending jobs. Published jobs appear on the public site immediately.'])
@section('dashboard')
<form method="GET" class="glass flex flex-wrap gap-2 rounded-2xl p-3">
    <input name="q" value="{{ request('q') }}" placeholder="Search jobs" class="input-glass flex-1 min-w-[200px]">
    <select name="status" class="input-glass w-auto"><option value="">All statuses</option>@foreach(\App\Models\Job::STATUSES as $s)<option @selected(request('status') == $s)>{{ $s }}</option>@endforeach</select>
    <button class="btn-ghost rounded-xl px-4 py-2 text-sm">Filter</button>
    <a href="{{ route('admin.exports.jobs') }}" class="btn-ghost rounded-xl px-4 py-2 text-sm">Export CSV</a>
</form>
<div class="glass mt-6 overflow-x-auto rounded-2xl"><table class="glass-table w-full min-w-[720px]"><thead><tr><th>Job</th><th>Company</th><th>Status</th><th>Applications</th><th>Featured</th><th>Actions</th></tr></thead><tbody>
@foreach($jobs as $job)<tr>
    <td><p class="font-medium">{{ $job->title }}</p><p class="text-xs text-slate-400">{{ $job->created_at->format('d M Y') }}</p></td>
    <td>{{ $job->company->name }}</td><td><x-badge :status="$job->status" /></td><td>{{ $job->applications_count }}</td><td>{{ $job->is_featured ? 'Yes' : 'No' }}</td>
    <td class="whitespace-nowrap">
        @if($job->status === 'pending')
            <form method="POST" action="{{ route('admin.jobs.moderate', $job) }}" class="inline">@csrf<input type="hidden" name="decision" value="published"><button class="text-sm text-emerald-300">Approve</button></form>
            <form method="POST" action="{{ route('admin.jobs.moderate', $job) }}" class="inline ml-2">@csrf<input type="hidden" name="decision" value="rejected"><input type="hidden" name="rejection_reason" value="Does not meet posting guidelines."><button class="text-sm text-rose-300">Reject</button></form>
        @endif
        <form method="POST" action="{{ route('admin.jobs.featured', $job) }}" class="inline ml-2">@csrf<button class="text-sm text-cyan-300">{{ $job->is_featured ? 'Unfeature' : 'Feature' }}</button></form>
        <a class="ml-2 text-sm text-cyan-300" href="{{ route('jobs.show', $job) }}" target="_blank" rel="noopener">View</a>
    </td>
</tr>@endforeach</tbody></table></div>
<div class="mt-4">{{ $jobs->links() }}</div>
@endsection
