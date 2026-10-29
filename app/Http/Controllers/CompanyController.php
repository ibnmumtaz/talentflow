<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Industry;
use App\Models\JobCategory;
use App\Models\Location;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $query = Company::active()->with(['industry', 'location'])->withCount('publishedJobs');
        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->q.'%');
        }
        if ($request->filled('industry_id')) {
            $query->where('industry_id', $request->industry_id);
        }
        if ($request->filled('verified')) {
            $query->where('verification_status', 'verified');
        }
        $companies = $query->orderByDesc('is_featured')->orderBy('name')->paginate(12)->withQueryString();

        return view('public.companies.index', ['companies' => $companies, 'industries' => Industry::orderBy('name')->get()]);
    }

    public function show(Company $company)
    {
        if (! $company->is_active) {
            abort(404);
        }
        $company->load(['industry', 'location']);
        $jobs = $company->publishedJobs()->with(['category', 'location'])->latest('published_at')->paginate(8);
        $structuredData = json_encode(['@context' => 'https://schema.org', '@type' => 'Organization', 'name' => $company->name, 'description' => $company->description, 'url' => $company->website]);

        return view('public.companies.show', compact('company', 'jobs', 'structuredData'));
    }

    public function careers(Company $company)
    {
        if (! $company->is_active) {
            abort(404);
        }
        $jobs = $company->publishedJobs()->with(['category', 'location'])->latest('published_at')->paginate(12);

        return view('public.companies.careers', compact('company', 'jobs'));
    }

    public function browseIndustries()
    {
        $industries = Industry::withCount(['companies' => fn ($q) => $q->where('is_active', true)])->orderBy('name')->get();

        return view('public.browse', ['title' => 'Browse industries', 'items' => $industries, 'type' => 'industry']);
    }

    public function browseCategories()
    {
        $categories = JobCategory::withCount(['jobs' => fn ($q) => $q->published()])->orderBy('name')->get();

        return view('public.browse', ['title' => 'Browse job categories', 'items' => $categories, 'type' => 'category']);
    }

    public function browseLocations()
    {
        $locations = Location::withCount(['jobs' => fn ($q) => $q->published()])->orderBy('name')->get();

        return view('public.browse', ['title' => 'Browse locations', 'items' => $locations, 'type' => 'location']);
    }
}
