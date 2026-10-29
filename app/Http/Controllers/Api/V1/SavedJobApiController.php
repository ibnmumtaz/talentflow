<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobResource;
use App\Models\Job;
use Illuminate\Http\Request;

class SavedJobApiController extends Controller
{
    public function index(Request $request)
    {
        $jobs = $request->user()->savedJobs()->with('job.company')->latest()->paginate(15);

        return JobResource::collection($jobs->through(fn ($s) => $s->job));
    }

    public function toggle(Request $request, Job $job)
    {
        $existing = $request->user()->savedJobs()->where('job_id', $job->id)->first();
        if ($existing) {
            $existing->delete();
            $saved = false;
        } else {
            $request->user()->savedJobs()->create(['job_id' => $job->id]);
            $saved = true;
        }

        return response()->json(['saved' => $saved]);
    }
}
