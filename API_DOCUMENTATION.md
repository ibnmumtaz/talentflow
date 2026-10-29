# API_DOCUMENTATION.md — TalentFlow REST API v1

Base URL: `{APP_URL}/api/v1` — e.g. `http://127.0.0.1:8000/api/v1`.
Rate limit: 60 requests/minute (`api` limiter). All responses are JSON.
Errors use Laravel's standard shape (`{ "message": "...", "errors": {...} }`).

## Authentication

Sanctum personal access tokens. Obtain a token:

```
POST /api/v1/auth/login
Content-Type: application/json

{ "email": "candidate@talentflow.example", "password": "password123" }
```

Response `200`:

```json
{ "token": "1|...", "user": { "id": 1, "name": "...", "email": "...", "role": "candidate" } }
```

Use it as `Authorization: Bearer <token>` on protected endpoints.
`POST /api/v1/auth/logout` (authenticated) revokes the current token.
`GET /api/v1/auth/me` (authenticated) returns the current user.

Unauthenticated requests to protected endpoints return `401`.
Unauthorised access to another user's/company's data returns `403`.

## Public endpoints (no auth)

| Method | Endpoint | Description |
|---|---|---|
| GET | `/jobs` | Paginated published jobs. Filters: `keyword`, `category_id`, `location_id`, `employment_type`, `workplace_type`, `experience_level`, `skill_id`, `salary_min`, `salary_max`, `posted_within` (1/7/30), `verified`, `remote_only`, `sort` (newest/salary_desc/salary_asc). |
| GET | `/jobs/{job}` | Job detail (by id or slug). Increments `views_count`. |
| GET | `/companies` | Paginated active companies. Filters: `keyword`, `industry_id`, `location_id`, `verified`. |
| GET | `/companies/{company}` | Company detail (by id or slug). |

Job payloads use `JobResource` (id, slug, title, company, category, location,
salary label, employment/workplace type, skills, published date, views).
Company payloads use `CompanyResource`. No private candidate or company
data is exposed on public endpoints.

## Authenticated endpoints (`auth:sanctum`)

| Method | Endpoint | Who | Description |
|---|---|---|---|
| GET | `/auth/me` | any | Current user |
| POST | `/auth/logout` | any | Revoke current token |
| GET | `/applications` | candidate / employer | Own applications (candidate) or company applications (employer, scoped) |
| GET | `/applications/{application}` | owner / scoped employer | Application detail |
| POST | `/applications/{application}/transition` | employer | Body: `{ "status": "<stage>", "reason": "..." }`. Only allowed transitions (see Application::TRANSITIONS); writes history + notification. |
| GET | `/saved-jobs` | candidate | Saved jobs |
| POST | `/saved-jobs/{job}/toggle` | candidate | Save or unsave a job (toggle) |
| GET | `/notifications` | any | Paginated notifications, newest first |
| POST | `/notifications/read-all` | any | Mark all read |

## Pagination

List endpoints return Laravel paginator JSON: `data`, `links`, `meta`
(`current_page`, `per_page`, `total`). Default page size 15 (jobs: 12).

## Examples

```bash
# Public jobs
curl "http://127.0.0.1:8000/api/v1/jobs?keyword=laravel&remote_only=1"

# Login + use token
TOKEN=$(curl -s -X POST http://127.0.0.1:8000/api/v1/auth/login \
  -H 'Content-Type: application/json' \
  -d '{"email":"candidate@talentflow.example","password":"password123"}' | python3 -c 'import sys,json;print(json.load(sys.stdin)["token"])')
curl -H "Authorization: Bearer $TOKEN" http://127.0.0.1:8000/api/v1/applications
```

## What is NOT in the API

There are no API endpoints for job creation, company management, admin
actions, CV upload/download, or interview management — those are web-only
in this release. Do not document or rely on endpoints beyond the table
above; verify with `php artisan route:list --path=api`.
