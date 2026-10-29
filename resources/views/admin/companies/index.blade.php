@extends('layouts.dashboard', ['heading' => 'Companies & verification', 'subheading' => 'Review verification requests and manage company visibility.'])
@section('dashboard')
<h2 class="font-semibold">Pending verification requests</h2>
@if($verifications->isEmpty())<p class="mt-2 text-sm text-slate-400">No pending requests.</p>
@else<div class="mt-3 space-y-3">@foreach($verifications as $v)<div class="glass rounded-2xl p-4"><p class="font-medium">{{ $v->company->name }} — requested by {{ $v->requestedBy->name }} on {{ $v->created_at->format('d M Y') }}</p><p class="mt-1 text-sm text-slate-300">{{ $v->details }}</p><a class="mt-2 inline-block text-sm text-cyan-300" href="{{ route('admin.companies.show', $v->company) }}">Review company →</a></div>@endforeach</div>@endif

<form method="GET" class="glass mt-8 flex flex-wrap gap-2 rounded-2xl p-3">
    <input name="q" value="{{ request('q') }}" placeholder="Search companies" class="input-glass flex-1 min-w-[200px]">
    <select name="verification_status" class="input-glass w-auto"><option value="">All verification states</option><option value="pending" @selected(request('verification_status') == 'pending')>Pending</option><option value="verified" @selected(request('verification_status') == 'verified')>Verified</option><option value="rejected" @selected(request('verification_status') == 'rejected')>Rejected</option></select>
    <button class="btn-ghost rounded-xl px-4 py-2 text-sm">Filter</button>
</form>
<div class="glass mt-4 overflow-x-auto rounded-2xl"><table class="glass-table w-full min-w-[640px]"><thead><tr><th>Company</th><th>Industry</th><th>Verification</th><th>Jobs</th><th>Active</th><th></th></tr></thead><tbody>
@foreach($companies as $c)<tr><td class="font-medium">{{ $c->name }}</td><td>{{ $c->industry->name ?? '—' }}</td><td><x-badge :status="$c->verification_status" /></td><td>{{ $c->published_jobs_count }}</td><td>{{ $c->is_active ? 'Yes' : 'No' }}</td><td><a class="text-sm text-cyan-300 hover:underline" href="{{ route('admin.companies.show', $c) }}">Review →</a></td></tr>@endforeach
</tbody></table></div>
<div class="mt-4">{{ $companies->links() }}</div>
@endsection
