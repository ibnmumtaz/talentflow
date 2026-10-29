@extends('layouts.dashboard', ['heading' => $user->name, 'subheading' => $user->email.' · '.$user->roleLabel()])
@section('dashboard')
<div class="grid gap-6 lg:grid-cols-2">
    <div class="glass rounded-2xl p-6">
        <h2 class="font-semibold">Role</h2>
        <form method="POST" action="{{ route('admin.users.role', $user) }}" class="mt-3 flex gap-2">@csrf @method('PUT')
            <select name="role" class="input-glass w-auto">@foreach(\App\Models\User::ROLES as $k => $v)<option value="{{ $k }}" @selected($user->role == $k)>{{ $v }}</option>@endforeach</select>
            <button class="btn-primary rounded-xl px-4 py-2 text-sm">Update role</button>
        </form>
        <p class="mt-2 text-xs text-slate-500">Changing a role changes dashboard access immediately. The current admin cannot demote themselves.</p>
        <h2 class="mt-6 font-semibold">Account status</h2>
        @if($user->is_suspended)
            <p class="mt-2 text-sm text-rose-300">Suspended since {{ $user->suspended_at?->format('d M Y H:i') }} — {{ $user->suspension_reason }}</p>
            <form method="POST" action="{{ route('admin.users.reactivate', $user) }}" class="mt-3">@csrf<button class="btn-primary rounded-xl px-4 py-2 text-sm">Reactivate account</button></form>
        @else
            <form method="POST" action="{{ route('admin.users.suspend', $user) }}" class="mt-3 flex gap-2" data-confirm="Suspend this account? They will be logged out and cannot log in.">@csrf<input name="reason" placeholder="Suspension reason" class="input-glass flex-1"><button class="btn-ghost rounded-xl px-4 py-2 text-sm text-rose-300">Suspend account</button></form>
        @endif
    </div>
    <div class="glass rounded-2xl p-6">
        <h2 class="font-semibold">Profile &amp; activity</h2>
        <p class="mt-2 text-sm text-slate-300">Email verified: {{ $user->email_verified_at ? 'Yes ('.$user->email_verified_at->format('d M Y').')' : 'No' }}<br>Last login: {{ $user->last_login_at?->format('d M Y H:i') ?? 'Never' }}<br>Applications: {{ $user->applications()->count() }} · Companies: {{ $user->memberships()->count() }}</p>
        @if($user->candidateProfile)<p class="mt-3 text-sm">{{ $user->candidateProfile->headline }} · {{ $user->candidateProfile->city }}</p>@endif
        <h3 class="mt-4 font-semibold">Recent applications</h3>
        <ul class="mt-2 space-y-1 text-sm">@forelse($user->applications()->latest()->limit(5)->get() as $a)<li>{{ $a->job->title }} — <x-badge :status="$a->status" /></li>@empty<li class="text-slate-400">None.</li>@endforelse</ul>
    </div>
</div>
@endsection
