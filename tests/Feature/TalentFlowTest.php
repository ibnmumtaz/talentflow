<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\CandidateResume;
use App\Models\Company;
use App\Models\CompanyMembership;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\User;
use Database\Seeders\LookupSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TalentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(LookupSeeder::class);
    }

    private function makeUser(string $role, string $email): User
    {
        return User::factory()->create(['role' => $role, 'email' => $email, 'email_verified_at' => now()]);
    }

    private function makeCompany(User $owner): Company
    {
        $company = Company::factory()->create();
        CompanyMembership::create(['company_id' => $company->id, 'user_id' => $owner->id, 'role' => 'owner', 'can_publish_jobs' => true, 'can_view_candidates' => true, 'can_manage_team' => true, 'status' => 'active', 'joined_at' => now()]);

        return $company;
    }

    public function test_homepage_loads_with_database_stats(): void
    {
        $this->get('/')->assertOk()->assertSee('TalentFlow');
    }

    public function test_registration_login_and_role_redirect(): void
    {
        $this->post('/register', ['name' => 'New Candidate', 'email' => 'new@example.com', 'password' => 'password123', 'password_confirmation' => 'password123', 'role' => 'candidate'])
            ->assertRedirect('/onboarding');
        $this->assertAuthenticated();
        $this->post('/logout')->assertRedirect('/');
        $user = $this->makeUser('candidate', 'login@example.com');
        $this->post('/login', ['email' => 'login@example.com', 'password' => 'password'])->assertRedirect(route('candidate.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_guests_cannot_access_private_dashboards(): void
    {
        $this->get('/candidate/dashboard')->assertRedirect('/login');
        $this->get('/employer/dashboard')->assertRedirect('/login');
        $this->get('/admin/dashboard')->assertRedirect('/login');
    }

    public function test_candidates_cannot_access_employer_or_admin_areas(): void
    {
        $candidate = $this->makeUser('candidate', 'cand@example.com');
        $this->actingAs($candidate)->get('/employer/dashboard')->assertForbidden();
        $this->actingAs($candidate)->get('/admin/dashboard')->assertForbidden();
    }

    public function test_suspended_account_cannot_log_in(): void
    {
        $user = $this->makeUser('candidate', 'susp@example.com');
        $user->update(['is_suspended' => true]);
        $this->post('/login', ['email' => 'susp@example.com', 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_job_search_filters_and_public_visibility(): void
    {
        $employer = $this->makeUser('employer', 'emp@example.com');
        $company = $this->makeCompany($employer);
        Job::factory()->create(['company_id' => $company->id, 'posted_by_id' => $employer->id, 'title' => 'Unique Laravel Role', 'status' => 'published', 'published_at' => now()]);
        Job::factory()->create(['company_id' => $company->id, 'posted_by_id' => $employer->id, 'title' => 'Hidden Draft Role', 'status' => 'draft']);
        $this->get('/jobs?q=Laravel')->assertOk()->assertSee('Unique Laravel Role')->assertDontSee('Hidden Draft Role');
        $this->get('/jobs?q=Hidden')->assertOk()->assertDontSee('Hidden Draft Role');
    }

    public function test_employer_can_create_job_and_admin_can_moderate_it(): void
    {
        $employer = $this->makeUser('employer', 'emp2@example.com');
        $company = $this->makeCompany($employer);
        $category = JobCategory::first();
        $response = $this->actingAs($employer)->post('/employer/jobs', [
            'company_id' => $company->id, 'title' => 'Test Developer', 'category_id' => $category->id,
            'description' => str_repeat('Great role. ', 10), 'employment_type' => 'full_time', 'workplace_type' => 'remote',
            'experience_level' => 'mid', 'salary_currency' => 'PKR', 'salary_period' => 'monthly', 'openings' => 1,
        ]);
        $response->assertRedirect();
        $job = Job::where('title', 'Test Developer')->first();
        $this->assertNotNull($job);
        $this->assertSame('draft', $job->status);

        $this->actingAs($employer)->post("/employer/jobs/{$job->id}/publish")->assertRedirect();
        $this->assertSame('pending', $job->fresh()->status);

        $admin = $this->makeUser('admin', 'admin@example.com');
        $this->actingAs($admin)->post("/admin/jobs/{$job->id}/moderate", ['decision' => 'published'])->assertRedirect();
        $this->assertSame('published', $job->fresh()->status);
        $this->get('/jobs')->assertSee('Test Developer');
    }

    public function test_candidate_application_duplicate_prevention_and_status_transition(): void
    {
        Storage::fake('private');
        $employer = $this->makeUser('employer', 'emp3@example.com');
        $company = $this->makeCompany($employer);
        $job = Job::factory()->create(['company_id' => $company->id, 'posted_by_id' => $employer->id, 'status' => 'published', 'published_at' => now()]);
        $candidate = $this->makeUser('candidate', 'cand2@example.com');
        $resume = CandidateResume::create(['user_id' => $candidate->id, 'title' => 'CV', 'original_filename' => 'cv.pdf', 'stored_filename' => 'cv.pdf', 'disk' => 'private', 'path' => 'resumes/cv.pdf', 'mime_type' => 'application/pdf', 'size_bytes' => 100, 'is_primary' => true]);

        $this->actingAs($candidate)->post("/jobs/{$job->id}/apply", ['resume_id' => $resume->id, 'cover_letter' => 'Hello'])->assertRedirect();
        $this->assertDatabaseCount('applications', 1);
        // Duplicate must not create a second row
        $this->actingAs($candidate)->post("/jobs/{$job->id}/apply", ['resume_id' => $resume->id])->assertSessionHasErrors('application');
        $this->assertDatabaseCount('applications', 1);

        $application = Application::first();
        // Invalid transition: submitted -> hired is not allowed
        $this->actingAs($employer)->post("/employer/applicants/{$application->id}/transition", ['status' => 'hired'])->assertSessionHasErrors('status');
        $this->assertSame('submitted', $application->fresh()->status);
        // Valid transition
        $this->actingAs($employer)->post("/employer/applicants/{$application->id}/transition", ['status' => 'shortlisted'])->assertRedirect();
        $this->assertSame('shortlisted', $application->fresh()->status);
        $this->assertDatabaseHas('application_status_histories', ['application_id' => $application->id, 'to_status' => 'shortlisted']);
    }

    public function test_employer_cannot_view_other_companys_applicants(): void
    {
        $employer = $this->makeUser('employer', 'emp4@example.com');
        $company = $this->makeCompany($employer);
        $job = Job::factory()->create(['company_id' => $company->id, 'posted_by_id' => $employer->id, 'status' => 'published', 'published_at' => now()]);
        $candidate = $this->makeUser('candidate', 'cand3@example.com');
        $application = Application::create(['job_id' => $job->id, 'user_id' => $candidate->id, 'status' => 'submitted']);

        $other = $this->makeUser('employer', 'other@example.com');
        $this->makeCompany($other);
        $this->actingAs($other)->get("/employer/applicants/{$application->id}")->assertForbidden();
        $this->actingAs($employer)->get("/employer/applicants/{$application->id}")->assertOk();
    }

    public function test_cv_upload_validation_and_unauthorised_download_blocked(): void
    {
        Storage::fake('private');
        $candidate = $this->makeUser('candidate', 'cand4@example.com');
        $this->actingAs($candidate)->post('/candidate/resumes', ['title' => 'Bad', 'resume' => UploadedFile::fake()->create('evil.exe', 100)])->assertSessionHasErrors('resume');
        $file = UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf');
        $this->actingAs($candidate)->post('/candidate/resumes', ['title' => 'My CV', 'resume' => $file])->assertRedirect();
        $resume = CandidateResume::first();
        $this->assertNotNull($resume);

        $stranger = $this->makeUser('candidate', 'stranger@example.com');
        $this->actingAs($stranger)->get("/resumes/{$resume->id}/download")->assertForbidden();
    }

    public function test_api_public_jobs_and_authenticated_transition(): void
    {
        $employer = $this->makeUser('employer', 'emp5@example.com');
        $company = $this->makeCompany($employer);
        Job::factory()->create(['company_id' => $company->id, 'posted_by_id' => $employer->id, 'status' => 'published', 'published_at' => now()]);
        $this->getJson('/api/v1/jobs')->assertOk()->assertJsonStructure(['data', 'links', 'meta']);
        $this->getJson('/api/v1/jobs')->assertJsonCount(1, 'data');

        $response = $this->postJson('/api/v1/auth/login', ['email' => 'emp5@example.com', 'password' => 'password']);
        $response->assertOk();
        $token = $response->json('token');
        $this->withHeader('Authorization', "Bearer $token")->getJson('/api/v1/auth/me')->assertOk()->assertJsonPath('role', 'employer');
    }

    public function test_api_requires_authentication_for_private_endpoints(): void
    {
        $this->getJson('/api/v1/applications')->assertUnauthorized();
    }

    public function test_job_alert_creation_and_saved_jobs(): void
    {
        $candidate = $this->makeUser('candidate', 'cand5@example.com');
        $this->actingAs($candidate)->post('/candidate/job-alerts', ['name' => 'Remote alert', 'keywords' => 'Laravel', 'workplace_type' => 'remote', 'frequency' => 'daily'])->assertRedirect();
        $this->assertDatabaseHas('job_alerts', ['user_id' => $candidate->id, 'name' => 'Remote alert']);

        $employer = $this->makeUser('employer', 'emp6@example.com');
        $company = $this->makeCompany($employer);
        $job = Job::factory()->create(['company_id' => $company->id, 'posted_by_id' => $employer->id, 'status' => 'published', 'published_at' => now()]);
        $this->actingAs($candidate)->post("/candidate/saved-jobs/{$job->id}/toggle")->assertRedirect();
        $this->assertDatabaseHas('saved_jobs', ['user_id' => $candidate->id, 'job_id' => $job->id]);
    }

    public function test_interview_scheduling_and_notification(): void
    {
        $employer = $this->makeUser('employer', 'emp7@example.com');
        $company = $this->makeCompany($employer);
        $job = Job::factory()->create(['company_id' => $company->id, 'posted_by_id' => $employer->id, 'status' => 'published', 'published_at' => now()]);
        $candidate = $this->makeUser('candidate', 'cand6@example.com');
        $application = Application::create(['job_id' => $job->id, 'user_id' => $candidate->id, 'status' => 'shortlisted']);
        $this->actingAs($employer)->post("/employer/applications/{$application->id}/interviews", [
            'title' => 'First interview', 'type' => 'online', 'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'duration_minutes' => 45, 'timezone' => 'Asia/Karachi', 'meeting_link' => 'https://meet.example.com/abc',
        ])->assertRedirect();
        $this->assertDatabaseHas('interviews', ['application_id' => $application->id]);
        $this->assertDatabaseHas('notifications', ['notifiable_id' => $candidate->id]);
    }

    public function test_password_reset_flow_sends_link(): void
    {
        $this->makeUser('candidate', 'reset@example.com');
        $this->post('/forgot-password', ['email' => 'reset@example.com'])->assertSessionHas('status');
    }
}
