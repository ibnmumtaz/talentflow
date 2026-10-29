<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobCategory;
use App\Models\Location;
use App\Models\Skill;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'location' => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'exists:job_categories,id'],
            'employment_type' => ['nullable', 'in:full_time,part_time,contract,internship,freelance,temporary'],
            'workplace_type' => ['nullable', 'in:remote,hybrid,onsite'],
            'experience_level' => ['nullable', 'in:entry,junior,mid,senior,lead,executive'],
            'skill_id' => ['nullable', 'exists:skills,id'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0'],
            'posted' => ['nullable', 'in:1,7,30'],
            'verified' => ['nullable', 'boolean'],
            'remote_only' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'in:newest,salary_desc,salary_asc,relevance'],
        ]);

        $query = Job::visible()->with(['company', 'category', 'location', 'skills']);

        if (! empty($validated['q'])) {
            $q = $validated['q'];
            $query->where(function ($qq) use ($q) {
                $qq->where('title', 'like', "%$q%")
                    ->orWhere('description', 'like', "%$q%")
                    ->orWhereHas('company', fn ($c) => $c->where('name', 'like', "%$q%"));
            });
        }
        if (! empty($validated['category_id'])) {
            $query->where('category_id', $validated['category_id']);
        }
        if (! empty($validated['location_id'])) {
            $query->where('location_id', $validated['location_id']);
        }
        if (! empty($validated['location'])) {
            $query->whereHas('location', fn ($l) => $l->where('name', 'like', '%'.$validated['location'].'%'));
        }
        if (! empty($validated['employment_type'])) {
            $query->where('employment_type', $validated['employment_type']);
        }
        if (! empty($validated['workplace_type'])) {
            $query->where('workplace_type', $validated['workplace_type']);
        }
        if (! empty($validated['remote_only'])) {
            $query->where('workplace_type', 'remote');
        }
        if (! empty($validated['experience_level'])) {
            $query->where('experience_level', $validated['experience_level']);
        }
        if (! empty($validated['skill_id'])) {
            $query->whereHas('skills', fn ($s) => $s->where('skills.id', $validated['skill_id']));
        }
        if (! empty($validated['salary_min'])) {
            $query->where('salary_min', '>=', $validated['salary_min']);
        }
        if (! empty($validated['salary_max'])) {
            $query->where('salary_max', '<=', $validated['salary_max']);
        }
        if (! empty($validated['posted'])) {
            $query->where('published_at', '>=', now()->subDays((int) $validated['posted']));
        }
        if (! empty($validated['verified'])) {
            $query->whereHas('company', fn ($c) => $c->where('verification_status', 'verified'));
        }

        match ($validated['sort'] ?? 'newest') {
            'salary_desc' => $query->orderByDesc('salary_max'),
            'salary_asc' => $query->orderBy('salary_min'),
            default => $query->latest('published_at'),
        };

        $jobs = $query->paginate(12)->withQueryString();
        $categories = JobCategory::where('is_active', true)->orderBy('name')->get();
        $locations = Location::where('is_active', true)->orderBy('name')->get();
        $skills = Skill::where('is_active', true)->orderBy('name')->limit(40)->get();

        return view('public.jobs.index', compact('jobs', 'categories', 'locations', 'skills', 'validated'));
    }

    public function show(Job $job)
    {
        if ($job->status !== 'published' || $job->isExpired()) {
            // Owners/admins may preview; everyone else gets 404 to avoid leaking drafts.
            if (! auth()->check() || (! auth()->user()->can('view', $job))) {
                abort(404);
            }
        }
        $job->load(['company', 'category', 'location', 'skills', 'screeningQuestions']);
        if ($job->status === 'published') {
            $job->increment('views_count');
        }
        $related = Job::visible()->where('id', '!=', $job->id)->where(function ($q) use ($job) {
            $q->where('category_id', $job->category_id)->orWhere('company_id', $job->company_id);
        })->limit(4)->get();
        $structuredData = json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'JobPosting',
            'title' => $job->title,
            'description' => strip_tags($job->description),
            'datePosted' => $job->published_at?->toDateString(),
            'employmentType' => strtoupper($job->employment_type),
            'hiringOrganization' => ['@type' => 'Organization', 'name' => $job->company->name, 'sameAs' => $job->company->website],
            'jobLocation' => ['@type' => 'Place', 'address' => ['@type' => 'PostalAddress', 'addressLocality' => $job->location->name ?? 'Remote', 'addressCountry' => 'PK']],
        ]);

        return view('public.jobs.show', compact('job', 'related', 'structuredData'));
    }

    public function remote(Request $request)
    {
        $request->merge(['workplace_type' => 'remote']);

        return $this->index($request);
    }

    public function internships(Request $request)
    {
        $request->merge(['employment_type' => 'internship']);

        return $this->index($request);
    }

    public function entryLevel(Request $request)
    {
        $request->merge(['experience_level' => 'entry']);

        return $this->index($request);
    }

    public function featured(Request $request)
    {
        $jobs = Job::visible()->with(['company', 'category', 'location'])->where('is_featured', true)->latest('published_at')->paginate(12);

        return view('public.jobs.index', ['jobs' => $jobs, 'categories' => JobCategory::where('is_active', true)->orderBy('name')->get(), 'locations' => Location::where('is_active', true)->orderBy('name')->get(), 'skills' => Skill::orderBy('name')->limit(40)->get(), 'validated' => [], 'heading' => 'Featured jobs']);
    }
}
