@extends('layouts.app')
@section('title', 'Something went wrong')
@section('content')
<div class="glass mx-auto mt-16 max-w-lg rounded-2xl p-8 text-center"><p class="text-5xl font-extrabold">500</p><h1 class="mt-2 text-xl font-bold">Something went wrong</h1><p class="mt-2 text-sm text-slate-400">An unexpected error occurred. The team has been notified via the application logs. Please try again shortly.</p><a href="{{ route('home') }}" class="btn-primary mt-5 inline-block rounded-xl px-4 py-2 text-sm">Back home</a></div>
@endsection
