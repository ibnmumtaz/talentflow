@extends('layouts.dashboard', ['heading' => 'Company profile', 'subheading' => $company->name.' · Verification: '.$company->verification_status])
@section('dashboard')
<form method="POST" action="{{ route('employer.company.update', $company) }}" enctype="multipart/form-data" class="glass space-y-4 rounded-2xl p-6">
    @csrf @method('PUT')
    <div class="grid gap-4 sm:grid-cols-2">
        <div><label class="text-sm" for="name">Company name</label><input id="name" name="name" required value="{{ old('name', $company->name) }}" class="input-glass mt-1"></div>
        <div><label class="text-sm" for="email">Public email</label><input id="email" type="email" name="email" value="{{ old('email', $company->email) }}" class="input-glass mt-1"></div>
        <div><label class="text-sm" for="website">Website</label><input id="website" name="website" value="{{ old('website', $company->website) }}" class="input-glass mt-1"></div>
        <div><label class="text-sm" for="phone">Phone</label><input id="phone" name="phone" value="{{ old('phone', $company->phone) }}" class="input-glass mt-1"></div>
        <div><label class="text-sm" for="industry_id">Industry</label><select id="industry_id" name="industry_id" class="input-glass mt-1"><option value="">Select</option>@foreach($industries as $i)<option value="{{ $i->id }}" @selected(old('industry_id', $company->industry_id) == $i->id)>{{ $i->name }}</option>@endforeach</select></div>
        <div><label class="text-sm" for="location_id">Location</label><select id="location_id" name="location_id" class="input-glass mt-1"><option value="">Select</option>@foreach($locations as $l)<option value="{{ $l->id }}" @selected(old('location_id', $company->location_id) == $l->id)>{{ $l->name }}</option>@endforeach</select></div>
        <div><label class="text-sm" for="size">Company size</label><select id="size" name="size" class="input-glass mt-1"><option value="">Select</option>@foreach(['1-10','11-50','51-200','201-500','501-1000','1000+'] as $s)<option @selected(old('size', $company->size) == $s)>{{ $s }}</option>@endforeach</select></div>
        <div><label class="text-sm" for="founded_year">Founded year</label><input id="founded_year" name="founded_year" value="{{ old('founded_year', $company->founded_year) }}" class="input-glass mt-1"></div>
        <div><label class="text-sm" for="linkedin_url">LinkedIn</label><input id="linkedin_url" name="linkedin_url" value="{{ old('linkedin_url', $company->linkedin_url) }}" class="input-glass mt-1"></div>
        <div><label class="text-sm" for="twitter_url">X / Twitter</label><input id="twitter_url" name="twitter_url" value="{{ old('twitter_url', $company->twitter_url) }}" class="input-glass mt-1"></div>
    </div>
    <div><label class="text-sm" for="description">Description</label><textarea id="description" name="description" rows="4" class="input-glass mt-1">{{ old('description', $company->description) }}</textarea></div>
    <div><label class="text-sm" for="culture">Culture</label><textarea id="culture" name="culture" rows="3" class="input-glass mt-1">{{ old('culture', $company->culture) }}</textarea></div>
    <div><label class="text-sm" for="benefits">Benefits (comma separated)</label><input id="benefits" name="benefits" value="{{ old('benefits', is_array($company->benefits) ? implode(', ', $company->benefits) : '') }}" class="input-glass mt-1"></div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div><label class="text-sm" for="logo">Logo (image, max 2 MB)</label><input id="logo" type="file" name="logo" accept="image/*" class="input-glass mt-1">@if($company->logo_path)<p class="mt-1 text-xs text-slate-400">Current: {{ $company->logo_path }}</p>@endif</div>
        <div><label class="text-sm" for="cover">Cover image (max 4 MB)</label><input id="cover" type="file" name="cover" accept="image/*" class="input-glass mt-1">@if($company->cover_path)<p class="mt-1 text-xs text-slate-400">Current: {{ $company->cover_path }}</p>@endif</div>
    </div>
    <button class="btn-primary rounded-xl px-5 py-2.5 text-sm">Save company profile</button>
    <p class="text-sm"><a class="text-cyan-300 hover:underline" href="{{ route('companies.show', $company) }}" target="_blank" rel="noopener">View public profile →</a></p>
</form>

<form method="POST" action="{{ route('employer.company.verification', $company) }}" enctype="multipart/form-data" class="glass mt-6 space-y-3 rounded-2xl p-6">
    @csrf
    <h2 class="font-semibold">Request verification</h2>
    <p class="text-sm text-slate-400">Current status: <x-badge :status="$company->verification_status" />. Verified companies get a badge on every job and company page.</p>
    <div><label class="text-sm" for="details">Verification details</label><textarea id="details" name="details" required rows="3" class="input-glass mt-1" placeholder="Registered business name, NTN / registration number, official website…"></textarea></div>
    <div><label class="text-sm" for="document">Supporting document (PDF/image, max 4 MB, optional)</label><input id="document" type="file" name="document" class="input-glass mt-1"></div>
    <button class="btn-ghost rounded-xl px-4 py-2 text-sm">Submit verification request</button>
</form>
@endsection
