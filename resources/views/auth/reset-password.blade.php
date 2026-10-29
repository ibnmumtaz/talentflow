@extends('layouts.app')
@section('title', 'Reset password')
@section('content')
<div class="mx-auto mt-10 max-w-md"><div class="glass-strong rounded-2xl p-6 sm:p-8">
    <h1 class="text-2xl font-bold">Choose a new password</h1>
    <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-4">@csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div><label class="text-sm" for="email">Email</label><input id="email" type="email" name="email" required value="{{ old('email', $email) }}" class="input-glass mt-1"></div>
        <div><label class="text-sm" for="password">New password</label><input id="password" type="password" name="password" required class="input-glass mt-1" autocomplete="new-password"></div>
        <div><label class="text-sm" for="password_confirmation">Confirm password</label><input id="password_confirmation" type="password" name="password_confirmation" required class="input-glass mt-1" autocomplete="new-password"></div>
        <button class="btn-primary w-full rounded-xl px-4 py-2.5 text-sm">Reset password</button>
    </form>
</div></div>
@endsection
