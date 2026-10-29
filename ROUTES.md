# ROUTES.md — Route & Page Inventory

Generated from `php artisan route:list` on 2026-10-28: **162 routes**
(plus framework `up` and Sanctum CSRF routes). Verify with
`php artisan route:list` — this file is a snapshot, the command is truth.

## Public (guest)

| Method | URI | Page |
|---|---|---|
| GET | `/` | Home / landing |
| GET | `/jobs`, `/jobs/remote`, `/jobs/internships`, `/jobs/entry-level`, `/jobs/featured` | Job lists + search |
| GET | `/jobs/{job}` | Job detail |
| GET | `/companies`, `/companies/{company}`, `/companies/{company}/careers` | Companies, profile, career page |
| GET | `/industries`, `/categories`, `/locations` | Browse pages |
| GET | `/resources`, `/resources/{article}` | Career articles |
| GET | `/faq` | Frequently asked questions |
| GET | `/about`, `/contact`, `/employers`, `/pricing`, `/terms`, `/privacy` | Static pages |
| POST | `/contact`, `/newsletter`, `/reports` | Contact, newsletter signup, report a job/account |
| GET | `/sitemap.xml`, `/robots.txt` | SEO |

## Auth

`GET/POST /login`, `GET/POST /register`, `POST /logout`,
`GET/POST /forgot-password`, `GET/POST /reset-password/{token}`,
`GET /email/verify`, `GET /email/verify/{id}/{hash}`,
`POST /email/verification-notification`, `GET/POST /onboarding`.

## Shared authenticated

`GET /notifications`, `GET /notifications/{id}` (mark read),
`POST /notifications/read-all`, `GET/PUT /settings`,
`PUT /settings/password`, `GET /settings/export`,
`POST /settings/delete-request`, `GET /resumes/{resume}/download`,
`GET /invitations/{token}` (accept team invitation).

## Candidate (`/candidate/*`, role:candidate)

Dashboard, profile edit + education/experience/skills/certifications/
languages (store/destroy), resumes (index/store/destroy/download/primary),
applications (index/show/withdraw), saved jobs (index/toggle),
job alerts (index/store/update/destroy), interviews (index/respond).
Apply: `POST /jobs/{job}/apply`.

## Employer (`/employer/*`, role:employer,admin)

Dashboard, company edit/update + verification request, jobs
(index/create/store/show/edit/update/destroy/publish/close/archive),
applicants (index/bulk/show/notes/transition), interviews
(index/update/feedback + `POST employer/applications/{application}/interviews`),
talent pool, team (index/invite/update member/destroy member),
analytics.

## Company owner aliases (`/company/*`, role:employer,admin)

`GET /company/dashboard`, `GET /company/team`, `GET /company/analytics`
— same controllers as the employer equivalents.

## Admin (`/admin/*`, role:admin,moderator)

Dashboard, users (index/show/role/suspend/reactivate), companies
(index/show/verify/toggle-active), jobs (index/moderate/featured/destroy),
lookups (index/store/destroy by type), content (articles/faqs/testimonials
store/destroy), reports (index/resolve), audit logs, settings
(index/update). **Admin-only** (nested `role:admin`): user management,
settings, CSV exports (`/admin/exports/users`, `/admin/exports/jobs`).

## API (`/api/v1/*`)

See API_DOCUMENTATION.md. Public: `GET /jobs`, `GET /jobs/{job}`,
`GET /companies`, `GET /companies/{company}`, `POST /auth/login`.
Authenticated (Sanctum): `GET /auth/me`, `POST /auth/logout`,
`GET /applications`, `GET /applications/{application}`,
`POST /applications/{application}/transition`, `GET /saved-jobs`,
`POST /saved-jobs/{job}/toggle`, `GET /notifications`,
`POST /notifications/read-all`.

## Error pages

Custom views in `resources/views/errors/`: 403, 404, 419, 429, 500.
