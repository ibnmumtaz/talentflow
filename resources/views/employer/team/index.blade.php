@extends('layouts.dashboard', ['heading' => 'Team — '.$company->name, 'subheading' => 'Invite recruiters and hiring managers. Owners keep full control.'])
@section('dashboard')
<form method="POST" action="{{ route('employer.team.invite', $company) }}" class="glass flex flex-wrap gap-2 rounded-2xl p-4">
    @csrf
    <input type="email" name="email" required placeholder="colleague@example.com" class="input-glass flex-1 min-w-[220px]">
    <select name="role" class="input-glass w-auto"><option value="recruiter">Recruiter</option><option value="hiring_manager">Hiring manager</option></select>
    <button class="btn-primary rounded-xl px-4 py-2 text-sm">Send invitation</button>
</form>

<div class="glass mt-6 overflow-x-auto rounded-2xl"><table class="glass-table w-full min-w-[640px]"><thead><tr><th>Member</th><th>Role</th><th>Can publish</th><th>Can view candidates</th><th>Can manage team</th><th>Actions</th></tr></thead><tbody>
@foreach($members as $m)<tr>
    <td><p class="font-medium">{{ $m->user->name }}</p><p class="text-xs text-slate-400">{{ $m->user->email }}</p></td>
    <td>{{ $m->role }}</td><td>{{ $m->can_publish_jobs ? 'Yes' : 'No' }}</td><td>{{ $m->can_view_candidates ? 'Yes' : 'No' }}</td><td>{{ $m->can_manage_team ? 'Yes' : 'No' }}</td>
    <td>
        <form method="POST" action="{{ route('employer.team.members.update', $m) }}" class="inline-flex flex-wrap gap-1">@csrf @method('PUT')
            <select name="role" class="input-glass w-auto py-1 text-xs"><option value="owner" @selected($m->role === 'owner')>Owner</option><option value="hiring_manager" @selected($m->role === 'hiring_manager')>Hiring manager</option><option value="recruiter" @selected($m->role === 'recruiter')>Recruiter</option></select>
            <label class="text-xs"><input type="checkbox" name="can_publish_jobs" value="1" @checked($m->can_publish_jobs)> Publish</label>
            <label class="text-xs"><input type="checkbox" name="can_view_candidates" value="1" @checked($m->can_view_candidates)> Candidates</label>
            <label class="text-xs"><input type="checkbox" name="can_manage_team" value="1" @checked($m->can_manage_team)> Team</label>
            <button class="text-xs text-cyan-300">Save</button>
        </form>
        @if($m->user_id !== auth()->id())
        <form method="POST" action="{{ route('employer.team.members.destroy', $m) }}" class="inline" data-confirm="Remove this team member?">@csrf @method('DELETE')<button class="ml-2 text-xs text-rose-300">Remove</button></form>
        @endif
    </td>
</tr>@endforeach
</tbody></table></div>

<h2 class="mt-8 font-semibold">Pending invitations</h2>
@if($invitations->isEmpty())<p class="mt-2 text-sm text-slate-400">No invitations.</p>
@else<ul class="mt-3 space-y-2 text-sm">@foreach($invitations as $inv)<li class="glass rounded-xl p-3">{{ $inv->email }} · {{ $inv->role }} · {{ $inv->status }} · expires {{ $inv->expires_at?->format('d M Y') }}<br><span class="text-xs text-slate-400">Acceptance link: {{ route('invitations.accept', $inv->token) }}</span></li>@endforeach</ul>@endif
@endsection
