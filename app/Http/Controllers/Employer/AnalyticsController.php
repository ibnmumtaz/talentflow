<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Interview;
use App\Models\Job;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $companyIds = $request->user()->activeCompanies()->pluck('companies.id');
        $jobs = Job::whereIn('company_id', $companyIds)->withCount('applications')->orderByDesc('applications_count')->limit(10)->get();
        $apps = Application::whereHas('job', fn ($q) => $q->whereIn('company_id', $companyIds));
        $byStage = (clone $apps)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $byDay = (clone $apps)->where('created_at', '>=', now()->subDays(30))->selectRaw('DATE(created_at) as d, count(*) as total')->groupBy('d')->orderBy('d')->get();
        $totalViews = Job::whereIn('company_id', $companyIds)->sum('views_count');
        $totalApps = $apps->count();
        $hired = (clone $apps)->where('status', 'hired')->count();
        $interviews = Interview::whereHas('job', fn ($q) => $q->whereIn('company_id', $companyIds))->count();

        return view('employer.analytics.index', compact('jobs', 'byStage', 'byDay', 'totalViews', 'totalApps', 'hired', 'interviews'));
    }
}
