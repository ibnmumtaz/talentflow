@extends('layouts.app')
@section('title', 'Forgot password')
@section('content')
<div class="mx-auto mt-10 max-w-md"><div class="glass-strong rounded-2xl p-6 sm:p-8">
    <h1 class="text-2xl font-bold">Reset your password</h1>
    <p class="mt-1 text-sm text-slate-400">Enter your email and we will send a reset link. In this demo, links are written to the application log (log mailer).</p>
    <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">@csrf
        <div><label class="text-sm" for="email">Email</label><input id="email" type="email" name="email" required value="{{ old('email') }}" class="input-glass mt-1"></div>
        <button class="btn-primary w-full rounded-xl px-4 py-2.5 text-sm">Email password reset link</button>
    </form>
    <p class="mt-4 text-sm"><a class="text-cyan-300 hover:underline" href="{{ route('login') }}">Back to log in</a></p>
</div></div>
@endsection
