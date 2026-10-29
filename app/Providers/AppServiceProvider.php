<?php

namespace App\Providers;

use App\Models\Application;
use App\Models\CandidateResume;
use App\Models\Company;
use App\Models\Interview;
use App\Models\Job;
use App\Models\PlatformSetting;
use App\Policies\ApplicationPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\InterviewPolicy;
use App\Policies\JobPolicy;
use App\Policies\ResumePolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Gate::policy(Company::class, CompanyPolicy::class);
        Gate::policy(Job::class, JobPolicy::class);
        Gate::policy(Application::class, ApplicationPolicy::class);
        Gate::policy(CandidateResume::class, ResumePolicy::class);
        Gate::policy(Interview::class, InterviewPolicy::class);

        Gate::define('admin', fn ($user) => $user->isAdmin());
        Gate::define('moderate', fn ($user) => $user->isModerator());

        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(5)->by($request->email.$request->ip()));
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip()));

        // Share brand / platform settings with every view, cached and fault-tolerant.
        try {
            if (Schema::hasTable('platform_settings')) {
                $settings = PlatformSetting::all()->pluck('value', 'key');
                View::share('brandName', $settings['site_name'] ?? 'TalentFlow');
                View::share('platformSettings', $settings);
            }
        } catch (\Throwable) {
            View::share('brandName', 'TalentFlow');
            View::share('platformSettings', collect());
        }
    }
}
