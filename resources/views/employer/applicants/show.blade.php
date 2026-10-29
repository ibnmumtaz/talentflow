@extends('layouts.dashboard', ['heading' => $application->user->name, 'subheading' => $application->job->title.' at '.$application->job->company->name])
@section('dashboard')
<div class="grid gap-6 lg:grid-cols-[1fr_340px]">
    <div class="space-y-6">
        <div class="glass rounded-2xl p-6">
            <div class="flex flex-wrap items-center justify-between gap-3"><h2 class="font-semibold">Candidate profile</h2><x-badge :status="$application->status" /></div>
            @php $p = $application->user->candidateProfile; @endphp
            <p class="mt-3 text-sm text-slate-300">{{ $p->headline ?? '' }} @if($p) · {{ $p->city ?? '' }} · {{ $p->years_of_experience ?? '' }} yrs experience @endif</p>
            @if($p?->summary)<p class="mt-2 text-sm text-slate-300">{{ $p->summary }}</p>@endif
            <div class="mt-3 flex flex-wrap gap-2">@foreach($p?->skills ?? [] as $s)<span class="rounded-full bg-white/5 px-3 py-1 text-xs">{{ $s->name }}</span>@endforeach</div>
            <h3 class="mt-5 font-semibold">Experience</h3>
            <ul class="mt-2 space-y-2 text-sm text-slate-300">@forelse($p?->experiences ?? [] as $e)<li>{{ $e->job_title }} — {{ $e->company_name }} ({{ $e->start_date?->format('M Y') }}–{{ $e->is_current ? 'Present' : $e->end_date?->format('M Y') }})</li>@empty<li class="text-slate-400">No experience listed.</li>@endforelse</ul>
            <h3 class="mt-5 font-semibold">Education</h3>
            <ul class="mt-2 space-y-2 text-sm text-slate-300">@forelse($p?->educations ?? [] as $e)<li>{{ $e->degree }} — {{ $e->institution }}</li>@empty<li class="text-slate-400">No education listed.</li>@endforelse</ul>
            @if($application->resume)<p class="mt-5 text-sm">CV: <a class="text-cyan-300 hover:underline" href="{{ route('resumes.download', $application->resume) }}">{{ $application->resume->original_filename }}</a> (authorised download)</p>@endif
            @if($application->cover_letter)<h3 class="mt-5 font-semibold">Cover letter</h3><p class="mt-2 whitespace-pre-line text-sm text-slate-300">{{ $application->cover_letter }}</p>@endif
            @if($application->answers->isNotEmpty())<h3 class="mt-5 font-semibold">Screening answers</h3><dl class="mt-2 space-y-2 text-sm">@foreach($application->answers as $ans)<div><dt class="text-slate-400">{{ $ans->question->question }}</dt><dd>{{ $ans->answer }}</dd></div>@endforeach</dl>@endif
        </div>

        <div class="glass rounded-2xl p-6">
            <h2 class="font-semibold">Status history</h2>
            <ol class="mt-3 space-y-3 text-sm">@forelse($application->histories as $h)<li><strong>{{ ucwords(str_replace('_', ' ', $h->to_status)) }}</strong> <span class="text-slate-400">{{ $h->created_at->format('d M Y H:i') }} · {{ $h->changedBy->name ?? 'System' }} @if($h->note)— {{ $h->note }}@endif</span></li>@empty<li class="text-slate-400">No history.</li>@endforelse</ol>
        </div>

        <div class="glass rounded-2xl p-6">
            <h2 class="font-semibold">Private notes <span class="text-xs font-normal text-slate-400">(never shown to candidates)</span></h2>
            <ul class="mt-3 space-y-3 text-sm">@forelse($application->notes as $n)<li class="rounded-xl bg-white/5 p-3">{{ $n->note }}<p class="mt-1 text-xs text-slate-400">{{ $n->author->name }} · {{ $n->created_at->diffForHumans() }}</p></li>@empty<li class="text-slate-400">No notes yet.</li>@endforelse</ul>
            <form method="POST" action="{{ route('employer.applicants.note', $application) }}" class="mt-4 space-y-2">@csrf
                <textarea name="note" required rows="3" placeholder="Add a private note for your hiring team…" class="input-glass"></textarea>
                <div class="flex gap-2"><input type="number" step="0.1" min="0" max="10" name="evaluation_score" value="{{ $application->evaluation_score }}" placeholder="Evaluation score / 10" class="input-glass w-48"><button class="btn-ghost rounded-xl px-4 py-2 text-sm">Save note &amp; score</button></div>
            </form>
        </div>

        <div class="glass rounded-2xl p-6">
            <h2 class="font-semibold">Schedule an interview</h2>
            <form method="POST" action="{{ route('employer.interviews.store', $application) }}" class="mt-3 grid gap-3 sm:grid-cols-2">@csrf
                <div><label class="text-sm" for="title">Title</label><input id="title" name="title" required value="Interview — {{ $application->job->title }}" class="input-glass mt-1"></div>
                <div><label class="text-sm" for="type">Type</label><select id="type" name="type" class="input-glass mt-1"><option value="online">Online</option><option value="phone">Phone</option><option value="in_person">In person</option></select></div>
                <div><label class="text-sm" for="scheduled_at">Date &amp; time</label><input id="scheduled_at" type="datetime-local" name="scheduled_at" required class="input-glass mt-1"></div>
                <div><label class="text-sm" for="duration_minutes">Duration (minutes)</label><input id="duration_minutes" type="number" min="15" max="240" name="duration_minutes" value="45" class="input-glass mt-1"></div>
                <div><label class="text-sm" for="timezone">Time zone</label><input id="timezone" name="timezone" required value="Asia/Karachi" class="input-glass mt-1"></div>
                <div><label class="text-sm" for="meeting_link">Meeting link</label><input id="meeting_link" type="url" name="meeting_link" class="input-glass mt-1"></div>
                <div class="sm:col-span-2"><label class="text-sm" for="location">Location (for in-person)</label><input id="location" name="location" class="input-glass mt-1"></div>
                <div class="sm:col-span-2"><label class="text-sm" for="notes">Notes to candidate</label><textarea id="notes" name="notes" rows="2" class="input-glass mt-1"></textarea></div>
                <button class="btn-primary rounded-xl px-4 py-2 text-sm sm:col-span-2">Schedule &amp; notify candidate</button>
            </form>
            <h3 class="mt-6 font-semibold">Interviews</h3>
            <ul class="mt-2 space-y-2 text-sm">@forelse($application->interviews as $i)<li>{{ $i->title }} — {{ $i->scheduled_at->format('d M Y H:i') }} · {{ $i->status }} · candidate: {{ $i->candidate_response ?? 'no response' }}</li>@empty<li class="text-slate-400">None yet.</li>@endforelse</ul>
        </div>
    </div>

    <aside>
        <form method="POST" action="{{ route('employer.applicants.transition', $application) }}" class="glass space-y-3 rounded-2xl p-5">
            @csrf
            <h2 class="font-semibold">Move stage</h2>
            <p class="text-xs text-slate-400">Current: {{ ucwords(str_replace('_', ' ', $application->status)) }}. Only valid transitions are listed.</p>
            <select name="status" required class="input-glass">
                @foreach(\App\Models\Application::TRANSITIONS[$application->status] ?? [] as $next)<option value="{{ $next }}">{{ ucwords(str_replace('_', ' ', $next)) }}</option>@endforeach
            </select>
            <textarea name="note" rows="2" placeholder="Optional note for the timeline" class="input-glass"></textarea>
            <button class="btn-primary w-full rounded-xl px-4 py-2 text-sm">Update stage</button>
            @if(empty(\App\Models\Application::TRANSITIONS[$application->status]))<p class="text-sm text-slate-400">This application is in a terminal stage.</p>@endif
        </form>
        <div class="glass mt-4 rounded-2xl p-5 text-sm text-slate-300">
            <p><strong class="text-white">Candidate:</strong> {{ $application->user->name }}<br>{{ $application->user->email }}</p>
            <p class="mt-2"><strong class="text-white">Applied:</strong> {{ $application->created_at->format('d M Y H:i') }}</p>
            <p class="mt-2"><strong class="text-white">Job:</strong> <a class="text-cyan-300" href="{{ route('employer.jobs.show', $application->job) }}">{{ $application->job->title }}</a></p>
        </div>
    </aside>
</div>
@endsection
