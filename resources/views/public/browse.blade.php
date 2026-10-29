@extends('layouts.app')
@section('title', $title)
@section('content')
<div class="mt-8"><h1 class="text-3xl font-bold tracking-tight">{{ $title }}</h1></div>
<div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
    @foreach($items as $item)
        @php
            $url = $type === 'industry' ? route('companies.index', ['industry_id' => $item->id]) : ($type === 'category' ? route('jobs.index', ['category_id' => $item->id]) : route('jobs.index', ['location_id' => $item->id]));
            $count = $type === 'industry' ? ($item->companies_count ?? 0) : ($item->jobs_count ?? 0);
        @endphp
        <a href="{{ $url }}" class="glass glass-hover block rounded-2xl p-5"><p class="font-semibold">{{ $item->name }}</p><p class="mt-1 text-sm text-slate-400">{{ $count }} {{ $type === 'industry' ? 'companies' : 'open jobs' }}</p></a>
    @endforeach
</div>
@endsection
