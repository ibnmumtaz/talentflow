@extends('layouts.dashboard', ['heading' => 'Notifications', 'subheading' => 'Application, interview and platform updates.'])
@section('dashboard')
<form method="POST" action="{{ route('notifications.read-all') }}" class="mb-4">@csrf<button class="btn-ghost rounded-xl px-4 py-2 text-sm">Mark all as read</button></form>
@if($notifications->isEmpty())<x-empty-state title="No notifications" message="You will see application updates, interviews and moderation outcomes here." />
@else<div class="space-y-3">@foreach($notifications as $n)
<a href="{{ route('notifications.read', $n->id) }}" class="glass {{ $n->read_at ? '' : 'glass-strong' }} block rounded-2xl p-4">
    <p class="font-medium">{{ $n->data['title'] ?? 'Notification' }} @if(!$n->read_at)<span class="ml-1 text-xs text-cyan-300">● unread</span>@endif</p>
    <p class="mt-1 text-sm text-slate-300">{{ $n->data['message'] ?? '' }}</p>
    <p class="mt-1 text-xs text-slate-500">{{ $n->created_at->diffForHumans() }}</p>
</a>
@endforeach</div><div class="mt-6">{{ $notifications->links() }}</div>@endif
@endsection
