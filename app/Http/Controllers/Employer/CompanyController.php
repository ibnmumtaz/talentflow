<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use App\Models\CompanyVerification;
use App\Models\Industry;
use App\Models\Location;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function edit(Request $request)
    {
        $company = $request->user()->activeCompanies()->first();
        if (! $company) {
            return redirect()->route('onboarding.show');
        }
        $this->authorize('update', $company);

        return view('employer.company.edit', ['company' => $company, 'industries' => Industry::orderBy('name')->get(), 'locations' => Location::orderBy('name')->get()]);
    }

    public function update(CompanyRequest $request, Company $company)
    {
        $this->authorize('update', $company);
        $data = $request->validated();
        $benefits = $data['benefits'] ?? null;
        unset($data['benefits'], $data['logo'], $data['cover']);
        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('company-logos/'.$company->id, 'public');
        }
        if ($request->hasFile('cover')) {
            $data['cover_path'] = $request->file('cover')->store('company-covers/'.$company->id, 'public');
        }
        $data['benefits'] = $benefits ? array_values(array_filter(array_map('trim', explode(',', $benefits)))) : null;
        $company->update($data);

        return back()->with('status', 'Company profile updated.');
    }

    public function requestVerification(Request $request, Company $company)
    {
        $this->authorize('update', $company);
        $data = $request->validate(['details' => ['required', 'string', 'max:2000'], 'document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096']]);
        $path = $request->hasFile('document') ? $request->file('document')->store('verifications/'.$company->id, 'private') : null;
        CompanyVerification::create(['company_id' => $company->id, 'requested_by_id' => $request->user()->id, 'details' => $data['details'], 'document_path' => $path, 'status' => 'pending']);
        $company->update(['verification_status' => 'pending']);

        return back()->with('status', 'Verification request submitted for review.');
    }
}
