<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Job;
use App\Notifications\JobModeratedNotification;
use Illuminate\Http\Request;

class JobAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Job::with(['company', 'category'])->withCount('applications');
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $query->where('title', 'like', '%'.$request->q.'%');
        }
        $jobs = $query->latest()->paginate(15)->withQueryString();

        return view('admin.jobs.index', compact('jobs'));
    }

    public function moderate(Request $request, Job $job)
    {
        $data = $request->validate(['decision' => ['required', 'in:published,rejected'], 'rejection_reason' => ['nullable', 'string', 'max:1000']]);
        $job->update(['status' => $data['decision'], 'published_at' => $data['decision'] === 'published' ? now() : $job->published_at, 'rejection_reason' => $data['rejection_reason'] ?? null]);
        AuditLog::record($request->user(), 'job.moderated', $job, ['status' => $data['decision']]);
        $job->postedBy->notify(new JobModeratedNotification($job));

        return back()->with('status', "Job {$data['decision']}.");
    }

    public function toggleFeatured(Request $request, Job $job)
    {
        $job->update(['is_featured' => ! $job->is_featured]);

        return back()->with('status', $job->is_featured ? 'Job featured.' : 'Job unfeatured.');
    }

    public function destroy(Request $request, Job $job)
    {
        $job->delete();
        AuditLog::record($request->user(), 'job.deleted', $job, []);

        return back()->with('status', 'Job deleted.');
    }
}
