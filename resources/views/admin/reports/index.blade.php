@extends('layouts.dashboard', ['heading' => 'Reports & complaints', 'subheading' => 'Review reports submitted by users.'])
@section('dashboard')
@if($reports->isEmpty())<x-empty-state title="No reports" message="Reports about jobs and companies will appear here." />
@else<div class="space-y-4">@foreach($reports as $r)
<div class="glass rounded-2xl p-5">
    <p class="font-medium">{{ ucfirst($r->reason) }} — {{ class_basename($r->reportable_type) }} #{{ $r->reportable_id }} · <x-badge :status="$r->status" /></p>
    <p class="mt-1 text-sm text-slate-300">{{ $r->details ?? 'No details provided.' }}</p>
    <p class="mt-1 text-xs text-slate-400">Reported by {{ $r->reporter->name ?? 'Guest' }} · {{ $r->created_at->format('d M Y H:i') }}</p>
    @if($r->status === 'open')
    <form method="POST" action="{{ route('admin.reports.resolve', $r) }}" class="mt-3 flex flex-wrap gap-2">@csrf
        <input name="resolution_notes" placeholder="Resolution notes (optional)" class="input-glass flex-1 min-w-[220px]">
        <select name="status" class="input-glass w-auto"><option value="resolved">Resolve</option><option value="dismissed">Dismiss</option></select>
        <button class="btn-primary rounded-xl px-4 py-2 text-sm">Save resolution</button>
    </form>
    @endif
</div>
@endforeach</div><div class="mt-6">{{ $reports->links() }}</div>@endif
@endsection
