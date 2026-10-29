@extends('layouts.app')
@section('title', 'Session expired')
@section('content')
<div class="glass mx-auto mt-16 max-w-lg rounded-2xl p-8 text-center"><p class="text-5xl font-extrabold">419</p><h1 class="mt-2 text-xl font-bold">Session expired</h1><p class="mt-2 text-sm text-slate-400">Your session expired for security reasons. Please refresh the page and try again.</p><a href="{{ route('home') }}" class="btn-primary mt-5 inline-block rounded-xl px-4 py-2 text-sm">Back home</a></div>
@endsection
