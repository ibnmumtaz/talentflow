@extends('layouts.dashboard', ['heading' => 'Job alerts', 'subheading' => 'Get notified when new jobs match your criteria.'])
@section('dashboard')
<form method="POST" action="{{ route('candidate.alerts.store') }}" class="glass space-y-3 rounded-2xl p-6">
    @csrf
    <h2 class="font-semibold">Create a job alert</h2>
    <div class="grid gap-3 sm:grid-cols-2">
        <div><label class="text-sm" for="name">Alert name</label><input id="name" name="name" required value="Job alert" class="input-glass mt-1"></div>
        <div><label class="text-sm" for="keywords">Keywords</label><input id="keywords" name="keywords" placeholder="e.g. Laravel" class="input-glass mt-1"></div>
        <div><label class="text-sm" for="category_id">Category</label><select id="category_id" name="category_id" class="input-glass mt-1"><option value="">Any</option>@foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
        <div><label class="text-sm" for="location_id">Location</label><select id="location_id" name="location_id" class="input-glass mt-1"><option value="">Any</option>@foreach($locations as $l)<option value="{{ $l->id }}">{{ $l->name }}</option>@endforeach</select></div>
        <div><label class="text-sm" for="workplace_type">Workplace</label><select id="workplace_type" name="workplace_type" class="input-glass mt-1"><option value="">Any</option><option value="remote">Remote</option><option value="hybrid">Hybrid</option><option value="onsite">On-site</option></select></div>
        <div><label class="text-sm" for="frequency">Frequency</label><select id="frequency" name="frequency" class="input-glass mt-1"><option value="daily">Daily</option><option value="instant">Instant</option><option value="weekly">Weekly</option></select></div>
    </div>
    <button class="btn-primary rounded-xl px-5 py-2.5 text-sm">Create alert</button>
    <p class="text-xs text-slate-500">Alerts are processed by the scheduled command <code>talentflow:job-alerts</code>. See INSTALLATION.md for the scheduler setup.</p>
</form>

@if($alerts->isEmpty())<div class="mt-6"><x-empty-state title="No job alerts" message="Create your first alert above." /></div>
@else<div class="glass mt-6 overflow-x-auto rounded-2xl"><table class="glass-table w-full min-w-[560px]"><thead><tr><th>Name</th><th>Keywords</th><th>Category</th><th>Location</th><th>Frequency</th><th>Active</th><th>Actions</th></tr></thead><tbody>
@foreach($alerts as $a)<tr><td class="font-medium">{{ $a->name }}</td><td>{{ $a->keywords ?? '—' }}</td><td>{{ $a->category->name ?? '—' }}</td><td>{{ $a->location->name ?? '—' }}</td><td>{{ $a->frequency }}</td>
<td><form method="POST" action="{{ route('candidate.alerts.update', $a) }}">@csrf @method('PUT')<input type="hidden" name="is_active" value="{{ $a->is_active ? 0 : 1 }}"><button class="text-sm {{ $a->is_active ? 'text-emerald-300' : 'text-slate-400' }}">{{ $a->is_active ? 'Active' : 'Paused' }}</button></form></td>
<td><form method="POST" action="{{ route('candidate.alerts.destroy', $a) }}" data-confirm="Delete this alert?">@csrf @method('DELETE')<button class="text-sm text-rose-300">Delete</button></form></td></tr>@endforeach
</tbody></table></div><div class="mt-4">{{ $alerts->links() }}</div>@endif
@endsection
