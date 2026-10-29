<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\CompanyVerification;
use App\Notifications\GenericNotification;
use Illuminate\Http\Request;

class CompanyAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Company::with(['industry'])->withCount('publishedJobs');
        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->q.'%');
        }
        if ($request->filled('verification_status')) {
            $query->where('verification_status', $request->verification_status);
        }
        $companies = $query->latest()->paginate(15)->withQueryString();
        $verifications = CompanyVerification::with(['company', 'requestedBy'])->where('status', 'pending')->latest()->limit(10)->get();

        return view('admin.companies.index', compact('companies', 'verifications'));
    }

    public function show(Company $company)
    {
        $company->load(['industry', 'location', 'memberships.user', 'verifications.requestedBy', 'jobs']);

        return view('admin.companies.show', compact('company'));
    }

    public function verify(Request $request, Company $company)
    {
        $data = $request->validate(['decision' => ['required', 'in:verified,rejected'], 'notes' => ['nullable', 'string', 'max:1000']]);
        $company->update(['verification_status' => $data['decision'], 'verification_notes' => $data['notes'] ?? null, 'verified_at' => $data['decision'] === 'verified' ? now() : null]);
        $company->verifications()->where('status', 'pending')->update(['status' => $data['decision'], 'reviewed_by_id' => $request->user()->id, 'review_notes' => $data['notes'] ?? null, 'reviewed_at' => now()]);
        AuditLog::record($request->user(), 'company.verification_'.$data['decision'], $company, ['verification_status' => $data['decision']]);
        foreach ($company->memberships()->where('role', 'owner')->with('user')->get() as $m) {
            $m->user->notify(new GenericNotification('Company verification '.$data['decision'], "Your company {$company->name} has been {$data['decision']}.", route('employer.company.edit'), 'Company verification update'));
        }

        return back()->with('status', "Company marked as {$data['decision']}.");
    }

    public function toggleActive(Request $request, Company $company)
    {
        $company->update(['is_active' => ! $company->is_active]);
        AuditLog::record($request->user(), 'company.toggled_active', $company, ['is_active' => $company->is_active]);

        return back()->with('status', $company->is_active ? 'Company activated.' : 'Company deactivated and hidden from the public site.');
    }
}
