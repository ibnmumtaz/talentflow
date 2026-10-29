<?php

use App\Http\Controllers\Admin\AdminContentController;
use App\Http\Controllers\Admin\CompanyAdminController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\JobAdminController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Candidate\ApplicationController as CandidateApplicationController;
use App\Http\Controllers\Candidate\DashboardController as CandidateDashboardController;
use App\Http\Controllers\Candidate\InterviewController as CandidateInterviewController;
use App\Http\Controllers\Candidate\JobAlertController;
use App\Http\Controllers\Candidate\ProfileController;
use App\Http\Controllers\Candidate\ResumeController;
use App\Http\Controllers\Candidate\SavedJobController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\Employer\AnalyticsController;
use App\Http\Controllers\Employer\ApplicantController;
use App\Http\Controllers\Employer\CompanyController as EmployerCompanyController;
use App\Http\Controllers\Employer\DashboardController as EmployerDashboardController;
use App\Http\Controllers\Employer\InterviewController as EmployerInterviewController;
use App\Http\Controllers\Employer\JobController as EmployerJobController;
use App\Http\Controllers\Employer\TalentPoolController;
use App\Http\Controllers\Employer\TeamController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ResumeDownloadController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/remote', [JobController::class, 'remote'])->name('jobs.remote');
Route::get('/jobs/internships', [JobController::class, 'internships'])->name('jobs.internships');
Route::get('/jobs/entry-level', [JobController::class, 'entryLevel'])->name('jobs.entry-level');
Route::get('/jobs/featured', [JobController::class, 'featured'])->name('jobs.featured');
Route::get('/jobs/{job}', [JobController::class, 'show'])->name('jobs.show');

Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
Route::get('/companies/{company}', [CompanyController::class, 'show'])->name('companies.show');
Route::get('/companies/{company}/careers', [CompanyController::class, 'careers'])->name('companies.careers');
Route::get('/industries', [CompanyController::class, 'browseIndustries'])->name('browse.industries');
Route::get('/categories', [CompanyController::class, 'browseCategories'])->name('browse.categories');
Route::get('/locations', [CompanyController::class, 'browseLocations'])->name('browse.locations');

Route::get('/resources', [ResourceController::class, 'articles'])->name('resources.index');
Route::get('/resources/{article}', [ResourceController::class, 'article'])->name('resources.show');
Route::get('/faq', [ResourceController::class, 'faq'])->name('pages.faq');

Route::get('/about', [PageController::class, 'about'])->name('pages.about');
Route::get('/contact', [PageController::class, 'contact'])->name('pages.contact');
Route::post('/contact', [PageController::class, 'contactStore'])->name('pages.contact.store')->middleware('throttle:10,1');
Route::get('/employers', [PageController::class, 'employers'])->name('pages.employers');
Route::get('/pricing', [PageController::class, 'pricing'])->name('pages.pricing');
Route::get('/terms', [PageController::class, 'terms'])->name('pages.terms');
Route::get('/privacy', [PageController::class, 'privacy'])->name('pages.privacy');
Route::post('/newsletter', [PageController::class, 'newsletter'])->name('newsletter.store')->middleware('throttle:10,1');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

