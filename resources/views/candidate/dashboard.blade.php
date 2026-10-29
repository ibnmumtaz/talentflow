@extends('layouts.dashboard', ['heading' => 'Welcome back, '.explode(' ', $user->name)[0], 'subheading' => 'Your job search, at a glance.'])
@section('dashboard')
@php $completeness = $profile->completeness(); @endphp
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <x-stat-card label="Applications" :value="$user->applications()->count()" hint="Total submitted" />
    <x-stat-card label="Saved jobs" :value="$user->savedJobs()->count()" hint="Jobs you bookmarked" />
    <x-stat-card label="Interviews" :value="\App\Models\Interview::whereHas('application', fn($q) => $q->where('user_id', $user->id))->count()" hint="All scheduled" />
    <x-stat-card label="Profile completeness" :value="$completeness.'%'" hint="Complete your profile to stand out" />
</div>

<div class="glass mt-6 rounded-2xl p-5">
    <div class="flex items-center justify-between"><h2 class="font-semibold">Profile completeness — {{ $completeness }}%</h2><a href="{{ route('candidate.profile.edit') }}" class="text-sm text-cyan-300 hover:underline">Edit profile →</a></div>
    <div class="mt-3 h-2.5 overflow-hidden rounded-full bg-white/10"><div class="h-full rounded-full bg-gradient-to-r from-violet-500 to-cyan-400" style="width: {{ $completeness }}%"></div></div>
    <p class="mt-2 text-sm text-slate-400">Add a headline, summary, experience, education, skills and a CV to reach 100%.</p>
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-2">
    <section class="glass rounded-2xl p-5">
        <h2 class="font-semibold">Recent applications</h2>
        @if($applications->isEmpty())<p class="mt-3 text-sm text-slate-400">No applications yet. <a class="text-cyan-300 hover:underline" href="{{ route('jobs.index') }}">Browse jobs →</a></p>
        @else<ul class="mt-3 space-y-3">@foreach($applications as $a)<li class="flex items-center justify-between gap-3"><div class="min-w-0"><a class="truncate font-medium hover:text-cyan-300" href="{{ route('candidate.applications.show', $a) }}">{{ $a->job->title }}</a><p class="text-xs text-slate-400">{{ $a->job->company->name }} · {{ $a->created_at->diffForHumans() }}</p></div><x-badge :status="$a->status" /></li>@endforeach</ul>@endif
        <h3 class="mt-6 text-sm font-semibold text-slate-300">Applications by status</h3>
        <div class="mt-2 flex flex-wrap gap-2">@foreach($byStatus as $s => $c)<span class="rounded-full bg-white/5 px-3 py-1 text-xs">{{ ucwords(str_replace('_', ' ', $s)) }}: {{ $c }}</span>@endforeach @if($byStatus->isEmpty())<span class="text-sm text-slate-500">None yet.</span>@endif</div>
    </section>
    <section class="glass rounded-2xl p-5">
        <h2 class="font-semibold">Upcoming interviews</h2>
        @if($interviews->isEmpty())<p class="mt-3 text-sm text-slate-400">No upcoming interviews.</p>
        @else<ul class="mt-3 space-y-3">@foreach($interviews as $i)<li><p class="font-medium">{{ $i->title }} — {{ $i->job->title }}</p><p class="text-xs text-slate-400">{{ $i->scheduled_at->format('l, d M Y H:i') }} ({{ $i->timezone }}) · {{ ucfirst($i->type) }} @if($i->meeting_link)· <a class="text-cyan-300" href="{{ $i->meeting_link }}" target="_blank" rel="noopener">Join link</a>@endif</p></li>@endforeach</ul>@endif
        <h2 class="mt-6 font-semibold">Saved jobs</h2>
        @if($saved->isEmpty())<p class="mt-2 text-sm text-slate-400">No saved jobs yet.</p>
        @else<ul class="mt-2 space-y-2">@foreach($saved as $s)<li><a class="hover:text-cyan-300" href="{{ route('jobs.show', $s->job) }}">{{ $s->job->title }}</a> <span class="text-xs text-slate-400">· {{ $s->job->company->name }}</span></li>@endforeach</ul>@endif
    </section>
</div>

<section class="mt-6">
    <h2 class="font-semibold">Recommended for you</h2>
    <div class="mt-3 grid gap-4 md:grid-cols-2">@foreach($recommended as $job)<x-job-card :job="$job" />@endforeach</div>
</section>
@endsection
