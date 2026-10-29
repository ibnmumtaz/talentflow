# DATABASE.md

Engine: **MariaDB 10.11** (MySQL-compatible). Tests use SQLite in memory.
45 tables after `php artisan migrate`. Charset `utf8mb4`, collation
`utf8mb4_unicode_ci`.

## Tables

| Group | Tables |
|---|---|
| Auth / core | `users`, `password_reset_tokens`, `sessions`, `cache`, `cache_locks`, `personal_access_tokens`, `notifications`, `queue_jobs`, `job_batches`, `failed_jobs`, `migrations` |
| Lookups | `industries`, `job_categories`, `locations`, `skills`, `subscription_plans`, `platform_settings`, `faqs`, `articles`, `testimonials`, `newsletter_subscribers` |
| Companies | `companies` (soft deletes), `company_memberships`, `company_invitations`, `company_verifications` |
| Jobs | `jobs` (soft deletes), `job_skills`, `job_screening_questions` |
| Candidates | `candidate_profiles`, `candidate_educations`, `candidate_experiences`, `candidate_skills`, `candidate_certifications`, `candidate_languages`, `candidate_resumes`, `saved_jobs`, `job_alerts` |
| Applications | `applications`, `application_answers`, `application_status_histories`, `application_notes`, `interviews`, `interview_participants`, `interview_feedback` |
| Moderation | `reports`, `audit_logs` |

## Key relationships

```
users 1—1 candidate_profiles 1—* educations / experiences / certifications / languages
users 1—* candidate_resumes, saved_jobs, job_alerts, applications
users *—* companies (company_memberships: role, can_publish_jobs, can_view_candidates, can_manage_team)
companies 1—* jobs 1—* applications 1—* interviews
jobs *—* skills (job_skills), 1—* job_screening_questions, 1—* applications
applications 1—* answers, status_histories, notes, interviews
interviews *—* users (interview_participants), 1—* interview_feedback
```

## Constraints and indexes

- `applications` unique `(job_id, user_id)` — duplicate applications are
  impossible at the database level, in addition to the controller check.
- `saved_jobs` unique `(user_id, job_id)`; `job_skills` unique `(job_id, skill_id)`;
  `candidate_skills` unique `(candidate_profile_id, skill_id)`;
  `company_memberships` unique `(company_id, user_id)`;
  `interview_participants` unique `(interview_id, user_id)`.
- Indexes on the hot filters: `jobs(status, published_at)`,
  `(employment_type, workplace_type)`, `(category_id, status)`,
  `(location_id, status)`, `expires_at`; `applications(job_id, status)`,
  `(user_id, status)`; `users(role, is_suspended)`; `reports` and `audit_logs`
  polymorphic indexes.
- Foreign keys with `cascadeOnDelete` for owned records and `nullOnDelete`
  for lookups, so deleting a lookup does not destroy jobs.
- Soft deletes on `companies` and `jobs` only — deleting a job keeps its
  applications (history) in the database.

## The `jobs` / queue-table rename

Laravel's default queue migration creates a table called `jobs`. TalentFlow's
domain table is also `jobs`, so the two collide. The queue table is renamed:

- Migration `0001_01_01_000002_create_jobs_table.php` creates `queue_jobs`
  (plus `job_batches`, `failed_jobs`).
- `config/queue.php`: `'table' => env('DB_QUEUE_TABLE', 'queue_jobs')`.

Do not rename either table without updating both places.

## Enums as strings

Statuses, roles and types are `varchar` with application-level whitelists
(`Job::STATUSES`, `Application::STAGES`/`TRANSITIONS`, `User::ROLES`), not
MySQL `ENUM`, so the schema stays portable to SQLite for tests and the
allowed transitions stay testable in PHP (`Application::canTransition`).

## Seed data

`LookupSeeder` (idempotent lookups, plans, settings, FAQs, articles,
testimonials) then `DemoSeeder`: 1 admin, 1 moderator, 8 candidates + 1 demo
candidate, 8 companies, 8 employer accounts + 1 demo employer + 1 recruiter,
36 jobs, 100 applications across all pipeline stages, interviews, saved jobs
and one job alert per candidate. All identities are fictional.
