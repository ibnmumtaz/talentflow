<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyInvitation;
use App\Models\CompanyMembership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $company = $request->user()->activeCompanies()->first();
        if (! $company) {
            return redirect()->route('onboarding.show');
        }
        $this->authorize('manageTeam', $company);
        $members = $company->memberships()->with('user')->get();
        $invitations = $company->invitations()->latest()->get();

        return view('employer.team.index', compact('company', 'members', 'invitations'));
    }

    public function invite(Request $request, Company $company)
    {
        $this->authorize('manageTeam', $company);
        $data = $request->validate(['email' => ['required', 'email', 'max:255'], 'role' => ['required', 'in:recruiter,hiring_manager']]);
        if ($company->memberships()->whereHas('user', fn ($q) => $q->where('email', $data['email']))->exists()) {
            return back()->withErrors(['email' => 'This person is already a member of the company.']);
        }
        $invitation = CompanyInvitation::create(['company_id' => $company->id, 'invited_by_id' => $request->user()->id, 'email' => $data['email'], 'role' => $data['role'], 'token' => Str::random(48), 'expires_at' => now()->addDays(7)]);
        // In local development the invitation link is shown on the team page; email delivery uses the log mailer.
        Mail::raw("You have been invited to join {$company->name} on TalentFlow. Accept: ".route('invitations.accept', $invitation->token), function ($m) use ($data) {
            $m->to($data['email'])->subject('TalentFlow team invitation');
        });

        return back()->with('status', 'Invitation sent. In this demo the acceptance link is also shown below (email uses the log mailer).');
    }

    public function updateMember(Request $request, CompanyMembership $membership)
    {
        $this->authorize('manageTeam', $membership->company);
        $data = $request->validate(['role' => ['required', 'in:owner,hiring_manager,recruiter'], 'can_publish_jobs' => ['boolean'], 'can_view_candidates' => ['boolean'], 'can_manage_team' => ['boolean']]);
        // Protect the last owner
        if ($membership->role === 'owner' && $data['role'] !== 'owner' && $membership->company->memberships()->where('role', 'owner')->count() <= 1) {
            return back()->withErrors(['role' => 'A company must keep at least one owner.']);
        }
        $membership->update($data + ['can_publish_jobs' => $request->boolean('can_publish_jobs'), 'can_view_candidates' => $request->boolean('can_view_candidates'), 'can_manage_team' => $request->boolean('can_manage_team')]);

        return back()->with('status', 'Team member updated.');
    }

    public function removeMember(Request $request, CompanyMembership $membership)
    {
        $this->authorize('manageTeam', $membership->company);
        if ($membership->role === 'owner' && $membership->company->memberships()->where('role', 'owner')->count() <= 1) {
            return back()->withErrors(['member' => 'Cannot remove the last owner.']);
        }
        if ($membership->user_id === $request->user()->id) {
            return back()->withErrors(['member' => 'You cannot remove yourself. Ask another owner.']);
        }
        $membership->delete();

        return back()->with('status', 'Team member removed.');
    }

    public function accept(Request $request, string $token)
    {
        $invitation = CompanyInvitation::where('token', $token)->where('status', 'pending')->firstOrFail();
        if ($invitation->expires_at && $invitation->expires_at->isPast()) {
            abort(410, 'This invitation has expired.');
        }
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login')->with('status', 'Log in with the invited email address to accept the invitation.');
        }
        if (strcasecmp($user->email, $invitation->email) !== 0) {
            abort(403, 'This invitation was sent to a different email address.');
        }
        CompanyMembership::firstOrCreate(['company_id' => $invitation->company_id, 'user_id' => $user->id], ['role' => $invitation->role, 'can_publish_jobs' => true, 'can_view_candidates' => true, 'status' => 'active', 'joined_at' => now()]);
        $invitation->update(['status' => 'accepted', 'accepted_at' => now()]);

        return redirect()->route('employer.dashboard')->with('status', 'You joined '.$invitation->company->name.'.');
    }
}
