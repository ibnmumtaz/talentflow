<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;

class TalentPoolController extends Controller
{
    public function index(Request $request)
    {
        $companyIds = $request->user()->activeCompanies()->pluck('companies.id');
        $query = Application::whereHas('job', fn ($q) => $q->whereIn('company_id', $companyIds))
            ->whereIn('status', ['shortlisted', 'interview_completed', 'offer_extended'])
            ->with(['job', 'user.candidateProfile.skills']);
        if ($request->filled('q')) {
            $query->whereHas('user', fn ($u) => $u->where('name', 'like', '%'.$request->q.'%'));
        }
        $pool = $query->latest()->paginate(15)->withQueryString();

        return view('employer.talent-pool.index', compact('pool'));
    }
}
