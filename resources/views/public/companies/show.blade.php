@extends('layouts.app')
@section('title', $company->name)
@section('description', \Illuminate\Support\Str::limit($company->description ?? '', 160))
@section('content')
<div class="glass mt-8 rounded-2xl p-6 sm:p-8">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">{{ $company->name }} @if($company->verification_status === 'verified')<span class="text-base text-cyan-300">✓ Verified employer</span>@endif</h1>
            <p class="mt-2 text-slate-300">{{ $company->industry->name ?? '' }} · {{ $company->location->name ?? '' }} · {{ $company->size ?? '' }} employees @if($company->founded_year) · Founded {{ $company->founded_year }} @endif</p>
            @if($company->website)<p class="mt-1"><a class="text-cyan-300 hover:underline" href="{{ $company->website }}" rel="noopener" target="_blank">{{ $company->website }}</a></p>@endif
        </div>
        <a href="{{ route('companies.careers', $company) }}" class="btn-primary rounded-xl px-4 py-2 text-sm">View open roles</a>
    </div>
    @if($company->description)<p class="mt-5 whitespace-pre-line text-slate-200">{{ $company->description }}</p>@endif
    @if($company->culture)<h2 class="mt-6 font-semibold">Culture</h2><p class="mt-2 whitespace-pre-line text-slate-300">{{ $company->culture }}</p>@endif
    @if($company->benefits)<h2 class="mt-6 font-semibold">Benefits</h2><div class="mt-2 flex flex-wrap gap-2">@foreach((array) $company->benefits as $b)<span class="rounded-full bg-white/5 px-3 py-1 text-xs">{{ $b }}</span>@endforeach</div>@endif
</div>
<section class="mt-10">
    <h2 class="text-xl font-bold">Open roles at {{ $company->name }}</h2>
    @if($jobs->isEmpty())<div class="mt-4"><x-empty-state title="No open roles right now" message="This company has no published jobs at the moment." /></div>
    @else<div class="mt-4 grid gap-4 md:grid-cols-2">@foreach($jobs as $job)<x-job-card :job="$job" />@endforeach</div><div class="mt-6">{{ $jobs->links() }}</div>@endif
</section>
@endsection
