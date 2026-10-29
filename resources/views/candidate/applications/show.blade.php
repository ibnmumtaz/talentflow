@extends('layouts.dashboard', ['heading' => $application->job->title, 'subheading' => $application->job->company->name.' · Applied '.$application->created_at->format('d M Y')])
@section('dashboard')
<div class="grid gap-6 lg:grid-cols-[1fr_320px]">
    <div class="space-y-6">
        <div class="glass rounded-2xl p-6">
            <div class="flex flex-wrap items-center justify-between gap-3"><h2 class="font-semibold">Status</h2><x-badge :status="$application->status" /></div>
            <ol class="mt-4 space-y-3">
                @forelse($application->histories as $h)
                    <li class="flex gap-3 text-sm"><span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-cyan-400"></span><div><p><strong>{{ ucwords(str_replace('_', ' ', $h->to_status)) }}</strong> @if($h->from_status) from {{ ucwords(str_replace('_', ' ', $h->from_status)) }} @endif</p><p class="text-xs text-slate-400">{{ $h->created_at->format('d M Y H:i') }} · by {{ $h->changedBy->name ?? 'System' }} @if($h->note)— {{ $h->note }}@endif</p></div></li>
                @empty<li class="text-sm text-slate-400">No history yet.</li>@endforelse
            </ol>
            @if($application->cover_letter)<h3 class="mt-6 font-semibold">Cover letter</h3><p class="mt-2 whitespace-pre-line text-sm text-slate-300">{{ $application->cover_letter }}</p>@endif
            @if($application->answers->isNotEmpty())<h3 class="mt-6 font-semibold">Screening answers</h3><dl class="mt-2 space-y-2 text-sm">@foreach($application->answers as $ans)<div><dt class="text-slate-400">{{ $ans->question->question }}</dt><dd>{{ $ans->answer }}</dd></div>@endforeach</dl>@endif
        </div>
        <div class="glass rounded-2xl p-6">
            <h2 class="font-semibold">Interviews</h2>
            @if($application->interviews->isEmpty())<p class="mt-2 text-sm text-slate-400">No interviews scheduled yet.</p>
            @else<ul class="mt-3 space-y-3 text-sm">@foreach($application->interviews as $i)<li><p class="font-medium">{{ $i->title }} — {{ $i->scheduled_at->format('l, d M Y H:i') }} ({{ $i->timezone }})</p><p class="text-slate-400">{{ ucfirst($i->type) }} · {{ $i->status }} @if($i->meeting_link)· <a class="text-cyan-300" href="{{ $i->meeting_link }}" target="_blank" rel="noopener">Meeting link</a>@endif @if($i->location)· {{ $i->location }}@endif</p>
            <form method="POST" action="{{ route('candidate.interviews.respond', $i) }}" class="mt-2 flex gap-2">@csrf<select name="response" class="input-glass w-auto py-1 text-sm"><option value="accepted" @selected($i->candidate_response === 'accepted')>Accept</option><option value="declined" @selected($i->candidate_response === 'declined')>Decline</option></select><button class="btn-ghost rounded-xl px-3 py-1 text-sm">Save response</button></form></li>@endforeach</ul>@endif
        </div>
    </div>
    <aside class="space-y-4">
        <div class="glass rounded-2xl p-5">
            <h3 class="font-semibold">Job</h3>
            <p class="mt-2 text-sm"><a class="text-cyan-300 hover:underline" href="{{ route('jobs.show', $application->job) }}">{{ $application->job->title }}</a></p>
            <p class="text-sm text-slate-400">{{ $application->job->location->name ?? '' }} · {{ $application->job->salaryLabel() }}</p>
            @if($application->resume)<p class="mt-3 text-sm">CV: <a class="text-cyan-300 hover:underline" href="{{ route('resumes.download', $application->resume) }}">{{ $application->resume->original_filename }}</a></p>@endif
            @if($application->evaluation_score)<p class="mt-2 text-sm">Employer evaluation: <strong>{{ $application->evaluation_score }} / 10</strong></p>@endif
        </div>
        @if(!in_array($application->status, ['hired', 'rejected', 'withdrawn'], true))
        <form method="POST" action="{{ route('candidate.applications.withdraw', $application) }}" data-confirm="Withdraw this application? This cannot be undone." class="glass rounded-2xl p-5">
            @csrf<button class="btn-ghost w-full rounded-xl px-4 py-2 text-sm text-rose-300">Withdraw application</button>
        </form>
        @endif
    </aside>
</div>
@endsection
