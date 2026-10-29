@extends('layouts.app')
@section('title', 'Pricing')
@section('content')
<div class="mt-8 text-center"><h1 class="text-3xl font-bold tracking-tight">Simple pricing, configurable plans</h1><p class="mt-1 text-sm text-slate-400">Plans are stored in the database and managed by administrators. No payment is collected in this local demo.</p></div>
<div class="mt-8 grid gap-4 md:grid-cols-3">
    @forelse($plans as $plan)
        <div class="glass {{ $plan->is_popular ? 'glass-strong border-violet-400/40' : '' }} rounded-2xl p-6">
            @if($plan->is_popular)<p class="text-xs font-semibold text-cyan-300">MOST POPULAR</p>@endif
            <h2 class="mt-1 text-lg font-bold">{{ $plan->name }}</h2>
            <p class="mt-2 text-3xl font-extrabold">{{ $plan->price > 0 ? $plan->currency.' '.number_format($plan->price) : 'Free' }} <span class="text-sm font-normal text-slate-400">/ {{ $plan->billing_period }}</span></p>
            <p class="mt-2 text-sm text-slate-400">{{ $plan->description }}</p>
            <ul class="mt-4 space-y-2 text-sm text-slate-300">
                <li>✓ {{ $plan->job_posts_limit }} active job post(s)</li>
                <li>✓ {{ $plan->featured_jobs_limit }} featured job(s)</li>
                @foreach((array) ($plan->features ?? []) as $f)<li>✓ {{ $f }}</li>@endforeach
            </ul>
            <a href="{{ route('register', ['role' => 'employer']) }}" class="btn-primary mt-6 block rounded-xl px-4 py-2.5 text-center text-sm">Start hiring</a>
        </div>
    @empty
        <x-empty-state title="No plans configured" message="An administrator can add subscription plans from the admin panel." />
    @endforelse
</div>
<p class="mt-6 text-center text-sm text-slate-400">Subscription architecture is ready for a payment provider, but none is required for the local demo.</p>
@endsection
