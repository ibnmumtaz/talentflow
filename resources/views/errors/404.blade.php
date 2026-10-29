@extends('layouts.app')
@section('title', 'Page not found')
@section('content')
<div class="glass mx-auto mt-16 max-w-lg rounded-2xl p-8 text-center"><p class="text-5xl font-extrabold">404</p><h1 class="mt-2 text-xl font-bold">Page not found</h1><p class="mt-2 text-sm text-slate-400">The page you are looking for may have moved, expired, or never existed. Try searching for a job instead.</p><a href="{{ route('jobs.index') }}" class="btn-primary mt-5 inline-block rounded-xl px-4 py-2 text-sm">Browse jobs</a></div>
@endsection
