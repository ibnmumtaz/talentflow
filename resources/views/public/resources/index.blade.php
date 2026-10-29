@extends('layouts.app')
@section('title', 'Career resources')
@section('content')
<div class="mt-8"><h1 class="text-3xl font-bold tracking-tight">Career resources</h1><p class="mt-1 text-sm text-slate-400">Practical guides for candidates and hiring teams.</p></div>
<form method="GET" class="glass mt-6 flex gap-2 rounded-2xl p-2">
    <input name="q" value="{{ request('q') }}" class="input-glass flex-1" placeholder="Search articles">
    <select name="category" class="input-glass w-48"><option value="">All categories</option>@foreach($categories as $c)<option @selected(request('category') == $c)>{{ $c }}</option>@endforeach</select>
    <button class="btn-primary rounded-xl px-4 py-2 text-sm">Search</button>
</form>
@if($articles->isEmpty())<div class="mt-6"><x-empty-state title="No articles found" message="Try a different search." /></div>
@else<div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
    @foreach($articles as $a)
        <a href="{{ route('resources.show', $a) }}" class="glass glass-hover block rounded-2xl p-5"><p class="text-xs font-semibold uppercase tracking-wider text-cyan-300">{{ $a->category }}</p><h2 class="mt-2 font-semibold">{{ $a->title }}</h2><p class="mt-2 line-clamp-2 text-sm text-slate-400">{{ $a->excerpt }}</p><p class="mt-3 text-xs text-slate-500">{{ $a->reading_time_minutes }} min read · {{ $a->published_at?->format('d M Y') }}</p></a>
    @endforeach
</div><div class="mt-6">{{ $articles->links() }}</div>@endif
@endsection
