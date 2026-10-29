@extends('layouts.app')
@section('title', 'Log in')
@section('content')
<div class="mx-auto mt-10 max-w-md">
    <div class="glass-strong rounded-2xl p-6 sm:p-8">
        <h1 class="text-2xl font-bold tracking-tight">Welcome back</h1>
        <p class="mt-1 text-sm text-slate-400">Log in to your TalentFlow dashboard.</p>
        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
            @csrf
            <div><label class="text-sm" for="email">Email</label><input id="email" type="email" name="email" required autofocus value="{{ old('email') }}" class="input-glass mt-1" autocomplete="email"></div>
            <div><label class="text-sm" for="password">Password</label><input id="password" type="password" name="password" required class="input-glass mt-1" autocomplete="current-password"></div>
            <label class="flex items-center gap-2 text-sm text-slate-300"><input type="checkbox" name="remember" value="1"> Remember me</label>
            <button class="btn-primary w-full rounded-xl px-4 py-2.5 text-sm">Log in</button>
        </form>
        <p class="mt-4 text-sm text-slate-400">No account? <a class="text-cyan-300 hover:underline" href="{{ route('register') }}">Create one</a> · <a class="text-cyan-300 hover:underline" href="{{ route('password.request') }}">Forgot password?</a></p>
        <div class="mt-6 rounded-xl bg-white/5 p-3 text-xs text-slate-400">
            <p class="font-semibold text-slate-200">Demo accounts (password: password123)</p>
            <p>admin@talentflow.example · employer@talentflow.example · candidate@talentflow.example</p>
        </div>
    </div>
</div>
@endsection