// Auth (guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::get('/forgot-password', [PasswordController::class, 'showForgot'])->name('password.request');
    Route::post('/forgot-password', [PasswordController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:5,1');
    Route::get('/reset-password/{token}', [PasswordController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password', [PasswordController::class, 'reset'])->name('password.update')->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/email/verify', [VerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->middleware('signed')->name('verification.verify');
    Route::post('/email/verification-notification', [VerificationController::class, 'resend'])->middleware('throttle:3,1')->name('verification.send');

    Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding.show');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');

    Route::get('/resumes/{resume}/download', [ResumeDownloadController::class, 'download'])->name('resumes.download');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::get('/notifications/{id}', [NotificationController::class, 'read'])->name('notifications.read');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::put('/settings/password', [SettingsController::class, 'password'])->name('settings.password');
    Route::get('/settings/export', [SettingsController::class, 'export'])->name('settings.export');
    Route::post('/settings/delete-request', [SettingsController::class, 'deleteRequest'])->name('settings.delete-request');

    Route::get('/invitations/{token}', [TeamController::class, 'accept'])->name('invitations.accept');
});

// Candidate
Route::middleware(['auth', 'role:candidate'])->prefix('candidate')->name('candidate.')->group(function () {
    Route::get('/dashboard', [CandidateDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/education', [ProfileController::class, 'storeEducation'])->name('profile.education.store');
    Route::delete('/profile/education/{education}', [ProfileController::class, 'destroyEducation'])->name('profile.education.destroy');
    Route::post('/profile/experience', [ProfileController::class, 'storeExperience'])->name('profile.experience.store');
    Route::delete('/profile/experience/{experience}', [ProfileController::class, 'destroyExperience'])->name('profile.experience.destroy');
    Route::post('/profile/skills', [ProfileController::class, 'storeSkill'])->name('profile.skills.store');
    Route::delete('/profile/skills/{skillId}', [ProfileController::class, 'destroySkill'])->name('profile.skills.destroy');
    Route::post('/profile/certifications', [ProfileController::class, 'storeCertification'])->name('profile.certifications.store');
    Route::delete('/profile/certifications/{certification}', [ProfileController::class, 'destroyCertification'])->name('profile.certifications.destroy');
    Route::post('/profile/languages', [ProfileController::class, 'storeLanguage'])->name('profile.languages.store');
    Route::delete('/profile/languages/{language}', [ProfileController::class, 'destroyLanguage'])->name('profile.languages.destroy');

    Route::get('/resumes', [ResumeController::class, 'index'])->name('resumes.index');
    Route::post('/resumes', [ResumeController::class, 'store'])->name('resumes.store');
    Route::post('/resumes/{resume}/primary', [ResumeController::class, 'setPrimary'])->name('resumes.primary');
    Route::get('/resumes/{resume}/download', [ResumeController::class, 'download'])->name('resumes.download');
    Route::delete('/resumes/{resume}', [ResumeController::class, 'destroy'])->name('resumes.destroy');

    Route::get('/applications', [CandidateApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [CandidateApplicationController::class, 'show'])->name('applications.show');
    Route::post('/applications/{application}/withdraw', [CandidateApplicationController::class, 'withdraw'])->name('applications.withdraw');

    Route::get('/saved-jobs', [SavedJobController::class, 'index'])->name('saved.index');
    Route::post('/saved-jobs/{job}/toggle', [SavedJobController::class, 'toggle'])->name('saved.toggle');

    Route::get('/job-alerts', [JobAlertController::class, 'index'])->name('alerts.index');
    Route::post('/job-alerts', [JobAlertController::class, 'store'])->name('alerts.store');
    Route::put('/job-alerts/{alert}', [JobAlertController::class, 'update'])->name('alerts.update');
    Route::delete('/job-alerts/{alert}', [JobAlertController::class, 'destroy'])->name('alerts.destroy');

    Route::get('/interviews', [CandidateInterviewController::class, 'index'])->name('interviews.index');
    Route::post('/interviews/{interview}/respond', [CandidateInterviewController::class, 'respond'])->name('interviews.respond');
});

// Apply for a job (candidate only, route outside prefix for clarity)
Route::post('/jobs/{job}/apply', [CandidateApplicationController::class, 'store'])->middleware(['auth', 'role:candidate'])->name('jobs.apply');

// Employer / company
Route::middleware(['auth', 'role:employer,admin'])->prefix('employer')->name('employer.')->group(function () {
    Route::get('/dashboard', [EmployerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/company', [EmployerCompanyController::class, 'edit'])->name('company.edit');
    Route::put('/company/{company}', [EmployerCompanyController::class, 'update'])->name('company.update');
    Route::post('/company/{company}/verification', [EmployerCompanyController::class, 'requestVerification'])->name('company.verification');

    Route::get('/jobs', [EmployerJobController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/create', [EmployerJobController::class, 'create'])->name('jobs.create');
    Route::post('/jobs', [EmployerJobController::class, 'store'])->name('jobs.store');
    Route::get('/jobs/{job}', [EmployerJobController::class, 'show'])->name('jobs.show');
    Route::get('/jobs/{job}/edit', [EmployerJobController::class, 'edit'])->name('jobs.edit');
    Route::put('/jobs/{job}', [EmployerJobController::class, 'update'])->name('jobs.update');
    Route::delete('/jobs/{job}', [EmployerJobController::class, 'destroy'])->name('jobs.destroy');
    Route::post('/jobs/{job}/publish', [EmployerJobController::class, 'publish'])->name('jobs.publish');
    Route::post('/jobs/{job}/close', [EmployerJobController::class, 'close'])->name('jobs.close');
    Route::post('/jobs/{job}/archive', [EmployerJobController::class, 'archive'])->name('jobs.archive');

    Route::get('/applicants', [ApplicantController::class, 'index'])->name('applicants.index');
    Route::get('/applicants/{application}', [ApplicantController::class, 'show'])->name('applicants.show');
    Route::post('/applicants/{application}/transition', [ApplicantController::class, 'transition'])->name('applicants.transition');
    Route::post('/applicants/bulk', [ApplicantController::class, 'bulk'])->name('applicants.bulk');
    Route::post('/applicants/{application}/notes', [ApplicantController::class, 'note'])->name('applicants.note');

    Route::get('/interviews', [EmployerInterviewController::class, 'index'])->name('interviews.index');
    Route::post('/applications/{application}/interviews', [EmployerInterviewController::class, 'store'])->name('interviews.store');
    Route::put('/interviews/{interview}', [EmployerInterviewController::class, 'update'])->name('interviews.update');
    Route::post('/interviews/{interview}/feedback', [EmployerInterviewController::class, 'feedback'])->name('interviews.feedback');

    Route::get('/talent-pool', [TalentPoolController::class, 'index'])->name('talent-pool.index');

    Route::get('/team', [TeamController::class, 'index'])->name('team.index');
    Route::post('/team/{company}/invite', [TeamController::class, 'invite'])->name('team.invite');
    Route::put('/team/members/{membership}', [TeamController::class, 'updateMember'])->name('team.members.update');
    Route::delete('/team/members/{membership}', [TeamController::class, 'removeMember'])->name('team.members.destroy');

    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
});

// Company-owner dashboard aliases (same employer workspace, distinct route)
Route::middleware(['auth', 'role:employer,admin'])->prefix('company')->name('company.')->group(function () {
    Route::get('/dashboard', [EmployerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/team', [TeamController::class, 'index'])->name('team');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
});

// Admin
Route::middleware(['auth', 'role:admin,moderator'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::put('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.role');
        Route::post('/users/{user}/suspend', [AdminUserController::class, 'suspend'])->name('users.suspend');
        Route::post('/users/{user}/reactivate', [AdminUserController::class, 'reactivate'])->name('users.reactivate');
        Route::get('/settings', [AdminContentController::class, 'settings'])->name('settings.index');
        Route::put('/settings', [AdminContentController::class, 'updateSettings'])->name('settings.update');
        Route::get('/exports/users', [AdminContentController::class, 'exportUsers'])->name('exports.users');
        Route::get('/exports/jobs', [AdminContentController::class, 'exportJobs'])->name('exports.jobs');
    });
    Route::get('/companies', [CompanyAdminController::class, 'index'])->name('companies.index');
    Route::get('/companies/{company}', [CompanyAdminController::class, 'show'])->name('companies.show');
    Route::post('/companies/{company}/verify', [CompanyAdminController::class, 'verify'])->name('companies.verify');
    Route::post('/companies/{company}/toggle-active', [CompanyAdminController::class, 'toggleActive'])->name('companies.toggle-active');
    Route::get('/jobs', [JobAdminController::class, 'index'])->name('jobs.index');
    Route::post('/jobs/{job}/moderate', [JobAdminController::class, 'moderate'])->name('jobs.moderate');
    Route::post('/jobs/{job}/featured', [JobAdminController::class, 'toggleFeatured'])->name('jobs.featured');
    Route::delete('/jobs/{job}', [JobAdminController::class, 'destroy'])->name('jobs.destroy');
    Route::get('/lookups', [AdminContentController::class, 'lookups'])->name('lookups.index');
    Route::post('/lookups/{type}', [AdminContentController::class, 'storeLookup'])->name('lookups.store');
    Route::delete('/lookups/{type}/{id}', [AdminContentController::class, 'destroyLookup'])->name('lookups.destroy');
    Route::get('/content', [AdminContentController::class, 'content'])->name('content.index');
    Route::post('/content/articles', [AdminContentController::class, 'storeArticle'])->name('content.articles.store');
    Route::delete('/content/articles/{article}', [AdminContentController::class, 'destroyArticle'])->name('content.articles.destroy');
    Route::post('/content/faqs', [AdminContentController::class, 'storeFaq'])->name('content.faqs.store');
    Route::delete('/content/faqs/{faq}', [AdminContentController::class, 'destroyFaq'])->name('content.faqs.destroy');
    Route::post('/content/testimonials', [AdminContentController::class, 'storeTestimonial'])->name('content.testimonials.store');
    Route::delete('/content/testimonials/{testimonial}', [AdminContentController::class, 'destroyTestimonial'])->name('content.testimonials.destroy');
    Route::get('/reports', [AdminContentController::class, 'reports'])->name('reports.index');
    Route::post('/reports/{report}/resolve', [AdminContentController::class, 'resolveReport'])->name('reports.resolve');
    Route::get('/audit-logs', [AdminContentController::class, 'audit'])->name('audit.index');
});
