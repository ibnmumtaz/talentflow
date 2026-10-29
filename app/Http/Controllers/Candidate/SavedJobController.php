<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;

class SavedJobController extends Controller
{
    public function index(Request $request)
    {
        $saved = $request->user()->savedJobs()->with(['job.company', 'job.location', 'job.category'])->latest()->paginate(12);

        return view('candidate.saved.index', compact('saved'));
    }

    public function toggle(Request $request, Job $job)
    {
        $existing = $request->user()->savedJobs()->where('job_id', $job->id)->first();
        if ($existing) {
            $existing->delete();
            $msg = 'Job removed from saved jobs.';
        } else {
            $request->user()->savedJobs()->create(['job_id' => $job->id]);
            $msg = 'Job saved.';
        }

        return back()->with('status', $msg);
    }
}
