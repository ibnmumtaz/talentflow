# TalentFlow — Job Portal & Applicant Tracking System

A complete, production-oriented recruitment platform built with **Laravel 12, PHP 8.3, MariaDB 10.11, Tailwind CSS 4, Alpine.js, Chart.js and Vite** — running natively on Ubuntu Linux with **no Docker**.

TalentFlow combines a public job marketplace, an applicant tracking system (ATS), candidate profiles and CV management, employer/company workspaces, interview scheduling, job alerts, notifications, admin moderation, analytics and a versioned REST API.

## Quick start (local, verified on Ubuntu 24.04)

```bash
cd talentflow
composer install
npm install
cp .env.example .env          # then set DB credentials (see INSTALLATION.md)
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve --host=127.0.0.1 --port=8000
```

Open http://127.0.0.1:8000 — demo accounts are in [DEMO_ACCOUNTS.md](DEMO_ACCOUNTS.md) (password `password123`, development only).

## What is included

- **Public site:** landing page with real database statistics, advanced job search (keyword, category, location, skills, salary, date, workplace, verified employers), job details with JobPosting structured data, company profiles and career pages, industries/categories/locations browsing, career articles, FAQs, pricing, about/contact/terms/privacy, sitemap and robots.txt, custom 403/404/419/429/500 pages.
- **Candidates:** onboarding, profile (education, experience, skills, certifications, languages, completeness score), private CV uploads (PDF/DOCX, 5 MB, private disk, authorised downloads only), applications with screening answers and duplicate prevention, status timeline, withdrawal, saved jobs, job alerts, interviews, notifications, settings, data export and deletion requests.
- **Employers & companies:** company onboarding and verification requests, job drafts/preview/publish/close/archive with moderation, ATS kanban + table views, stage transitions validated server-side, private notes and evaluation scores, bulk actions, interview scheduling and feedback, talent pool, team invitations and per-member permissions, recruitment analytics with Chart.js.
- **Admin:** dashboard with registration/application trends, user management (roles, suspend/reactivate, self-lockout protection), company verification queue, job moderation, lookups (industries, categories, skills, locations), content (articles, FAQs, testimonials, newsletter), reports, audit logs, platform settings, CSV exports.
- **API:** `/api/v1` with Sanctum tokens — public jobs/companies, authenticated applications, saved jobs, notifications. See [API_DOCUMENTATION.md](API_DOCUMENTATION.md).
- **Security:** role middleware, policies/gates, server-side validation (Form Requests), CSRF, login throttling, suspension checks, private file serving, audit logging, mass-assignment protection.

## Design

Dark glassmorphism throughout: midnight navy/indigo backgrounds, violet/indigo/cyan gradients, translucent blurred glass surfaces, reusable Blade components (`x-job-card`, `x-stat-card`, `x-badge`, `x-empty-state`), responsive layouts with a mobile dashboard drawer, accessible labels, focus indicators and reduced-motion support. Tokens live in `resources/css/app.css`. Icons are text/unicode symbols (no icon library is installed).

## Documentation

| File | Contents |
|---|---|
| [INSTALLATION.md](INSTALLATION.md) | Native Ubuntu setup, MariaDB, mail, queues, scheduler |
| [DATABASE.md](DATABASE.md) | Schema, relationships, indexes, constraints, the queue-table rename |
| [FEATURES.md](FEATURES.md) | Implemented workflows, page inventory summary |
| [ROLES_AND_PERMISSIONS.md](ROLES_AND_PERMISSIONS.md) | Role/permission matrix and enforcement points |
| [API_DOCUMENTATION.md](API_DOCUMENTATION.md) | Every API endpoint, auth and response format |
| [TESTING.md](TESTING.md) | How tests run and what they cover |
| [TROUBLESHOOTING.md](TROUBLESHOOTING.md) | Common errors and fixes |
| [DEMO_ACCOUNTS.md](DEMO_ACCOUNTS.md) | Development-only logins (change before any real deployment) |
| [ROUTES.md](ROUTES.md) | Full route and page inventory |

## Decisions worth knowing

- **No permission package.** Roles are a native `users.role` column plus `company_memberships` (owner / hiring_manager / recruiter with `can_publish_jobs`, `can_view_candidates`, `can_manage_team`) enforced by policies and middleware. This avoids a duplicate permission system and keeps the authorisation model auditable in one place. See ROLES_AND_PERMISSIONS.md.
- **Queue table renamed.** Laravel's default queue table is named `jobs`, which collides with the domain `jobs` table. The queue table is `queue_jobs` (`config/queue.php`, `DB_QUEUE_TABLE`). See DATABASE.md.
- **Local queue driver is `sync`.** Notifications implement `ShouldQueue`; with `sync` they run immediately in the demo. Set `QUEUE_CONNECTION=database` and run `php artisan queue:work` for a real worker (see INSTALLATION.md).
- **Mail is the log mailer locally.** Verification, password-reset and notification emails are written to `storage/logs/laravel.log`. Configure SMTP in `.env` for real delivery (see INSTALLATION.md).
- **No paid services.** No external search, AI, payment, calendar or video integrations are claimed. Subscription plans are configurable data, ready for a payment provider later.

## Project layout

```
app/Http/Controllers/{Auth,Candidate,Employer,Admin,Api/V1}  HTTP layer (thin controllers)
app/Models/            Eloquent models, scopes, relationships, casts
app/Policies/          Company, Job, Application, Resume, Interview
app/Services/          ApplicationService (validated stage transitions)
app/Notifications/     Database + mail notifications
app/Console/Commands/  talentflow:job-alerts (alerts + job expiry)
database/migrations/   9 migrations, 45 tables
database/seeders/      LookupSeeder + DemoSeeder (36 jobs, 8 companies, 100 applications)
resources/views/       Public, candidate, employer, admin, shared, errors
routes/{web,api,api_v1,console}.php
tests/Feature/         15 passing feature tests
```
