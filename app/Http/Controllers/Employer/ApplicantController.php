<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use App\Services\ApplicationService;
use Illuminate\Http\Request;

class ApplicantController extends Controller
{
    public function index(Request $request)
    {
        $companyIds = $request->user()->activeCompanies()->pluck('companies.id');
        $query = Application::whereHas('job', fn ($q) => $q->whereIn('company_id', $companyIds))->with(['job.company', 'user.candidateProfile', 'resume']);
        if ($request->filled('job_id')) {
            $query->where('job_id', $request->job_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $query->whereHas('user', fn ($u) => $u->where('name', 'like', '%'.$request->q.'%')->orWhere('email', 'like', '%'.$request->q.'%'));
        }
        $applications = $query->latest()->paginate(15)->withQueryString();
        $stageCounts = Application::whereHas('job', fn ($q) => $q->whereIn('company_id', $companyIds))->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $jobs = Job::whereIn('company_id', $companyIds)->orderBy('title')->get();
        $view = $request->query('view', 'pipeline');

        return view('employer.applicants.index', compact('applications', 'stageCounts', 'jobs', 'view'));
    }

    public function show(Request $request, Application $application)
    {
        $this->authorize('view', $application);
        $application->load(['job.company', 'user.candidateProfile.educations', 'user.candidateProfile.experiences', 'user.candidateProfile.skills', 'resume', 'answers.question', 'histories.changedBy', 'notes.author', 'interviews']);

        return view('employer.applicants.show', compact('application'));
    }

    public function transition(Request $request, Application $application)
    {
        $this->authorize('update', $application);
        $data = $request->validate(['status' => ['required', 'in:'.implode(',', Application::STAGES)], 'note' => ['nullable', 'string', 'max:1000']]);
        try {
            ApplicationService::transition($application, $data['status'], $request->user(), $data['note'] ?? null);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        return back()->with('status', 'Application moved to '.ucwords(str_replace('_', ' ', $data['status'])).'.');
    }

    public function bulk(Request $request)
    {
        $data = $request->validate(['ids' => ['required', 'array'], 'ids.*' => ['exists:applications,id'], 'status' => ['required', 'in:'.implode(',', Application::STAGES)]]);
        $moved = 0;
        $skipped = 0;
        foreach (Application::whereIn('id', $data['ids'])->get() as $app) {
            if (! $request->user()->can('update', $app)) {
                $skipped++;

                continue;
            }
            if (! Application::canTransition($app->status, $data['status'])) {
                $skipped++;

                continue;
            }
            ApplicationService::transition($app, $data['status'], $request->user());
            $moved++;
        }

        return back()->with('status', "$moved application(s) updated, $skipped skipped (unauthorised or invalid transition).");
    }

    public function note(Request $request, Application $application)
    {
        $this->authorize('update', $application);
        $data = $request->validate(['note' => ['required', 'string', 'max:2000'], 'evaluation_score' => ['nullable', 'numeric', 'min:0', 'max:10']]);
        $application->notes()->create(['author_id' => $request->user()->id, 'note' => $data['note']]);
        if (isset($data['evaluation_score'])) {
            $application->update(['evaluation_score' => $data['evaluation_score']]);
        }

        return back()->with('status', 'Note saved. Notes are private to your hiring team.');
    }
}
