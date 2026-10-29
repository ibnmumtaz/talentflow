<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\ApplicationStatusHistory;
use App\Models\CandidateProfile;
use App\Models\Company;
use App\Models\CompanyMembership;
use App\Models\Industry;
use App\Models\Interview;
use App\Models\Job;
use App\Models\JobAlert;
use App\Models\JobCategory;
use App\Models\Location;
use App\Models\SavedJob;
use App\Models\Skill;
use App\Models\User;
use App\Notifications\GenericNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password123');

        $admin = User::firstOrCreate(['email' => 'admin@talentflow.example'], ['name' => 'TalentFlow Admin', 'password' => $password, 'role' => 'admin', 'email_verified_at' => now(), 'onboarding_completed_at' => now()]);
        $moderator = User::firstOrCreate(['email' => 'support@talentflow.example'], ['name' => 'Sara Support', 'password' => $password, 'role' => 'moderator', 'email_verified_at' => now(), 'onboarding_completed_at' => now()]);

        $candidateNames = [
            ['Ayesha Khan', 'ayesha.khan@example.com', 'Product Designer', 'Karachi'],
            ['Bilal Ahmed', 'bilal.ahmed@example.com', 'Laravel Developer', 'Lahore'],
            ['Fatima Noor', 'fatima.noor@example.com', 'Data Analyst', 'Islamabad'],
            ['Hassan Raza', 'hassan.raza@example.com', 'Frontend Developer', 'Karachi'],
            ['Mahnoor Asif', 'mahnoor.asif@example.com', 'Digital Marketer', 'Lahore'],
            ['Usman Tariq', 'usman.tariq@example.com', 'DevOps Engineer', 'Remote — Pakistan'],
            ['Zainab Ali', 'zainab.ali@example.com', 'HR Executive', 'Islamabad'],
            ['Ali Hamza', 'ali.hamza@example.com', 'Junior Developer', 'Faisalabad'],
        ];
        $candidates = [];
        foreach ($candidateNames as [$name, $email, $title, $city]) {
            $u = User::firstOrCreate(['email' => $email], ['name' => $name, 'password' => $password, 'role' => 'candidate', 'email_verified_at' => now(), 'onboarding_completed_at' => now()]);
            $profile = CandidateProfile::firstOrCreate(['user_id' => $u->id], ['headline' => $title, 'summary' => "Experienced $title with a record of shipping useful work and learning fast.", 'city' => $city, 'current_title' => $title, 'years_of_experience' => rand(1, 8), 'open_to_work' => true, 'open_to_remote' => true]);
            $skillNames = Skill::inRandomOrder()->limit(4)->get();
            foreach ($skillNames as $s) {
                $profile->skills()->syncWithoutDetaching([$s->id => ['level' => 'intermediate']]);
            }
            $candidates[] = $u;
        }
        // Demo candidate login
        User::firstOrCreate(['email' => 'candidate@talentflow.example'], ['name' => 'Demo Candidate', 'password' => $password, 'role' => 'candidate', 'email_verified_at' => now(), 'onboarding_completed_at' => now()]);

        $companiesData = [
            ['NexaTech Solutions', 'Technology & Software', '51-200', 'verified', 'We build web platforms for clients across three continents.'],
            ['CloudServe', 'Technology & Software', '11-50', 'verified', 'Cloud infrastructure and managed services, remote-first.'],
            ['BrightCare Health', 'Healthcare', '201-500', 'verified', 'Digital health services connecting patients and clinicians.'],
            ['EduSpark', 'Education', '11-50', 'verified', 'Online learning for school and university students.'],
            ['ShopKart', 'E-commerce & Retail', '51-200', 'pending', 'Fast-growing e-commerce marketplace.'],
            ['FinLedger', 'Finance & Banking', '51-200', 'verified', 'Fintech products for SMEs.'],
            ['MediaPulse', 'Marketing & Media', '11-50', 'verified', 'Creative and performance marketing agency.'],
            ['LogiTrack', 'Logistics & Transport', '201-500', 'pending', 'Fleet and delivery technology.'],
        ];
        $companies = [];
        $employerIndex = 0;
        foreach ($companiesData as [$name, $industryName, $size, $verification, $desc]) {
            $industry = Industry::where('name', $industryName)->first();
            $location = Location::inRandomOrder()->first();
            $company = Company::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'description' => $desc, 'industry_id' => $industry?->id, 'location_id' => $location?->id, 'size' => $size, 'verification_status' => $verification, 'verified_at' => $verification === 'verified' ? now() : null, 'is_featured' => in_array($name, ['NexaTech Solutions', 'CloudServe', 'FinLedger']), 'website' => 'https://example.com/'.Str::slug($name), 'email' => 'hello@'.Str::slug($name).'.example', 'benefits' => ['Health insurance', 'Remote flexibility', 'Annual bonus']]);
            $companies[] = $company;
            $employerIndex++;
            $employer = User::firstOrCreate(['email' => 'employer'.$employerIndex.'@talentflow.example'], ['name' => "Employer $employerIndex ($name)", 'password' => $password, 'role' => 'employer', 'email_verified_at' => now(), 'onboarding_completed_at' => now()]);
            CompanyMembership::firstOrCreate(['company_id' => $company->id, 'user_id' => $employer->id], ['role' => 'owner', 'can_publish_jobs' => true, 'can_view_candidates' => true, 'can_manage_team' => true, 'status' => 'active', 'joined_at' => now()]);
            // second team member for first company
            if ($employerIndex === 1) {
                $recruiter = User::firstOrCreate(['email' => 'recruiter@talentflow.example'], ['name' => 'Demo Recruiter', 'password' => $password, 'role' => 'employer', 'email_verified_at' => now(), 'onboarding_completed_at' => now()]);
                CompanyMembership::firstOrCreate(['company_id' => $company->id, 'user_id' => $recruiter->id], ['role' => 'recruiter', 'can_publish_jobs' => true, 'can_view_candidates' => true, 'can_manage_team' => false, 'status' => 'active', 'joined_at' => now()]);
            }
        }
        $demoEmployer = User::firstOrCreate(['email' => 'employer@talentflow.example'], ['name' => 'Demo Employer', 'password' => $password, 'role' => 'employer', 'email_verified_at' => now(), 'onboarding_completed_at' => now()]);
        if (! empty($companies)) {
            CompanyMembership::firstOrCreate(['company_id' => $companies[0]->id, 'user_id' => $demoEmployer->id], ['role' => 'owner', 'can_publish_jobs' => true, 'can_view_candidates' => true, 'can_manage_team' => true, 'status' => 'active', 'joined_at' => now()]);
        }

        $jobSpecs = [
            ['Senior Laravel Developer', 'Software Development', 'full_time', 'hybrid', 'senior', 350000, 520000],
            ['Frontend Developer (React)', 'Software Development', 'full_time', 'remote', 'mid', 280000, 420000],
            ['Product Designer', 'Design & UX', 'full_time', 'hybrid', 'mid', 260000, 400000],
            ['Data Analyst', 'Data & Analytics', 'full_time', 'onsite', 'mid', 220000, 340000],
            ['Digital Marketing Executive', 'Marketing', 'full_time', 'onsite', 'junior', 120000, 190000],
            ['Customer Support Specialist', 'Customer Support', 'full_time', 'remote', 'entry', 90000, 140000],
            ['DevOps Engineer', 'Software Development', 'full_time', 'remote', 'senior', 380000, 560000],
            ['Flutter Mobile Developer', 'Software Development', 'contract', 'remote', 'mid', 250000, 380000],
            ['HR Executive', 'Human Resources', 'full_time', 'onsite', 'junior', 110000, 170000],
            ['Finance Officer', 'Finance & Accounting', 'full_time', 'onsite', 'mid', 180000, 260000],
            ['Content Writer', 'Marketing', 'part_time', 'remote', 'entry', 60000, 100000],
            ['QA Engineer', 'Software Development', 'full_time', 'hybrid', 'mid', 200000, 320000],
            ['Project Manager', 'Product Management', 'full_time', 'onsite', 'senior', 320000, 480000],
            ['SEO Specialist', 'Marketing', 'full_time', 'remote', 'mid', 150000, 230000],
            ['Backend Engineer (Python)', 'Software Development', 'full_time', 'remote', 'mid', 300000, 450000],
            ['UI/UX Intern', 'Design & UX', 'internship', 'hybrid', 'entry', 30000, 50000],
            ['Software Engineering Intern', 'Software Development', 'internship', 'onsite', 'entry', 30000, 50000],
            ['Sales Executive', 'Sales', 'full_time', 'onsite', 'junior', 130000, 200000],
            ['Operations Manager', 'Operations', 'full_time', 'onsite', 'senior', 280000, 420000],
            ['Machine Learning Engineer', 'Data & Analytics', 'full_time', 'remote', 'senior', 400000, 600000],
            ['Business Analyst', 'Data & Analytics', 'full_time', 'hybrid', 'mid', 220000, 330000],
            ['Graphic Designer', 'Design & UX', 'freelance', 'remote', 'junior', 80000, 150000],
            ['Network Engineer', 'Engineering', 'full_time', 'onsite', 'mid', 210000, 310000],
            ['Nurse / Care Coordinator', 'Healthcare', 'full_time', 'onsite', 'junior', 140000, 210000],
            ['Teacher — Mathematics', 'Healthcare', 'full_time', 'onsite', 'mid', 120000, 180000],
            ['Accountant', 'Finance & Accounting', 'full_time', 'onsite', 'mid', 170000, 250000],
            ['Social Media Manager', 'Marketing', 'full_time', 'hybrid', 'mid', 160000, 240000],
            ['Full-Stack Developer', 'Software Development', 'full_time', 'remote', 'mid', 320000, 480000],
            ['Junior Laravel Developer', 'Software Development', 'full_time', 'hybrid', 'entry', 130000, 200000],
            ['Delivery Operations Lead', 'Operations', 'full_time', 'onsite', 'senior', 240000, 360000],
            ['Product Manager', 'Product Management', 'full_time', 'hybrid', 'senior', 340000, 520000],
            ['Support Engineer', 'Customer Support', 'full_time', 'remote', 'junior', 110000, 170000],
            ['Data Engineer', 'Data & Analytics', 'full_time', 'remote', 'senior', 360000, 540000],
            ['Recruitment Officer', 'Human Resources', 'full_time', 'onsite', 'junior', 120000, 180000],
            ['E-commerce Manager', 'Operations', 'full_time', 'hybrid', 'mid', 230000, 350000],
            ['Security Engineer', 'Engineering', 'full_time', 'remote', 'senior', 380000, 580000],
        ];

        $jobs = [];
        foreach ($jobSpecs as $i => [$title, $catName, $etype, $wtype, $level, $min, $max]) {
            $company = $companies[$i % count($companies)];
            $category = JobCategory::where('name', $catName)->first();
            $location = Location::inRandomOrder()->first();
            $status = $i % 9 === 8 ? 'pending' : ($i % 11 === 10 ? 'draft' : 'published');
            $poster = $company->memberships()->first()?->user_id ?? $admin->id;
            $job = Job::firstOrCreate(
                ['company_id' => $company->id, 'slug' => Str::slug($title).'-'.Str::slug($company->name)],
                [
                    'posted_by_id' => $poster,
                    'category_id' => $category?->id,
                    'location_id' => $location?->id,
                    'title' => $title,
                    'summary' => "Join {$company->name} as a $title. You will work with a supportive team on meaningful problems.",
                    'description' => "About the role\n\nWe are looking for a $title to join {$company->name}. You will collaborate with talented colleagues, take ownership of your work, and grow your skills in a supportive environment.\n\nWhat you will do\n\n- Deliver high-quality work on time\n- Collaborate with cross-functional teams\n- Communicate clearly and proactively\n- Continuously improve how we work",
                    'responsibilities' => "- Deliver high-quality work\n- Collaborate with the team\n- Document decisions and outcomes",
                    'requirements' => "- Relevant experience in a similar role\n- Strong communication skills\n- A portfolio or examples of previous work where applicable",
                    'employment_type' => $etype,
                    'workplace_type' => $wtype,
                    'experience_level' => $level,
                    'salary_min' => $min,
                    'salary_max' => $max,
                    'salary_currency' => 'PKR',
                    'openings' => rand(1, 3),
                    'application_deadline' => now()->addDays(rand(10, 45))->toDateString(),
                    'expires_at' => now()->addDays(rand(20, 60))->toDateString(),
                    'status' => $status,
                    'is_featured' => $i % 7 === 0,
                    'is_remote' => $wtype === 'remote',
                    'published_at' => $status === 'published' ? now()->subDays(rand(0, 20)) : null,
                    'benefits' => ['Health insurance', 'Paid leave', 'Learning budget'],
                    'views_count' => rand(20, 900),
                ]
            );
            $job->skills()->syncWithoutDetaching(Skill::inRandomOrder()->limit(3)->pluck('id')->all());
            if ($i % 4 === 0) {
                $job->screeningQuestions()->firstOrCreate(['question' => 'What is your notice period?'], ['type' => 'text', 'is_required' => false]);
                $job->screeningQuestions()->firstOrCreate(['question' => 'Are you willing to work in a hybrid arrangement?'], ['type' => 'yes_no', 'is_required' => false]);
            }
            $jobs[] = $job;
        }

        $stages = ['submitted', 'under_review', 'shortlisted', 'interview_scheduled', 'interview_completed', 'offer_extended', 'hired', 'rejected'];
        $ai = 0;
        foreach ($jobs as $job) {
            if ($job->status !== 'published') {
                continue;
            }
            foreach (array_slice($candidates, 0, rand(2, 5)) as $cand) {
                $stage = $stages[$ai % count($stages)];
                $ai++;
                $app = Application::firstOrCreate(['job_id' => $job->id, 'user_id' => $cand->id], [
                    'cover_letter' => 'I am excited about this role and believe my experience is a strong fit. I would welcome the chance to discuss it further.',
                    'status' => $stage,
                    'evaluation_score' => in_array($stage, ['shortlisted', 'interview_completed', 'offer_extended', 'hired']) ? rand(65, 95) / 10 : null,
                ]);
                ApplicationStatusHistory::firstOrCreate(['application_id' => $app->id, 'to_status' => 'submitted'], ['from_status' => null, 'changed_by_id' => $cand->id]);
                if ($stage !== 'submitted') {
                    ApplicationStatusHistory::firstOrCreate(['application_id' => $app->id, 'to_status' => $stage], ['from_status' => 'submitted', 'changed_by_id' => $job->posted_by_id]);
                }
                if (in_array($stage, ['interview_scheduled', 'interview_completed', 'offer_extended', 'hired'])) {
                    Interview::firstOrCreate(['application_id' => $app->id], ['job_id' => $job->id, 'scheduled_by_id' => $job->posted_by_id, 'title' => 'First interview', 'type' => 'online', 'scheduled_at' => now()->addDays(rand(1, 7)), 'duration_minutes' => 45, 'meeting_link' => 'https://meet.example.com/'.Str::random(8), 'status' => $stage === 'interview_completed' ? 'completed' : 'scheduled']);
                }
            }
            $job->update(['applications_count_cached' => $job->applications()->count()]);
        }

        foreach ($candidates as $cand) {
            $saved = Job::published()->inRandomOrder()->limit(3)->get();
            foreach ($saved as $j) {
                SavedJob::firstOrCreate(['user_id' => $cand->id, 'job_id' => $j->id]);
            }
            JobAlert::firstOrCreate(['user_id' => $cand->id, 'name' => 'Remote developer roles'], ['keywords' => 'developer', 'workplace_type' => 'remote', 'frequency' => 'daily']);
        }

        // Welcome notifications for demo users (database channel, via generic notification data)
        foreach ([$admin, $candidates[0]] as $u) {
            $u->notify(new GenericNotification('Welcome to TalentFlow', 'Your demo account is ready. Explore jobs, companies and your dashboard.', route('home'), 'Welcome to TalentFlow'));
        }
    }
}
