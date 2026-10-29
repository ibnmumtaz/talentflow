@extends('layouts.dashboard', ['heading' => 'Audit logs', 'subheading' => 'Critical administrative and recruitment actions.'])
@section('dashboard')
<div class="glass overflow-x-auto rounded-2xl"><table class="glass-table w-full min-w-[640px]"><thead><tr><th>When</th><th>User</th><th>Action</th><th>Subject</th><th>IP</th></tr></thead><tbody>
@foreach($logs as $log)<tr><td class="text-sm">{{ $log->created_at->format('d M Y H:i') }}</td><td>{{ $log->user->name ?? 'System' }}</td><td class="font-mono text-xs">{{ $log->action }}</td><td class="text-sm">{{ $log->auditable_type ? class_basename($log->auditable_type).' #'.$log->auditable_id : '—' }}</td><td class="text-sm text-slate-400">{{ $log->ip_address ?? '—' }}</td></tr>@endforeach
</tbody></table></div>
<div class="mt-4">{{ $logs->links() }}</div>
@endsection
