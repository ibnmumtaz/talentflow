<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Article;
use App\Models\Company;
use App\Models\Faq;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\Location;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $stats = Cache::remember('home_stats', 300, function () {
            return [
                'jobs' => Job::published()->count(),
                'companies' => Company::active()->verified()->count(),
                'candidates' => User::where('role', 'candidate')->count(),
                'hired' => Application::where('status', 'hired')->count(),
            ];
        });
        $featuredJobs = Job::visible()->with(['company', 'category', 'location'])->where('is_featured', true)->latest('published_at')->limit(6)->get();
        $latestJobs = Job::visible()->with(['company', 'category', 'location'])->latest('published_at')->limit(8)->get();
        $remoteJobs = Job::visible()->with(['company', 'location'])->where('workplace_type', 'remote')->latest('published_at')->limit(4)->get();
        $categories = JobCategory::withCount(['jobs' => fn ($q) => $q->published()])->where('is_active', true)->orderByDesc('jobs_count')->limit(8)->get();
        $locations = Location::withCount(['jobs' => fn ($q) => $q->published()])->where('is_active', true)->orderByDesc('jobs_count')->limit(8)->get();
        $featuredCompanies = Company::active()->verified()->where('is_featured', true)->withCount(['publishedJobs'])->limit(6)->get();
        $articles = Article::where('is_published', true)->latest('published_at')->limit(3)->get();
        $faqs = Faq::where('is_active', true)->orderBy('sort_order')->limit(5)->get();
        $testimonials = Testimonial::where('is_active', true)->orderBy('sort_order')->limit(3)->get();

        return view('public.home', compact('stats', 'featuredJobs', 'latestJobs', 'remoteJobs', 'categories', 'locations', 'featuredCompanies', 'articles', 'faqs', 'testimonials'));
    }
}
