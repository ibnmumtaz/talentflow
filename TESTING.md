# TESTING.md

## Running tests

```bash
php artisan test            # full suite (SQLite in-memory, no MariaDB needed)
php artisan test --filter=JobSearch   # single test, if named
./vendor/bin/pint --test    # code style check (Laravel Pint)
```

## Configuration

`phpunit.xml` sets `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`,
`QUEUE_CONNECTION=sync`, `MAIL_MAILER=array`. Tests never touch the
development MariaDB database and never send real mail.

## Coverage (tests/Feature/TalentFlowTest.php — 15 tests, 71 assertions)

| Area | Tests |
|---|---|
| Authentication | Registration creates a candidate and redirects to the candidate dashboard; login redirects by role (candidate/employer/admin); logout; password reset flow; suspended accounts are blocked |
| Authorisation | Guests cannot access private dashboards; candidates get 403 on employer/admin dashboards; employers cannot access other companies' applicants; recruiters get 403 on team management; moderators/admins can access moderation |
| Jobs | Search filters return only published/visible jobs; expired/draft jobs are hidden; job creation by an employer; moderation flow (pending → published); pagination |
| Applications | Candidate application submission; duplicate applications blocked (controller + DB constraint); valid and invalid ATS stage transitions; status history is written; candidate ownership enforced |
| Files | CV upload validation (PDF/DOCX only, size limit, executable rejected); unauthorised CV download returns 403 |
| Interviews | Employer can schedule an interview; a notification is created for the candidate |
| Alerts / saved jobs | Job alerts can be created and match jobs; saved jobs toggle works |
| API | Public job listing returns JSON; authenticated applications endpoint works; unauthenticated access to a private endpoint returns 401 |
| Pages | Landing page renders with database data; empty states render |

## Manual verification performed (2026-10-28)

- `npm run build` succeeded (Vite 7.3.7, 66 modules).
- HTTP sweep of 20+ public/auth/dashboard URLs: 200s for every role's
  own pages, 403s for cross-role access, 404 for unknown routes.
- `php artisan talentflow:job-alerts` ran cleanly.
- `php artisan route:list` (166 route lines) and `migrate:status` (9/9 ran)
  verified.

## Adding tests

Follow the existing file: use `RefreshDatabase`, factories
(`UserFactory`, `CompanyFactory`, `JobFactory`) and the seeded lookups
only where needed. Prefer feature tests that hit HTTP endpoints and assert
database state — they catch authorisation regressions that unit tests miss.
Do not disable authorisation or validation to make a test pass.
