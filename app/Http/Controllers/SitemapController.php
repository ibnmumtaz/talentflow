<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Job;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = [route('home'), route('jobs.index'), route('companies.index'), route('resources.index'), route('pages.pricing')];
        $jobs = Job::visible()->select(['id', 'updated_at'])->latest()->limit(200)->get();
        $companies = Company::active()->select(['id', 'updated_at'])->limit(100)->get();
        $content = view('public.sitemap', compact('urls', 'jobs', 'companies'))->render();

        return response($content, 200, ['Content-Type' => 'application/xml']);
    }

    public function robots()
    {
        return response("User-agent: *\nAllow: /\nDisallow: /candidate/\nDisallow: /employer/\nDisallow: /admin/\nSitemap: ".route('sitemap')."\n", 200, ['Content-Type' => 'text/plain']);
    }
}
