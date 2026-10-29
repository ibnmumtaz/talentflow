@extends('layouts.app')
@section('title', 'Create your account')
@section('content')
<div class="mx-auto mt-10 max-w-md">
    <div class="glass-strong rounded-2xl p-6 sm:p-8">
        <h1 class="text-2xl font-bold tracking-tight">Create your account</h1>
        <p class="mt-1 text-sm text-slate-400">Free for job seekers. Employers can post jobs after onboarding.</p>
        <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
            @csrf
            <div><label class="text-sm" for="name">Full name</label><input id="name" name="name" required autofocus value="{{ old('name') }}" class="input-glass mt-1" autocomplete="name"></div>
            <div><label class="text-sm" for="email">Email</label><input id="email" type="email" name="email" required value="{{ old('email') }}" class="input-glass mt-1" autocomplete="email"></div>
            <div>
                <label class="text-sm" for="role">I am joining as</label>
                <select id="role" name="role" class="input-glass mt-1"><option value="candidate" @selected(old('role', $role) == 'candidate')>Job seeker / Candidate</option><option value="employer" @selected(old('role', $role) == 'employer')>Employer / Recruiter</option></select>
            </div>
            <div><label class="text-sm" for="password">Password</label><input id="password" type="password" name="password" required class="input-glass mt-1" autocomplete="new-password"><p class="mt-1 text-xs text-slate-500">At least 8 characters.</p></div>
            <div><label class="text-sm" for="password_confirmation">Confirm password</label><input id="password_confirmation" type="password" name="password_confirmation" required class="input-glass mt-1" autocomplete="new-password"></div>
            <button class="btn-primary w-full rounded-xl px-4 py-2.5 text-sm">Create account</button>
        </form>
        <p class="mt-4 text-sm text-slate-400">Already have an account? <a class="text-cyan-300 hover:underline" href="{{ route('login') }}">Log in</a></p>
    </div>
</div>
@endsection
