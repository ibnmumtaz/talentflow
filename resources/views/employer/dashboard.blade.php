@extends('layouts.dashboard', ['heading' => 'Employer dashboard', 'subheading' => 'Recruitment at a glance across your companies.'])
@section('dashboard')
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <x-stat-card label="Active jobs" :value="$activeJobs" hint="Published jobs" />
    <x-stat-card label="Applications" :value="array_sum($byStage->toArray())" hint="All time" />
    <x-stat-card label="Hire rate" :value="$conversion.'%'" hint="Hired / applications" />
    <x-stat-card label="Companies" :value="$companies->count()" hint="You are a member of" />
</div>
<div class="mt-6 grid gap-6 lg:grid-cols-2">
    <section class="glass rounded-2xl p-5">
        <h2 class="font-semibold">Applicants by stage</h2>
        <div class="mt-3 flex flex-wrap gap-2">@forelse($byStage as $s => $c)<span class="rounded-full bg-white/5 px-3 py-1 text-xs">{{ ucwords(str_replace('_', ' ', $s)) }}: {{ $c }}</span>@empty<p class="text-sm text-slate-400">No applications yet.</p>@endforelse</div>
        <h2 class="mt-6 font-semibold">Recent applications</h2>
        <ul class="mt-3 space-y-3">@forelse($recent as $a)<li class="flex justify-between gap-3"><div class="min-w-0"><a class="font-medium hover:text-cyan-300" href="{{ route('employer.applicants.show', $a) }}">{{ $a->user->name }}</a><p class="text-xs text-slate-400">{{ $a->job->title }} · {{ $a->created_at->diffForHumans() }}</p></div><x-badge :status="$a->status" /></li>@empty<li class="text-sm text-slate-400">No applications yet. <a class="text-cyan-300" href="{{ route('employer.jobs.create') }}">Post a job →</a></li>@endforelse</ul>
    </section>
    <section class="glass rounded-2xl p-5">
        <h2 class="font-semibold">Upcoming interviews</h2>
        <ul class="mt-3 space-y-3">@forelse($interviews as $i)<li><p class="font-medium">{{ $i->title }} — {{ $i->application->user->name }}</p><p class="text-xs text-slate-400">{{ $i->scheduled_at->format('l, d M Y H:i') }} · {{ $i->job->title }}</p></li>@empty<li class="text-sm text-slate-400">No upcoming interviews.</li>@endforelse</ul>
        <h2 class="mt-6 font-semibold">Your companies</h2>
        <ul class="mt-3 space-y-2">@foreach($companies as $c)<li class="flex justify-between text-sm"><a class="hover:text-cyan-300" href="{{ route('employer.company.edit') }}">{{ $c->name }}</a><span class="text-slate-400">{{ $c->published_jobs_count }} jobs · {{ $c->verification_status }}</span></li>@endforeach</ul>
        <a href="{{ route('employer.jobs.create') }}" class="btn-primary mt-6 inline-block rounded-xl px-4 py-2 text-sm">Post a new job</a>
    </section>
</div>
@endsection
