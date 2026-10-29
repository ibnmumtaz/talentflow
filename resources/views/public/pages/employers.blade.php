@extends('layouts.app')
@section('title', 'Recruitment solutions for employers')
@section('content')
<div class="mt-8 max-w-3xl">
    <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">Hire faster, with a pipeline you can trust.</h1>
    <p class="mt-3 text-slate-300">Post structured jobs, screen with questions, move candidates through stages, schedule interviews and keep your whole hiring team aligned — in one workspace.</p>
    <div class="mt-6 grid gap-3 sm:grid-cols-2">
        <div class="glass rounded-2xl p-5"><h2 class="font-semibold">Structured job posts</h2><p class="mt-1 text-sm text-slate-400">Salary, workplace, skills and screening questions up front.</p></div>
        <div class="glass rounded-2xl p-5"><h2 class="font-semibold">Real ATS pipeline</h2><p class="mt-1 text-sm text-slate-400">Submitted → review → shortlist → interview → offer → hired, with history.</p></div>
        <div class="glass rounded-2xl p-5"><h2 class="font-semibold">Team permissions</h2><p class="mt-1 text-sm text-slate-400">Owners, hiring managers and recruiters see only what they need.</p></div>
        <div class="glass rounded-2xl p-5"><h2 class="font-semibold">Analytics</h2><p class="mt-1 text-sm text-slate-400">Views, conversion, stage counts and hiring outcomes.</p></div>
    </div>
    <div class="mt-6 flex gap-3"><a href="{{ route('register', ['role' => 'employer']) }}" class="btn-primary rounded-xl px-5 py-2.5 text-sm">Create an employer account</a><a href="{{ route('pages.pricing') }}" class="btn-ghost rounded-xl px-5 py-2.5 text-sm">See pricing</a></div>
</div>
@endsection
