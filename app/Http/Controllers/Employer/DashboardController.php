<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Interview;
use App\Models\Job;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $companyIds = $user->activeCompanies()->pluck('companies.id');
        $jobs = Job::whereIn('company_id', $companyIds)->with(['company']);
        $activeJobs = (clone $jobs)->where('status', 'published')->count();
        $applications = Application::whereHas('job', fn ($q) => $q->whereIn('company_id', $companyIds));
        $byStage = (clone $applications)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $recent = (clone $applications)->with(['job', 'user'])->latest()->limit(8)->get();
        $interviews = Interview::whereHas('job', fn ($q) => $q->whereIn('company_id', $companyIds))->where('scheduled_at', '>=', now())->orderBy('scheduled_at')->limit(5)->get();
        $companies = $user->activeCompanies()->withCount('publishedJobs')->get();
        $conversion = $applications->count() ? round($applications->clone()->where('status', 'hired')->count() / max(1, $applications->count()) * 100, 1) : 0;

        return view('employer.dashboard', compact('user', 'companies', 'activeJobs', 'byStage', 'recent', 'interviews', 'conversion'));
    }
}
