<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobResource;
use App\Models\Job;
use Illuminate\Http\Request;

class JobApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Job::visible()->with(['company', 'category', 'location', 'skills']);
        if ($request->filled('q')) {
            $query->where('title', 'like', '%'.$request->q.'%');
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('workplace_type')) {
            $query->where('workplace_type', $request->workplace_type);
        }
        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }

        return JobResource::collection($query->latest('published_at')->paginate(15));
    }

    public function show(Job $job)
    {
        if ($job->status !== 'published') {
            abort(404);
        }

        return new JobResource($job->load(['company', 'category', 'location', 'skills']));
    }
}
