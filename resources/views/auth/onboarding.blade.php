@extends('layouts.app')
@section('title', 'Onboarding')
@section('content')
<div class="mx-auto mt-10 max-w-xl">
    <div class="glass-strong rounded-2xl p-6 sm:p-8">
        <h1 class="text-2xl font-bold tracking-tight">A few details to get you started</h1>
        <p class="mt-1 text-sm text-slate-400">You joined as {{ $user->roleLabel() }}. You can change everything later.</p>
        <form method="POST" action="{{ route('onboarding.store') }}" class="mt-6 space-y-4">
            @csrf
            @if($user->role === 'candidate')
                <div><label class="text-sm" for="headline">Professional headline</label><input id="headline" name="headline" class="input-glass mt-1" placeholder="e.g. Laravel Developer"></div>
                <div><label class="text-sm" for="city">City</label><input id="city" name="city" class="input-glass mt-1" placeholder="Karachi"></div>
                <div><label class="text-sm" for="years_of_experience">Years of experience</label><input id="years_of_experience" type="number" min="0" max="60" name="years_of_experience" class="input-glass mt-1"></div>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="open_to_remote" value="1" checked> Open to remote work</label>
            @else
                <div><label class="text-sm" for="company_name">Company name</label><input id="company_name" name="company_name" required class="input-glass mt-1" placeholder="Your company"></div>
                <div><label class="text-sm" for="company_description">What does your company do?</label><textarea id="company_description" name="company_description" rows="3" class="input-glass mt-1"></textarea></div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div><label class="text-sm" for="industry_id">Industry</label><select id="industry_id" name="industry_id" class="input-glass mt-1"><option value="">Select</option>@foreach(\App\Models\Industry::orderBy('name')->get() as $i)<option value="{{ $i->id }}">{{ $i->name }}</option>@endforeach</select></div>
                    <div><label class="text-sm" for="location_id">Location</label><select id="location_id" name="location_id" class="input-glass mt-1"><option value="">Select</option>@foreach(\App\Models\Location::orderBy('name')->get() as $l)<option value="{{ $l->id }}">{{ $l->name }}</option>@endforeach</select></div>
                </div>
            @endif
            <button class="btn-primary w-full rounded-xl px-4 py-2.5 text-sm">Complete onboarding</button>
        </form>
    </div>
</div>
@endsection
