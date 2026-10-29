@extends('layouts.dashboard', ['heading' => $job->exists ? 'Edit job: '.$job->title : 'Post a new job', 'subheading' => 'Complete, structured listings convert better.'])
@section('dashboard')
@php $isEdit = $job->exists; @endphp
<form method="POST" action="{{ $isEdit ? route('employer.jobs.update', $job) : route('employer.jobs.store') }}" class="glass space-y-5 rounded-2xl p-6">
    @csrf @if($isEdit) @method('PUT') @endif
    <div class="grid gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2"><label class="text-sm" for="title">Job title *</label><input id="title" name="title" required value="{{ old('title', $job->title) }}" class="input-glass mt-1"></div>
        <div><label class="text-sm" for="company_id">Company *</label><select id="company_id" name="company_id" required class="input-glass mt-1">@foreach($companies as $c)<option value="{{ $c->id }}" @selected(old('company_id', $job->company_id) == $c->id)>{{ $c->name }}</option>@endforeach</select></div>
        <div><label class="text-sm" for="category_id">Category *</label><select id="category_id" name="category_id" required class="input-glass mt-1"><option value="">Select</option>@foreach($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id', $job->category_id) == $c->id)>{{ $c->name }}</option>@endforeach</select></div>
        <div><label class="text-sm" for="location_id">Location</label><select id="location_id" name="location_id" class="input-glass mt-1"><option value="">Select / remote</option>@foreach($locations as $l)<option value="{{ $l->id }}" @selected(old('location_id', $job->location_id) == $l->id)>{{ $l->name }}</option>@endforeach</select></div>
        <div><label class="text-sm" for="employment_type">Employment type *</label><select id="employment_type" name="employment_type" class="input-glass mt-1">@foreach(\App\Models\Job::EMPLOYMENT_TYPES as $k => $v)<option value="{{ $k }}" @selected(old('employment_type', $job->employment_type) == $k)>{{ $v }}</option>@endforeach</select></div>
        <div><label class="text-sm" for="workplace_type">Workplace *</label><select id="workplace_type" name="workplace_type" class="input-glass mt-1">@foreach(\App\Models\Job::WORKPLACE_TYPES as $k => $v)<option value="{{ $k }}" @selected(old('workplace_type', $job->workplace_type) == $k)>{{ $v }}</option>@endforeach</select></div>
        <div><label class="text-sm" for="experience_level">Experience level *</label><select id="experience_level" name="experience_level" class="input-glass mt-1">@foreach(\App\Models\Job::EXPERIENCE_LEVELS as $k => $v)<option value="{{ $k }}" @selected(old('experience_level', $job->experience_level) == $k)>{{ $v }}</option>@endforeach</select></div>
        <div><label class="text-sm" for="openings">Openings</label><input id="openings" type="number" min="1" max="100" name="openings" value="{{ old('openings', $job->openings ?? 1) }}" class="input-glass mt-1"></div>
        <div><label class="text-sm" for="salary_min">Salary min (PKR)</label><input id="salary_min" type="number" min="0" name="salary_min" value="{{ old('salary_min', $job->salary_min) }}" class="input-glass mt-1"></div>
        <div><label class="text-sm" for="salary_max">Salary max (PKR)</label><input id="salary_max" type="number" min="0" name="salary_max" value="{{ old('salary_max', $job->salary_max) }}" class="input-glass mt-1"></div>
        <div><label class="text-sm" for="salary_currency">Currency</label><select id="salary_currency" name="salary_currency" class="input-glass mt-1">@foreach(['PKR','USD','GBP','EUR','AED','INR'] as $cur)<option @selected(old('salary_currency', $job->salary_currency) == $cur)>{{ $cur }}</option>@endforeach</select></div>
        <div><label class="text-sm" for="salary_period">Salary period</label><select id="salary_period" name="salary_period" class="input-glass mt-1">@foreach(['monthly','annually','hourly'] as $p)<option @selected(old('salary_period', $job->salary_period) == $p)>{{ $p }}</option>@endforeach</select></div>
        <div><label class="text-sm" for="application_deadline">Application deadline</label><input id="application_deadline" type="date" name="application_deadline" value="{{ old('application_deadline', $job->application_deadline?->format('Y-m-d')) }}" class="input-glass mt-1"></div>
        <div><label class="text-sm" for="expires_at">Listing expires</label><input id="expires_at" type="date" name="expires_at" value="{{ old('expires_at', $job->expires_at?->format('Y-m-d')) }}" class="input-glass mt-1"></div>
    </div>
    <div><label class="text-sm" for="summary">Summary</label><textarea id="summary" name="summary" rows="2" class="input-glass mt-1">{{ old('summary', $job->summary) }}</textarea></div>
    <div><label class="text-sm" for="description">Description * (at least 50 characters)</label><textarea id="description" name="description" required rows="6" class="input-glass mt-1">{{ old('description', $job->description) }}</textarea></div>
    <div><label class="text-sm" for="responsibilities">Responsibilities</label><textarea id="responsibilities" name="responsibilities" rows="3" class="input-glass mt-1">{{ old('responsibilities', $job->responsibilities) }}</textarea></div>
    <div><label class="text-sm" for="requirements">Requirements</label><textarea id="requirements" name="requirements" rows="3" class="input-glass mt-1">{{ old('requirements', $job->requirements) }}</textarea></div>
    <div><label class="text-sm" for="benefits">Benefits (comma separated)</label><input id="benefits" name="benefits" value="{{ old('benefits', is_array($job->benefits) ? implode(', ', $job->benefits) : '') }}" class="input-glass mt-1"></div>
    <div>
        <span class="text-sm">Required skills</span>
        <div class="mt-2 flex flex-wrap gap-2">@foreach($skills as $s)<label class="flex items-center gap-1.5 rounded-full bg-white/5 px-3 py-1.5 text-xs"><input type="checkbox" name="skills[]" value="{{ $s->id }}" @checked(in_array($s->id, old('skills', $job->skills->pluck('id')->all() ?? [])))> {{ $s->name }}</label>@endforeach</div>
    </div>
    <div>
        <span class="text-sm">Screening questions</span>
        @for($i = 0; $i < 3; $i++)
            @php $q = $job->screeningQuestions[$i] ?? null; @endphp
            <div class="mt-2 grid gap-2 sm:grid-cols-[1fr_160px_auto]">
                <input name="screening_questions[{{ $i }}][question]" value="{{ old("screening_questions.$i.question", $q->question ?? '') }}" placeholder="Question {{ $i + 1 }}" class="input-glass">
                <select name="screening_questions[{{ $i }}][type]" class="input-glass"><option value="text" @selected(($q->type ?? '') === 'text')>Text</option><option value="yes_no" @selected(($q->type ?? '') === 'yes_no')>Yes / No</option><option value="multiple_choice" @selected(($q->type ?? '') === 'multiple_choice')>Multiple choice</option></select>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="screening_questions[{{ $i }}][is_required]" value="1" @checked($q->is_required ?? false)> Required</label>
            </div>
        @endfor
    </div>
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="salary_visible" value="1" @checked(old('salary_visible', $job->salary_visible ?? true))> Show salary publicly</label>
    <div class="flex gap-2"><button class="btn-primary rounded-xl px-5 py-2.5 text-sm">{{ $isEdit ? 'Save changes' : 'Save as draft' }}</button><a href="{{ route('employer.jobs.index') }}" class="btn-ghost rounded-xl px-4 py-2.5 text-sm">Cancel</a></div>
    @if($isEdit)<p class="text-xs text-slate-500">Current status: {{ $job->status }}. Use the actions on the job page to publish, close or archive.</p>@endif
</form>
@endsection
