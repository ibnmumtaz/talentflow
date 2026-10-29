@extends('layouts.dashboard', ['heading' => 'Interviews', 'subheading' => 'Scheduled interviews for your companies.'])
@section('dashboard')
@if($interviews->isEmpty())<x-empty-state title="No interviews scheduled" message="Schedule interviews from an applicant's detail page." />
@else<div class="space-y-4">@foreach($interviews as $i)
<div class="glass rounded-2xl p-5">
    <div class="flex flex-wrap justify-between gap-3"><h2 class="font-semibold">{{ $i->title }} — {{ $i->application->user->name }}</h2><x-badge :status="$i->status" /></div>
    <p class="mt-2 text-sm text-slate-300">{{ $i->scheduled_at->format('l, d M Y H:i') }} ({{ $i->timezone }}) · {{ $i->duration_minutes }} min · {{ ucfirst($i->type) }} · {{ $i->job->title }}</p>
    @if($i->meeting_link)<p class="text-sm"><a class="text-cyan-300" href="{{ $i->meeting_link }}" target="_blank" rel="noopener">Meeting link →</a></p>@endif
    <div class="mt-3 flex flex-wrap gap-2">
        <form method="POST" action="{{ route('employer.interviews.update', $i) }}">@csrf @method('PUT')<input type="hidden" name="status" value="completed"><button class="btn-ghost rounded-xl px-3 py-1.5 text-sm">Mark completed</button></form>
        <form method="POST" action="{{ route('employer.interviews.update', $i) }}">@csrf @method('PUT')<input type="hidden" name="status" value="cancelled"><button class="btn-ghost rounded-xl px-3 py-1.5 text-sm">Cancel</button></form>
        <a href="{{ route('employer.applicants.show', $i->application) }}" class="btn-ghost rounded-xl px-3 py-1.5 text-sm">Open applicant →</a>
    </div>
    <form method="POST" action="{{ route('employer.interviews.feedback', $i) }}" class="mt-4 grid gap-2 sm:grid-cols-[80px_1fr_auto]">@csrf
        <input type="number" min="1" max="5" name="rating" placeholder="Rating /5" class="input-glass">
        <input name="feedback" required placeholder="Interview feedback…" class="input-glass">
        <select name="recommendation" class="input-glass"><option value="">Recommendation</option><option value="hire">Hire</option><option value="maybe">Maybe</option><option value="no_hire">No hire</option></select>
        <button class="btn-ghost rounded-xl px-3 py-2 text-sm sm:col-span-3">Save feedback</button>
    </form>
    @if($i->feedback->isNotEmpty())<ul class="mt-3 space-y-2 text-sm">@foreach($i->feedback as $f)<li class="rounded-xl bg-white/5 p-3">{{ $f->feedback }} <span class="text-slate-400">— {{ $f->author->name }} @if($f->rating) · {{ $f->rating }}/5 @endif @if($f->recommendation) · {{ $f->recommendation }} @endif</span></li>@endforeach</ul>@endif
</div>
@endforeach</div><div class="mt-6">{{ $interviews->links() }}</div>@endif
@endsection
