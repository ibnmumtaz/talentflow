<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Interview;
use App\Models\Job;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $profile = $user->candidateProfile()->with(['educations', 'experiences', 'skills', 'certifications', 'languages'])->first()
            ?? $user->candidateProfile()->create(['open_to_work' => true]);
        $applications = $user->applications()->with('job.company')->latest()->limit(5)->get();
        $byStatus = $user->applications()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $interviews = Interview::whereHas('application', fn ($q) => $q->where('user_id', $user->id))->where('scheduled_at', '>=', now())->orderBy('scheduled_at')->limit(5)->get();
        $saved = $user->savedJobs()->with('job.company')->latest()->limit(4)->get();
        $recommended = Job::visible()->with(['company', 'location'])->latest('published_at')->limit(4)->get();

        return view('candidate.dashboard', compact('user', 'profile', 'applications', 'byStatus', 'interviews', 'saved', 'recommended'));
    }
}
