@extends('layouts.dashboard', ['heading' => 'Admin dashboard', 'subheading' => 'Platform health, moderation and activity.'])
@section('dashboard')
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <x-stat-card label="Total users" :value="$stats['users']" hint="{{ $stats['candidates'] }} candidates · {{ $stats['employers'] }} employers" />
    <x-stat-card label="Companies" :value="$stats['companies']" hint="{{ $stats['pending_companies'] }} pending verification" />
    <x-stat-card label="Jobs" :value="$stats['jobs']" hint="{{ $stats['published_jobs'] }} published · {{ $stats['pending_jobs'] }} pending" />
    <x-stat-card label="Applications" :value="$stats['applications']" hint="{{ $stats['hired'] }} hired" />
</div>
<div class="mt-6 grid gap-6 lg:grid-cols-2">
    <div class="glass rounded-2xl p-5"><h2 class="font-semibold">Registrations — last 30 days</h2><canvas class="mt-4" height="200" data-chart='{"type":"bar","data":{"labels":{!! json_encode($registrations->pluck("d")->toArray()) !!},"datasets":[{"label":"Users","data":{!! json_encode($registrations->pluck("total")->toArray()) !!},"backgroundColor":"#8b5cf6"}]},"options":{"scales":{"y":{"beginAtZero":true}}}}'></canvas></div>
    <div class="glass rounded-2xl p-5"><h2 class="font-semibold">Applications — last 30 days</h2><canvas class="mt-4" height="200" data-chart='{"type":"line","data":{"labels":{!! json_encode($applicationsByDay->pluck("d")->toArray()) !!},"datasets":[{"label":"Applications","data":{!! json_encode($applicationsByDay->pluck("total")->toArray()) !!},"borderColor":"#22d3ee","backgroundColor":"rgba(34,211,238,0.15)","fill":true,"tension":0.3}]},"options":{"scales":{"y":{"beginAtZero":true}}}}'></canvas></div>
</div>
<div class="mt-6 grid gap-6 lg:grid-cols-3">
    <section class="glass rounded-2xl p-5"><h2 class="font-semibold">Popular categories</h2><ul class="mt-3 space-y-2 text-sm">@foreach($byCategory as $c)<li class="flex justify-between"><span>{{ $c->name }}</span><span class="text-slate-400">{{ $c->jobs_count }} jobs</span></li>@endforeach</ul></section>
    <section class="glass rounded-2xl p-5"><h2 class="font-semibold">Recent users</h2><ul class="mt-3 space-y-2 text-sm">@foreach($recentUsers as $u)<li class="flex justify-between"><span>{{ $u->name }}</span><span class="text-slate-400">{{ $u->role }}</span></li>@endforeach</ul><a class="mt-3 inline-block text-sm text-cyan-300" href="{{ route('admin.users.index') }}">Manage users →</a></section>
    <section class="glass rounded-2xl p-5"><h2 class="font-semibold">Recent jobs</h2><ul class="mt-3 space-y-2 text-sm">@foreach($recentJobs as $j)<li class="flex justify-between gap-2"><span class="truncate">{{ $j->title }}</span><x-badge :status="$j->status" /></li>@endforeach</ul><a class="mt-3 inline-block text-sm text-cyan-300" href="{{ route('admin.jobs.index') }}">Moderate jobs →</a></section>
</div>
<section class="glass mt-6 rounded-2xl p-5"><h2 class="font-semibold">Recent audit activity</h2><ul class="mt-3 space-y-2 text-sm">@forelse($recentActivity as $a)<li>{{ $a->action }} · {{ $a->user->name ?? 'System' }} · {{ $a->created_at->diffForHumans() }}</li>@empty<li class="text-slate-400">No audit entries yet. Administrative actions are logged here.</li>@endforelse</ul></section>
@endsection
