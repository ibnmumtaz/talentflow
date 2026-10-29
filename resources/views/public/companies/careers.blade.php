@extends('layouts.app')
@section('title', 'Careers at '.$company->name)
@section('content')
<div class="mt-8"><h1 class="text-3xl font-bold tracking-tight">Careers at {{ $company->name }}</h1><p class="mt-1 text-sm text-slate-400">{{ $company->description ? \Illuminate\Support\Str::limit($company->description, 160) : '' }}</p></div>
@if($jobs->isEmpty())<div class="mt-6"><x-empty-state title="No open roles" message="Check back soon for new opportunities." /></div>
@else<div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">@foreach($jobs as $job)<x-job-card :job="$job" />@endforeach</div><div class="mt-6">{{ $jobs->links() }}</div>@endif
<p class="mt-6"><a class="text-cyan-300 hover:underline" href="{{ route('companies.show', $company) }}">← Back to company profile</a></p>
@endsection
