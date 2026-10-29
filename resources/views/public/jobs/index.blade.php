@extends('layouts.app')
@section('title', $heading ?? 'Browse jobs')
@section('content')
<div class="mt-8">
    <h1 class="text-3xl font-bold tracking-tight">{{ $heading ?? 'Browse jobs' }}</h1>
    <p class="mt-1 text-sm text-slate-400">{{ $jobs->total() }} job(s) found. Only published, unexpired jobs are shown.</p>
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-[280px_1fr]">
    <aside class="glass h-fit rounded-2xl p-5 lg:sticky lg:top-24" aria-label="Filters">
        <form method="GET" action="{{ route('jobs.index') }}" class="space-y-4">
            <div><label class="text-xs font-semibold uppercase tracking-wider text-slate-400" for="q">Keywords</label><input id="q" name="q" value="{{ $validated['q'] ?? request('q') }}" class="input-glass mt-1" placeholder="Title, company, keyword"></div>
            <div><label class="text-xs font-semibold uppercase tracking-wider text-slate-400" for="category_id">Category</label>
                <select id="category_id" name="category_id" class="input-glass mt-1"><option value="">All categories</option>@foreach($categories as $c)<option value="{{ $c->id }}" @selected(($validated['category_id'] ?? request('category_id')) == $c->id)>{{ $c->name }}</option>@endforeach</select></div>
            <div><label class="text-xs font-semibold uppercase tracking-wider text-slate-400" for="location_id">Location</label>
                <select id="location_id" name="location_id" class="input-glass mt-1"><option value="">All locations</option>@foreach($locations as $l)<option value="{{ $l->id }}" @selected(($validated['location_id'] ?? request('location_id')) == $l->id)>{{ $l->name }}</option>@endforeach</select></div>
            <div><label class="text-xs font-semibold uppercase tracking-wider text-slate-400" for="employment_type">Employment type</label>
                <select id="employment_type" name="employment_type" class="input-glass mt-1"><option value="">Any</option>@foreach(\App\Models\Job::EMPLOYMENT_TYPES as $k => $v)<option value="{{ $k }}" @selected(($validated['employment_type'] ?? request('employment_type')) == $k)>{{ $v }}</option>@endforeach</select></div>
            <div><label class="text-xs font-semibold uppercase tracking-wider text-slate-400" for="workplace_type">Workplace</label>
                <select id="workplace_type" name="workplace_type" class="input-glass mt-1"><option value="">Any</option>@foreach(\App\Models\Job::WORKPLACE_TYPES as $k => $v)<option value="{{ $k }}" @selected(($validated['workplace_type'] ?? request('workplace_type')) == $k)>{{ $v }}</option>@endforeach</select></div>
            <div><label class="text-xs font-semibold uppercase tracking-wider text-slate-400" for="experience_level">Experience</label>
                <select id="experience_level" name="experience_level" class="input-glass mt-1"><option value="">Any</option>@foreach(\App\Models\Job::EXPERIENCE_LEVELS as $k => $v)<option value="{{ $k }}" @selected(($validated['experience_level'] ?? request('experience_level')) == $k)>{{ $v }}</option>@endforeach</select></div>
            <div class="grid grid-cols-2 gap-2">
                <div><label class="text-xs text-slate-400" for="salary_min">Min salary</label><input id="salary_min" type="number" name="salary_min" value="{{ $validated['salary_min'] ?? request('salary_min') }}" class="input-glass mt-1" placeholder="0"></div>
                <div><label class="text-xs text-slate-400" for="salary_max">Max salary</label><input id="salary_max" type="number" name="salary_max" value="{{ $validated['salary_max'] ?? request('salary_max') }}" class="input-glass mt-1" placeholder="0"></div>
            </div>
            <div><label class="text-xs font-semibold uppercase tracking-wider text-slate-400" for="posted">Date posted</label>
                <select id="posted" name="posted" class="input-glass mt-1"><option value="">Any time</option><option value="1" @selected(request('posted') == '1')>Last 24 hours</option><option value="7" @selected(request('posted') == '7')>Last 7 days</option><option value="30" @selected(request('posted') == '30')>Last 30 days</option></select></div>
            <div><label class="text-xs font-semibold uppercase tracking-wider text-slate-400" for="sort">Sort by</label>
                <select id="sort" name="sort" class="input-glass mt-1"><option value="newest" @selected(request('sort', 'newest') == 'newest')>Newest</option><option value="salary_desc" @selected(request('sort') == 'salary_desc')>Salary: high to low</option><option value="salary_asc" @selected(request('sort') == 'salary_asc')>Salary: low to high</option></select></div>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remote_only" value="1" @checked(request('remote_only'))> Remote only</label>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="verified" value="1" @checked(request('verified'))> Verified employers only</label>
            <div class="flex gap-2"><button class="btn-primary flex-1 rounded-xl px-3 py-2 text-sm">Apply filters</button><a href="{{ route('jobs.index') }}" class="btn-ghost rounded-xl px-3 py-2 text-sm">Reset</a></div>
        </form>
    </aside>
    <section>
        @if(request()->query() && count(request()->query()) > 0)
            <div class="mb-4 flex flex-wrap gap-2 text-xs">
                @foreach(request()->query() as $k => $v)
                    @if($v !== null && $v !== '' && $k !== 'page')<span class="rounded-full bg-white/10 px-3 py-1">{{ $k }}: {{ is_array($v) ? implode(', ', $v) : $v }}</span>@endif
                @endforeach
            </div>
        @endif
        @if($jobs->isEmpty())
            <x-empty-state title="No jobs match these filters" message="Try widening your search, removing a filter, or checking back soon." :action="null" />
            <p class="mt-4 text-center"><a class="text-cyan-300 hover:underline" href="{{ route('jobs.index') }}">Clear all filters</a></p>
        @else
            <div class="grid gap-4 md:grid-cols-2">@foreach($jobs as $job)<x-job-card :job="$job" />@endforeach</div>
            <div class="mt-6">{{ $jobs->links() }}</div>
        @endif
    </section>
</div>
@endsection
