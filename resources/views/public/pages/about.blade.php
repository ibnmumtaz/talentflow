@extends('layouts.app')
@section('title', 'About TalentFlow')
@section('content')
<div class="glass mt-8 rounded-2xl p-6 sm:p-8 max-w-3xl">
    <h1 class="text-3xl font-bold tracking-tight">About TalentFlow</h1>
    <p class="mt-4 text-slate-300">TalentFlow is a recruitment platform built to make hiring clearer for everyone involved. Candidates see real jobs, real salary ranges and real application stages. Employers get a structured pipeline instead of an overflowing inbox. Administrators keep the marketplace safe with moderation, verification and audit logs.</p>
    <p class="mt-3 text-slate-300">The platform is built with Laravel, MariaDB, Tailwind CSS and Vite, and runs locally on Ubuntu without Docker or paid external services. Demo data on this installation is fictional.</p>
    <h2 class="mt-6 font-semibold">What we value</h2>
    <ul class="mt-2 list-disc pl-5 text-slate-300"><li>Transparency — stages, salaries and decisions are visible.</li><li>Privacy — CVs are private and shared only with authorised hiring teams.</li><li>Craft — a fast, accessible, polished experience on every device.</li></ul>
</div>
@endsection
