<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Faq;
use App\Models\Industry;
use App\Models\JobCategory;
use App\Models\Location;
use App\Models\PlatformSetting;
use App\Models\Skill;
use App\Models\SubscriptionPlan;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LookupSeeder extends Seeder
{
    public function run(): void
    {
        $industries = ['Technology & Software', 'Finance & Banking', 'Healthcare', 'Education', 'E-commerce & Retail', 'Marketing & Media', 'Engineering', 'Logistics & Transport', 'Hospitality', 'Telecommunications'];
        foreach ($industries as $name) {
            Industry::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'description' => "Employers in $name."]);
        }

        $categories = ['Software Development', 'Data & Analytics', 'Design & UX', 'Marketing', 'Sales', 'Customer Support', 'Finance & Accounting', 'Human Resources', 'Operations', 'Product Management', 'Engineering', 'Healthcare'];
        foreach ($categories as $name) {
            JobCategory::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }

        $locations = [['Karachi', 'Sindh'], ['Lahore', 'Punjab'], ['Islamabad', 'Islamabad Capital Territory'], ['Rawalpindi', 'Punjab'], ['Faisalabad', 'Punjab'], ['Multan', 'Punjab'], ['Peshawar', 'Khyber Pakhtunkhwa'], ['Quetta', 'Balochistan'], ['Hyderabad', 'Sindh'], ['Remote — Pakistan', 'Remote']];
        foreach ($locations as [$name, $region]) {
            Location::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'region' => $region]);
        }

        $skills = ['PHP', 'Laravel', 'JavaScript', 'React', 'Vue.js', 'Python', 'SQL', 'MySQL', 'PostgreSQL', 'Docker', 'AWS', 'UI Design', 'Figma', 'SEO', 'Content Writing', 'Digital Marketing', 'Project Management', 'Data Analysis', 'Excel', 'Communication', 'Node.js', 'Flutter', 'DevOps', 'Machine Learning', 'Accounting'];
        foreach ($skills as $name) {
            Skill::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }

        SubscriptionPlan::firstOrCreate(['slug' => 'starter'], ['name' => 'Starter', 'price' => 0, 'job_posts_limit' => 1, 'features' => ['1 active job post', 'Basic applicant tracking', 'Email notifications']]);
        SubscriptionPlan::firstOrCreate(['slug' => 'growth'], ['name' => 'Growth', 'price' => 14999, 'job_posts_limit' => 10, 'featured_jobs_limit' => 2, 'is_popular' => true, 'features' => ['10 active job posts', '2 featured jobs', 'ATS pipeline & notes', 'Interview scheduling', 'Team seats (5)']]);
        SubscriptionPlan::firstOrCreate(['slug' => 'scale'], ['name' => 'Scale', 'price' => 34999, 'job_posts_limit' => 50, 'featured_jobs_limit' => 10, 'features' => ['50 active job posts', '10 featured jobs', 'Unlimited team seats', 'Advanced analytics', 'Priority support']]);

        PlatformSetting::set('site_name', 'TalentFlow');
        PlatformSetting::set('site_tagline', 'Find work you love. Hire people you trust.');
        PlatformSetting::set('jobs_require_moderation', '1');
        PlatformSetting::set('max_resume_size_kb', '5120');
        PlatformSetting::set('support_email', 'support@talentflow.example');

        $faqs = [
            ['How do I apply for a job on TalentFlow?', 'Create a free candidate account, complete your profile, upload your CV, then click Apply on any published job. You can track every application from your dashboard.', 'candidate'],
            ['Is TalentFlow free for job seekers?', 'Yes. Searching, applying, saving jobs and creating job alerts are free for candidates.', 'candidate'],
            ['How do employers post a job?', 'Register as an employer, create or join a company, then post a job. Jobs go through moderation when required and become public once published.', 'employer'],
            ['How long does job moderation take?', 'In this demo, an administrator reviews pending jobs from the admin moderation queue. Published jobs appear immediately on the public site.', 'employer'],
            ['Can I withdraw an application?', 'Yes, from My Applications, unless the application has already been marked hired or rejected.', 'candidate'],
            ['How are my CV files stored?', 'CVs are stored on a private disk and served only through authorised downloads. Employers can only view a CV attached to an application for their company.', 'all'],
        ];
        foreach ($faqs as $i => [$q, $a, $aud]) {
            Faq::firstOrCreate(['question' => $q], ['answer' => $a, 'audience' => $aud, 'sort_order' => $i]);
        }

        $articles = [
            ['How to write a CV that gets shortlisted in Pakistan', 'Career Advice', 'Focus on measurable outcomes, tailor the top of your CV to each role, and keep it to two pages. Recruiters scan for relevance first — put the strongest evidence where it cannot be missed.'],
            ['Interview preparation: 12 questions to practise', 'Interviews', 'Prepare short, specific stories for common questions. Use the STAR structure, quantify results, and prepare two thoughtful questions for the interviewer.'],
            ['Remote work: how to stand out as a remote candidate', 'Remote Work', 'Show async communication skills, a reliable workspace, and evidence of self-directed delivery. A short portfolio beats a long cover letter.'],
            ['Salary negotiation without awkwardness', 'Career Advice', 'Research market ranges, anchor on total value, and negotiate in writing. Silence after an offer is normal — use it to prepare, not to panic.'],
            ['Hiring managers: writing job posts candidates actually read', 'Hiring', 'Lead with the outcome of the role, state the salary range, and keep requirements to what is genuinely required. Short, honest posts convert better.'],
            ['Your first 90 days in a new job', 'Career Advice', 'Listen first, map stakeholders, ship one small visible win early, and agree success measures with your manager in week one.'],
        ];
        foreach ($articles as [$title, $cat, $excerpt]) {
            Article::firstOrCreate(['slug' => Str::slug($title)], ['title' => $title, 'category' => $cat, 'excerpt' => $excerpt, 'body' => $excerpt."\n\n".str_repeat('This guide is part of TalentFlow career resources. Apply the ideas in small steps, review what worked, and keep iterating. ', 6), 'reading_time_minutes' => 6, 'published_at' => now()->subDays(rand(1, 30))]);
        }

        $testimonials = [
            ['Ayesha Khan', 'Product Designer', 'NexaTech', 'TalentFlow made my job search feel organised for the first time. I could see exactly where every application stood.', 5, 'AK'],
            ['Bilal Ahmed', 'Head of Engineering', 'CloudServe', 'The pipeline view replaced three spreadsheets. Shortlisting and interview scheduling now take minutes, not days.', 5, 'BA'],
            ['Sara Malik', 'HR Manager', 'BrightCare', 'Candidate quality improved because job posts are structured and screening questions filter well.', 5, 'SM'],
        ];
        foreach ($testimonials as $i => [$name, $role, $company, $quote, $rating, $ini]) {
            Testimonial::firstOrCreate(['name' => $name], ['role_title' => $role, 'company' => $company, 'quote' => $quote, 'rating' => $rating, 'avatar_initials' => $ini, 'sort_order' => $i]);
        }
    }
}
