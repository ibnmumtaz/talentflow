# FEATURES.md

Every feature below is implemented and persisted in the database. Nothing
listed here is a mockup.

## Public website

- Home page: hero search, trusted/featured companies, featured + latest +
  remote jobs, popular categories and locations with **real job counts**,
  statistics calculated from the database, how-it-works, candidate/employer
  benefits, career articles, testimonials, FAQs, newsletter signup
  (with consent, stored in `newsletter_subscribers`).
- Job search: keyword/title/company, category, industry, location, skills,
  employment type, workplace type, remote-only, experience level, salary
  min/max, date posted, verified employers, sorting (newest / salary),
  pagination with query preservation, active-filter chips, reset.
- Job detail: structured sections, responsibilities/requirements, salary,
  benefits, skills, screening questions, save/apply, report form,
  JobPosting JSON-LD, canonical URL.
- Curated lists: `/jobs/remote`, `/jobs/internships`, `/jobs/entry-level`,
  `/jobs/featured`.
- Companies: index with search/filters, profile page, career page
  (open jobs), Organization JSON-LD.
- Browse: industries, categories, locations — each with real counts.
- Resources: article listing + detail, FAQs, about, contact (stored
  message handling via the contact form), employers/solutions, pricing
  (configurable plans from the database), terms, privacy.
- SEO: per-page titles/descriptions, Open Graph, sitemap.xml, robots.txt,
  human-readable slugs (`/jobs/{slug}`, `/companies/{slug}`).
- Error pages: 403, 404, 419, 429, 500 in the glassmorphism design.

## Candidate workflows

Register → verify email → onboarding → profile (headline, summary, contact,
location, education, experience, skills, certifications, languages,
portfolio links) with a **completeness percentage** → upload CVs (multiple,
default CV, replace/delete) → search → save → apply (screening answers,
CV + optional cover letter, duplicate blocked by controller + DB unique
constraint) → track timeline → withdraw (non-terminal stages only) →
interview invitations → notifications → job alerts → settings
(preferences, password, data export JSON, account-deletion request).

## Employer / company workflows

Register as employer → create company or join by invitation → complete
company profile (logo/cover upload, description, culture, links, industry,
size, location) → request verification (admin approves/rejects) → create
job draft → preview → submit for moderation (or publish directly when the
company is verified and moderation is not required) → admin approves →
publicly visible → applicants arrive → ATS pipeline (kanban + table) →
shortlist / advance / reject with internal reason → private notes +
evaluation scores → schedule interviews (type, timezone, duration,
meeting link/location, participants) → feedback → hire → close/archive
job → analytics (views are tracked per job, applications by stage,
conversion, interviews, hires) → talent pool (candidates who applied to
any company job) → team invitations with per-member permissions.

## ATS pipeline stages

`submitted → under_review → shortlisted → interview → offer → hired`,
plus `rejected` and `withdrawn`. Only the transitions in
`Application::TRANSITIONS` are allowed; every transition writes a
`application_status_histories` row, an `audit_logs` row and a candidate
notification, inside a transaction (`ApplicationService`).

## Interviews

Date/time with timezone, online/phone/in-person, duration, meeting link or
location, participants, status (scheduled / completed / cancelled /
rescheduled), employer feedback with rating, candidate confirmation where
supported. Scheduling sends a database + mail notification. No external
calendar or video integration is claimed.

## Saved jobs, alerts, notifications

- Save/unsave jobs (unique constraint), saved-jobs page.
- Job alerts: keyword, category, location, workplace type, frequency
  (instant/daily/weekly — all processed by the same daily command;
  see Limitations).
- Notification centre: unread count, mark read / mark all read, preferences
  (application updates, interviews, job alerts, marketing).
- Email notifications via Laravel notifications (log mailer locally):
  application received, status changed, interview scheduled, job moderated,
  company verification outcome, job-alert matches.

## Admin

Dashboard (users, jobs, applications, trends, popular categories, top
jobs), user list/detail (role change, suspend/reactivate, self-lockout
guard), company list/detail + verification queue, job moderation queue
(approve/reject with reason), lookups CRUD, content CRUD (articles, FAQs,
testimonials, featured flags), newsletter subscribers, reports/complaints
with resolution, audit logs (filterable), platform settings (brand, limits,
moderation toggles), notification-template note, CSV exports (users, jobs,
applications). Destructive actions require confirmation and are logged.

## API (`/api/v1`)

See API_DOCUMENTATION.md for the full list: public jobs/companies,
Sanctum login, authenticated profile, applications, saved jobs,
notifications.

## Limitations (honest, by design)

- No light theme; dark glassmorphism only.
- No two-factor authentication (architecture only — `two_factor_*` is not
  implemented).
- No CV text parsing; CVs are stored and previewed/downloaded only.
- No Google Calendar / video-service integration; interviews store a
  meeting link or location only.
- No interview-reminder job; only the scheduling notification exists.
- Job-alert frequencies are stored but all processed by the daily command.
- Subscription plans are database records only; no payment provider.
- Email verification exists but dashboard routes are not gated by the
  `verified` middleware (users can use dashboards before verifying).
- Icons are unicode/text symbols; no Lucide library is installed.
