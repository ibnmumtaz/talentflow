@extends('layouts.app')

@php
if (empty($nav)) {
    $u = auth()->user();
    if ($u && $u->isAdmin()) {
        $nav = [
            ['label'=>'Overview','url'=>route('admin.dashboard')],
            ['label'=>'Users','url'=>route('admin.users.index')],
            ['label'=>'Companies','url'=>route('admin.companies.index')],
            ['label'=>'Job moderation','url'=>route('admin.jobs.index')],
            ['label'=>'Reports','url'=>route('admin.reports.index')],
            ['label'=>'Content','url'=>route('admin.content.index')],
            ['label'=>'Lookups','url'=>route('admin.lookups.index')],
            ['label'=>'Audit logs','url'=>route('admin.audit.index')],
            ['label'=>'Settings','url'=>route('admin.settings.index')],
            ['label'=>'Notifications','url'=>route('notifications.index')],
        ];
    } elseif ($u && $u->role==='employer') {
        $nav = [
            ['label'=>'Overview','url'=>route('employer.dashboard')],
            ['label'=>'My jobs','url'=>route('employer.jobs.index')],
            ['label'=>'Post a job','url'=>route('employer.jobs.create')],
            ['label'=>'Applicants / ATS','url'=>route('employer.applicants.index')],
            ['label'=>'Interviews','url'=>route('employer.interviews.index')],
            ['label'=>'Talent pool','url'=>route('employer.talent-pool.index')],
            ['label'=>'Company profile','url'=>route('employer.company.edit')],
            ['label'=>'Team','url'=>route('employer.team.index')],
            ['label'=>'Analytics','url'=>route('employer.analytics.index')],
            ['label'=>'Notifications','url'=>route('notifications.index')],
            ['label'=>'Account settings','url'=>route('settings.index')],
        ];
    } else {
        $nav = [
            ['label'=>'Overview','url'=>route('candidate.dashboard')],
            ['label'=>'My profile','url'=>route('candidate.profile.edit')],
            ['label'=>'CVs / Resumes','url'=>route('candidate.resumes.index')],
            ['label'=>'My applications','url'=>route('candidate.applications.index')],
            ['label'=>'Saved jobs','url'=>route('candidate.saved.index')],
            ['label'=>'Job alerts','url'=>route('candidate.alerts.index')],
            ['label'=>'Interviews','url'=>route('candidate.interviews.index')],
            ['label'=>'Notifications','url'=>route('notifications.index')],
            ['label'=>'Account settings','url'=>route('settings.index')],
        ];
    }
}
@endphp
@section('content')
<div class="mt-6 grid gap-6 lg:grid-cols-[240px_1fr]" x-data="{navOpen:false}">
    <aside class="glass hidden rounded-2xl p-4 lg:block" aria-label="Dashboard navigation">
        <div class="flex items-center gap-3 px-2 py-2">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-cyan-400 font-bold">{{ auth()->user()->initials() }}</span>
            <div class="min-w-0">
                <p class="truncate text-sm font-semibold">{{ auth()->user()->name }}</p>
                <p class="text-xs text-slate-400">{{ auth()->user()->roleLabel() }}</p>
            </div>
        </div>
        <nav class="mt-4 space-y-1 text-sm">
            @foreach(($nav ?? []) as $item)
                <a href="{{ $item['url'] }}" class="block rounded-xl px-3 py-2 {{ request()->url() === $item['url'] ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">{{ $item['label'] }}</a>
            @endforeach
        </nav>
        <div class="mt-6 border-t border-white/10 pt-4 text-xs text-slate-500">
            <a class="hover:text-white" href="{{ route('home') }}">← Back to site</a>
        </div>
    </aside>
    <div class="lg:hidden">
        <button @click="navOpen=!navOpen" class="btn-ghost w-full rounded-xl px-4 py-2 text-sm">☰ Dashboard menu</button>
        <div x-show="navOpen" x-cloak class="glass mt-2 rounded-2xl p-3 text-sm">
            @foreach(($nav ?? []) as $item)
                <a href="{{ $item['url'] }}" class="block rounded-lg px-3 py-2 {{ request()->url() === $item['url'] ? 'bg-white/10 text-white' : 'text-slate-200 hover:bg-white/5' }}">{{ $item['label'] }}</a>
            @endforeach
        </div>
    </div>
    <section class="min-w-0">
        @if(!empty($heading))
            <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">{{ $heading }}</h1>
                    @if(!empty($subheading))<p class="mt-1 text-sm text-slate-400">{{ $subheading }}</p>@endif
                </div>
                @isset($actions)<div class="flex gap-2">{{ $actions }}</div>@endisset
            </div>
        @endif
        @yield('dashboard')
    </section>
</div>
@endsection
