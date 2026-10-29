@extends('layouts.app')
@section('title', 'Access denied')
@section('content')
<div class="glass mx-auto mt-16 max-w-lg rounded-2xl p-8 text-center"><p class="text-5xl font-extrabold">403</p><h1 class="mt-2 text-xl font-bold">Access denied</h1><p class="mt-2 text-sm text-slate-400">You do not have permission to view this page. If you think this is a mistake, check which account you are signed in with.</p><a href="{{ route('home') }}" class="btn-primary mt-5 inline-block rounded-xl px-4 py-2 text-sm">Back home</a></div>
@endsection
