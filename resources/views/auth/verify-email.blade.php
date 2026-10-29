@extends('layouts.app')
@section('title', 'Verify your email')
@section('content')
<div class="mx-auto mt-10 max-w-md"><div class="glass-strong rounded-2xl p-6 sm:p-8">
    <h1 class="text-2xl font-bold">Verify your email</h1>
    <p class="mt-1 text-sm text-slate-400">We sent a verification link to {{ auth()->user()->email }}. In this demo, the link is written to the application log (log mailer). Click it, or request a new one below.</p>
    <form method="POST" action="{{ route('verification.send') }}" class="mt-6">@csrf<button class="btn-primary w-full rounded-xl px-4 py-2.5 text-sm">Resend verification email</button></form>
    <p class="mt-4 text-sm"><a class="text-cyan-300 hover:underline" href="{{ auth()->user()->dashboardUrl() }}">Continue to my dashboard →</a></p>
</div></div>
@endsection
