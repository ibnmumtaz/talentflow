@extends('layouts.app')
@section('title', $article->title)
@section('description', $article->excerpt)
@section('content')
<article class="mx-auto mt-8 max-w-3xl">
    <p class="text-xs font-semibold uppercase tracking-wider text-cyan-300">{{ $article->category }}</p>
    <h1 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">{{ $article->title }}</h1>
    <p class="mt-2 text-sm text-slate-400">{{ $article->reading_time_minutes }} min read · {{ $article->published_at?->format('d M Y') }} @if($article->author) · by {{ $article->author->name }} @endif</p>
    <div class="glass mt-6 rounded-2xl p-6 sm:p-8 whitespace-pre-line leading-relaxed text-slate-200">{{ $article->body }}</div>
</article>
@if($related->isNotEmpty())
<section class="mt-10"><h2 class="text-xl font-bold">Related articles</h2><div class="mt-4 grid gap-4 md:grid-cols-3">@foreach($related as $r)<a href="{{ route('resources.show', $r) }}" class="glass glass-hover block rounded-2xl p-5"><h3 class="font-semibold">{{ $r->title }}</h3><p class="mt-2 line-clamp-2 text-sm text-slate-400">{{ $r->excerpt }}</p></a>@endforeach</div></section>
@endif
@endsection
