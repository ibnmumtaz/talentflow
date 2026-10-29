@extends('layouts.app')
@section('title', 'Browse companies')
@section('content')
<div class="mt-8"><h1 class="text-3xl font-bold tracking-tight">Companies hiring on TalentFlow</h1><p class="mt-1 text-sm text-slate-400">Verified employers are reviewed by our team.</p></div>
<form method="GET" class="glass mt-6 flex flex-col gap-2 rounded-2xl p-2 sm:flex-row" role="search">
    <input name="q" value="{{ request('q') }}" class="input-glass flex-1" placeholder="Search companies">
    <select name="industry_id" class="input-glass sm:w-56"><option value="">All industries</option>@foreach($industries as $i)<option value="{{ $i->id }}" @selected(request('industry_id') == $i->id)>{{ $i->name }}</option>@endforeach</select>
    <label class="flex items-center gap-2 px-2 text-sm"><input type="checkbox" name="verified" value="1" @checked(request('verified'))> Verified only</label>
    <button class="btn-primary rounded-xl px-4 py-2 text-sm">Search</button>
</form>
@if($companies->isEmpty())<div class="mt-6"><x-empty-state title="No companies found" message="Try a different search term." /></div>
@else
<div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
    @foreach($companies as $company)
        <a href="{{ route('companies.show', $company) }}" class="glass glass-hover block rounded-2xl p-5">
            <p class="font-semibold">{{ $company->name }} @if($company->verification_status === 'verified')<span class="text-cyan-300 text-sm">✓ Verified</span>@endif</p>
            <p class="mt-1 text-sm text-slate-400">{{ $company->industry->name ?? '' }} · {{ $company->location->name ?? '' }} · {{ $company->size ?? '' }}</p>
            <p class="mt-2 line-clamp-2 text-sm text-slate-300">{{ $company->description }}</p>
            <p class="mt-3 text-sm text-cyan-300">{{ $company->published_jobs_count }} open role(s) →</p>
        </a>
    @endforeach
</div>
<div class="mt-6">{{ $companies->links() }}</div>
@endif
@endsection
