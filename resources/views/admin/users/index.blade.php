@extends('layouts.dashboard', ['heading' => 'Users', 'subheading' => 'Manage accounts, roles and suspensions. Your own admin account is protected from self-lockout.'])
@section('dashboard')
<form method="GET" class="glass flex flex-wrap gap-2 rounded-2xl p-3">
    <input name="q" value="{{ request('q') }}" placeholder="Search name or email" class="input-glass flex-1 min-w-[200px]">
    <select name="role" class="input-glass w-auto"><option value="">All roles</option>@foreach(\App\Models\User::ROLES as $k => $v)<option value="{{ $k }}" @selected(request('role') == $k)>{{ $v }}</option>@endforeach</select>
    <button class="btn-ghost rounded-xl px-4 py-2 text-sm">Filter</button>
    <a href="{{ route('admin.exports.users') }}" class="btn-ghost rounded-xl px-4 py-2 text-sm">Export CSV</a>
</form>
<div class="glass mt-6 overflow-x-auto rounded-2xl"><table class="glass-table w-full min-w-[640px]"><thead><tr><th>User</th><th>Role</th><th>Verified</th><th>Suspended</th><th>Joined</th><th></th></tr></thead><tbody>
@foreach($users as $u)<tr>
    <td><p class="font-medium">{{ $u->name }}</p><p class="text-xs text-slate-400">{{ $u->email }}</p></td>
    <td>{{ $u->roleLabel() }}</td><td>{{ $u->email_verified_at ? 'Yes' : 'No' }}</td><td>{{ $u->is_suspended ? 'Yes' : 'No' }}</td><td class="text-sm text-slate-400">{{ $u->created_at->format('d M Y') }}</td>
    <td><a class="text-sm text-cyan-300 hover:underline" href="{{ route('admin.users.show', $u) }}">Manage →</a></td>
</tr>@endforeach</tbody></table></div>
<div class="mt-4">{{ $users->links() }}</div>
@endsection
