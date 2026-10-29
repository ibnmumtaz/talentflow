<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApplicationRequest;
use App\Models\Application;
use App\Models\ApplicationStatusHistory;
use App\Models\CandidateResume;
use App\Models\Job;
use App\Notifications\NewApplicationReceived;
use App\Services\ApplicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->applications()->with(['job.company', 'job.location']);
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $applications = $query->latest()->paginate(12)->withQueryString();

        return view('candidate.applications.index', compact('applications'));
    }

    public function show(Request $request, Application $application)
    {
        abort_unless($application->user_id === $request->user()->id, 403);
        $application->load(['job.company', 'job.location', 'resume', 'histories.changedBy', 'interviews', 'answers.question']);

        return view('candidate.applications.show', compact('application'));
    }

    public function store(ApplicationRequest $request, Job $job)
    {
        if ($job->status !== 'published' || $job->isExpired()) {
            abort(404);
        }
        if ($job->applications()->where('user_id', $request->user()->id)->exists()) {
            return back()->withErrors(['application' => 'You have already applied for this job.']);
        }
        $application = DB::transaction(function () use ($request, $job) {
            $resume = null;
            if ($request->filled('resume_id')) {
                $resume = CandidateResume::where('id', $request->resume_id)->where('user_id', $request->user()->id)->firstOrFail();
            } elseif ($request->hasFile('resume_file')) {
                $file = $request->file('resume_file');
                $stored = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
                $path = $file->storeAs('resumes/'.$request->user()->id, $stored, 'private');
                $resume = $request->user()->resumes()->create(['title' => 'CV for '.$job->title, 'original_filename' => $file->getClientOriginalName(), 'stored_filename' => $stored, 'disk' => 'private', 'path' => $path, 'mime_type' => $file->getMimeType(), 'size_bytes' => $file->getSize(), 'is_primary' => ! $request->user()->resumes()->exists()]);
            } else {
                $resume = $request->user()->primaryResume();
            }
            // Validate required screening questions
            foreach ($job->screeningQuestions()->where('is_required', true)->get() as $q) {
                if (empty($request->input("answers.{$q->id}"))) {
                    throw ValidationException::withMessages(["answers.{$q->id}" => 'This screening question is required.']);
                }
            }
            $app = Application::create(['job_id' => $job->id, 'user_id' => $request->user()->id, 'resume_id' => $resume?->id, 'cover_letter' => $request->cover_letter, 'status' => 'submitted']);
            foreach ($request->input('answers', []) as $qid => $answer) {
                $question = $job->screeningQuestions()->find($qid);
                if ($question && $answer !== null && $answer !== '') {
                    $app->answers()->create(['question_id' => $qid, 'answer' => $answer]);
                }
            }
            ApplicationStatusHistory::create(['application_id' => $app->id, 'from_status' => null, 'to_status' => 'submitted', 'changed_by_id' => $request->user()->id]);
            $job->increment('applications_count_cached');

            return $app;
        });

        // Notify company owners / recruiters who can view candidates
        foreach ($job->company->memberships()->where('status', 'active')->where('can_view_candidates', true)->with('user')->get() as $m) {
            if ($m->user->notifyPreference('new_application', true)) {
                $m->user->notify(new NewApplicationReceived($application));
            }
        }

        return redirect()->route('candidate.applications.show', $application)->with('status', 'Application submitted. The employer has been notified.');
    }

    public function withdraw(Request $request, Application $application)
    {
        $request->user()->can('withdraw', $application) || abort(403);
        ApplicationService::transition($application, 'withdrawn', $request->user(), 'Withdrawn by candidate.');
        $application->update(['withdrawn_at' => now()]);

        return back()->with('status', 'Application withdrawn.');
    }
}
