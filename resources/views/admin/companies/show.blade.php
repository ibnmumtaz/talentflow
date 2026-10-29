@extends('layouts.dashboard', ['heading' => $company->name, 'subheading' => 'Verification: '.$company->verification_status])
@section('dashboard')
<div class="grid gap-6 lg:grid-cols-2">
    <div class="glass rounded-2xl p-6">
        <p class="text-sm text-slate-300">{{ $company->description }}</p>
        <p class="mt-3 text-sm">Industry: {{ $company->industry->name ?? '—' }} · Location: {{ $company->location->name ?? '—' }} · Size: {{ $company->size ?? '—' }}<br>Website: {{ $company->website ?? '—' }} · Email: {{ $company->email ?? '—' }}</p>
        <h2 class="mt-5 font-semibold">Members</h2>
        <ul class="mt-2 space-y-1 text-sm">@foreach($company->memberships as $m)<li>{{ $m->user->name }} ({{ $m->role }}) — {{ $m->user->email }}</li>@endforeach</ul>
        <h2 class="mt-5 font-semibold">Verification requests</h2>
        <ul class="mt-2 space-y-2 text-sm">@forelse($company->verifications as $v)<li>{{ $v->status }} — {{ $v->details }} <span class="text-slate-400">({{ $v->created_at->format('d M Y') }})</span></li>@empty<li class="text-slate-400">None.</li>@endforelse</ul>
    </div>
    <div class="space-y-4">
        <form method="POST" action="{{ route('admin.companies.verify', $company) }}" class="glass space-y-3 rounded-2xl p-6">@csrf
            <h2 class="font-semibold">Verification decision</h2>
            <select name="decision" class="input-glass"><option value="verified">Approve / verify</option><option value="rejected">Reject</option></select>
            <textarea name="notes" rows="3" placeholder="Notes for the company (optional)" class="input-glass"></textarea>
            <button class="btn-primary rounded-xl px-4 py-2 text-sm">Save decision</button>
        </form>
        <form method="POST" action="{{ route('admin.companies.toggle-active', $company) }}" class="glass rounded-2xl p-6" data-confirm="Toggle this company's public visibility?">@csrf<button class="btn-ghost rounded-xl px-4 py-2 text-sm">{{ $company->is_active ? 'Deactivate company' : 'Activate company' }}</button></form>
        <div class="glass rounded-2xl p-6"><h2 class="font-semibold">Jobs ({{ $company->jobs->count() }})</h2><ul class="mt-2 space-y-1 text-sm">@foreach($company->jobs as $j)<li class="flex justify-between"><span>{{ $j->title }}</span><x-badge :status="$j->status" /></li>@endforeach</ul></div>
    </div>
</div>
@endsection
