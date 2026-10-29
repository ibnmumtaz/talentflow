<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'candidates' => User::where('role', 'candidate')->count(),
            'employers' => User::where('role', 'employer')->count(),
            'companies' => Company::count(),
            'jobs' => Job::count(),
            'published_jobs' => Job::where('status', 'published')->count(),
            'pending_jobs' => Job::where('status', 'pending')->count(),
            'pending_companies' => Company::where('verification_status', 'pending')->count(),
            'applications' => Application::count(),
            'hired' => Application::where('status', 'hired')->count(),
        ];
        $registrations = User::where('created_at', '>=', now()->subDays(30))->selectRaw('DATE(created_at) as d, count(*) as total')->groupBy('d')->orderBy('d')->get();
        $applicationsByDay = Application::where('created_at', '>=', now()->subDays(30))->selectRaw('DATE(created_at) as d, count(*) as total')->groupBy('d')->orderBy('d')->get();
        $byCategory = JobCategory::withCount(['jobs' => fn ($q) => $q->published()])->orderByDesc('jobs_count')->limit(6)->get();
        $recentUsers = User::latest()->limit(6)->get();
        $recentJobs = Job::with('company')->latest()->limit(6)->get();
        $recentActivity = AuditLog::with('user')->latest()->limit(8)->get();

        return view('admin.dashboard', compact('stats', 'registrations', 'applicationsByDay', 'byCategory', 'recentUsers', 'recentJobs', 'recentActivity'));
    }
}
