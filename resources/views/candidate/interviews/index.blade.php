@extends('layouts.dashboard', ['heading' => 'My interviews', 'subheading' => 'All interview invitations in one place.'])
@section('dashboard')
@if($interviews->isEmpty())<x-empty-state title="No interviews" message="When an employer schedules an interview, it will appear here with the meeting link or location." />
@else<div class="space-y-4">@foreach($interviews as $i)
<div class="glass rounded-2xl p-5">
    <div class="flex flex-wrap items-center justify-between gap-3"><h2 class="font-semibold">{{ $i->title }} — {{ $i->job->title }}</h2><x-badge :status="$i->status" /></div>
    <p class="mt-2 text-sm text-slate-300">{{ $i->scheduled_at->format('l, d M Y H:i') }} ({{ $i->timezone }}) · {{ $i->duration_minutes }} minutes · {{ ucfirst($i->type) }}</p>
    @if($i->meeting_link)<p class="mt-1 text-sm"><a class="text-cyan-300 hover:underline" href="{{ $i->meeting_link }}" target="_blank" rel="noopener">Join meeting →</a></p>@endif
    @if($i->location)<p class="mt-1 text-sm text-slate-400">Location: {{ $i->location }}</p>@endif
    @if($i->notes)<p class="mt-2 text-sm text-slate-300">{{ $i->notes }}</p>@endif
    <form method="POST" action="{{ route('candidate.interviews.respond', $i) }}" class="mt-3 flex gap-2">@csrf<select name="response" class="input-glass w-auto py-1.5 text-sm"><option value="accepted" @selected($i->candidate_response === 'accepted')>Accept</option><option value="declined" @selected($i->candidate_response === 'declined')>Decline</option></select><button class="btn-ghost rounded-xl px-3 py-1.5 text-sm">Save response</button>@if($i->candidate_response)<span class="self-center text-sm text-slate-400">Your response: {{ $i->candidate_response }}</span>@endif</form>
</div>
@endforeach</div><div class="mt-6">{{ $interviews->links() }}</div>@endif
@endsection
