<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        if ($request->filled('q')) {
            $query->where(fn ($q) => $q->where('name', 'like', '%'.$request->q.'%')->orWhere('email', 'like', '%'.$request->q.'%'));
        }
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('suspended')) {
            $query->where('is_suspended', $request->suspended === '1');
        }
        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['candidateProfile', 'memberships.company', 'applications.job']);

        return view('admin.users.show', compact('user'));
    }

    public function updateRole(Request $request, User $user)
    {
        $data = $request->validate(['role' => ['required', 'in:admin,moderator,employer,candidate']]);
        if ($user->id === $request->user()->id && $data['role'] !== 'admin') {
            return back()->withErrors(['role' => 'You cannot remove your own administrator role.']);
        }
        $old = $user->role;
        $user->update(['role' => $data['role']]);
        AuditLog::record($request->user(), 'user.role_changed', $user, ['role' => $data['role']], ['role' => $old]);

        return back()->with('status', 'Role updated.');
    }

    public function suspend(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['user' => 'You cannot suspend your own account.']);
        }
        $user->update(['is_suspended' => true, 'suspended_at' => now(), 'suspension_reason' => $request->input('reason', 'Suspended by administrator')]);
        AuditLog::record($request->user(), 'user.suspended', $user, ['is_suspended' => true]);

        return back()->with('status', 'Account suspended.');
    }

    public function reactivate(Request $request, User $user)
    {
        $user->update(['is_suspended' => false, 'suspended_at' => null, 'suspension_reason' => null]);
        AuditLog::record($request->user(), 'user.reactivated', $user, ['is_suspended' => false]);

        return back()->with('status', 'Account reactivated.');
    }
}
