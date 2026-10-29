<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobRequest;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\Location;
use App\Models\PlatformSetting;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $companyIds = $request->user()->activeCompanies()->pluck('companies.id');
        $query = Job::whereIn('company_id', $companyIds)->with(['company', 'category', 'location'])->withCount('applications');
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $query->where('title', 'like', '%'.$request->q.'%');
        }
        $jobs = $query->latest()->paginate(12)->withQueryString();

        return view('employer.jobs.index', compact('jobs'));
    }

    public function create(Request $request)
    {
        $companies = $request->user()->activeCompanies()->get();
        if ($companies->isEmpty()) {
            return redirect()->route('onboarding.show');
        }

        return view('employer.jobs.form', ['job' => new Job, 'companies' => $companies, 'categories' => JobCategory::orderBy('name')->get(), 'locations' => Location::orderBy('name')->get(), 'skills' => Skill::orderBy('name')->get()]);
    }

    public function store(JobRequest $request)
    {
        $company = Company::findOrFail($request->input('company_id'));
        $this->authorize('manageJobs', $company);
        $job = $this->saveJob($request, new Job(['company_id' => $company->id, 'posted_by_id' => $request->user()->id]));
        AuditLog::record($request->user(), 'job.created', $job, ['title' => $job->title, 'status' => $job->status]);

        return redirect()->route('employer.jobs.show', $job)->with('status', 'Job saved as draft. Preview it, then publish when ready.');
    }

    public function show(Job $job)
    {
        $this->authorize('view', $job);
        $job->load(['company', 'category', 'location', 'skills', 'screeningQuestions']);

        return view('employer.jobs.show', compact('job'));
    }

    public function edit(Request $request, Job $job)
    {
        $this->authorize('update', $job);

        return view('employer.jobs.form', ['job' => $job->load('skills', 'screeningQuestions'), 'companies' => $request->user()->activeCompanies()->get(), 'categories' => JobCategory::orderBy('name')->get(), 'locations' => Location::orderBy('name')->get(), 'skills' => Skill::orderBy('name')->get()]);
    }

    public function update(JobRequest $request, Job $job)
    {
        $this->authorize('update', $job);
        $this->saveJob($request, $job);

        return redirect()->route('employer.jobs.show', $job)->with('status', 'Job updated.');
    }

    public function destroy(Request $request, Job $job)
    {
        $this->authorize('update', $job);
        $job->delete();

        return redirect()->route('employer.jobs.index')->with('status', 'Job archived (soft-deleted).');
    }

    public function publish(Request $request, Job $job)
    {
        $this->authorize('publish', $job);
        $requiresModeration = PlatformSetting::get('jobs_require_moderation', '1') === '1';
        $job->update(['status' => $requiresModeration ? 'pending' : 'published', 'published_at' => $requiresModeration ? null : now()]);
        AuditLog::record($request->user(), 'job.submitted_for_moderation', $job, ['status' => $job->status]);

        return back()->with('status', $requiresModeration ? 'Job submitted for moderation.' : 'Job published.');
    }

    public function close(Request $request, Job $job)
    {
        $this->authorize('update', $job);
        $job->update(['status' => 'closed']);

        return back()->with('status', 'Job closed. It is no longer publicly visible.');
    }

    public function archive(Request $request, Job $job)
    {
        $this->authorize('update', $job);
        $job->update(['status' => 'archived']);

        return back()->with('status', 'Job archived.');
    }

    protected function saveJob(JobRequest $request, Job $job): Job
    {
        $data = $request->validated();
        $skills = $data['skills'] ?? [];
        $questions = $data['screening_questions'] ?? [];
        $benefits = $data['benefits'] ?? null;
        unset($data['skills'], $data['screening_questions'], $data['benefits']);
        $data['slug'] = Str::slug($data['title']).'-'.Str::random(5);
        // Keep slug stable on update unless title changed materially
        if ($job->exists && $job->title === $data['title']) {
            $data['slug'] = $job->slug;
        }
        $data['benefits'] = $benefits ? array_values(array_filter(array_map('trim', explode(',', $benefits)))) : null;
        $data['salary_visible'] = $request->boolean('salary_visible');
        $data['is_remote'] = ($data['workplace_type'] ?? null) === 'remote';
        $data['status'] = $job->exists ? $job->status : 'draft';
        $job->fill($data);
        $job->save();
        $job->skills()->sync($skills);
        $job->screeningQuestions()->delete();
        foreach ($questions as $i => $q) {
            if (! empty($q['question'])) {
                $job->screeningQuestions()->create(['question' => $q['question'], 'type' => $q['type'] ?? 'text', 'is_required' => ! empty($q['is_required']), 'sort_order' => $i]);
            }
        }

        return $job;
    }
}
