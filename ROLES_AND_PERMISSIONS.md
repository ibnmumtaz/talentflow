# ROLES_AND_PERMISSIONS.md

## Roles

`users.role` is a single string column. Allowed values: `admin`,
`moderator`, `employer`, `candidate`. There is **no separate role/permission
package** and no `roles` table — this is deliberate (see README).

| Role | Dashboard | Description |
|---|---|---|
| `candidate` | `/candidate/dashboard` | Job seeker |
| `employer` | `/employer/dashboard` | Recruiter / hiring manager / company owner |
| `moderator` | `/admin/dashboard` | Restricted support: moderation, content, reports, audit logs |
| `admin` | `/admin/dashboard` | Full platform administration |

## Company memberships (employer sub-roles)

An employer belongs to companies through `company_memberships`:

| Membership role | `can_publish_jobs` | `can_view_candidates` | `can_manage_team` |
|---|---|---|---|
| `owner` | yes | yes | yes |
| `hiring_manager` | yes | yes | no |
| `recruiter` | no (draft only) | yes | no |

The **company owner** is an employer with an `owner` membership — there is
no separate `company_owner` user role. `/company/dashboard`, `/company/team`
and `/company/analytics` are aliases to the employer controllers.

## Enforcement points (server-side, never UI-only)

| Layer | Mechanism |
|---|---|
| Route middleware | `role:admin,moderator`, `role:employer,admin`, `role:candidate` (`app/Http/Middleware/EnsureRole.php`, alias `role`); `not_suspended` on the `web` group (`EnsureNotSuspended`) |
| Policies | `CompanyPolicy`, `JobPolicy`, `ApplicationPolicy`, `ResumePolicy`, `InterviewPolicy` — registered in `AppServiceProvider` |
| Gates | `admin`, `manage-company`, `publish-jobs` in `AppServiceProvider` |
| Controllers | `authorize()` calls; company scope via `auth()->user()->companies()`; cross-company access returns 403 |
| Models / DB | Unique constraints (duplicate applications, duplicate saves), foreign keys |
| API | Sanctum `auth:sanctum` + the same policies; public endpoints expose no private data |

## Permission matrix (summary)

| Capability | Guest | Candidate | Employer | Moderator | Admin |
|---|---|---|---|---|---|
| Browse jobs/companies/resources | ✓ | ✓ | ✓ | ✓ | ✓ |
| Apply / save jobs / alerts | — | ✓ own | — | — | — |
| View own CVs / applications | — | ✓ own | — | — | — |
| Create/edit own-company jobs | — | — | ✓ scoped | — | ✓ |
| View applicants / CVs | — | — | ✓ scoped + `can_view_candidates` | — | ✓ |
| Publish jobs | — | — | ✓ scoped + `can_publish_jobs` | — | ✓ |
| Manage team / invitations | — | — | owner only | — | ✓ |
| Moderate jobs / verify companies | — | — | — | ✓ | ✓ |
| Manage users / roles / suspend | — | — | — | — | ✓ |
| Platform settings / CSV exports | — | — | — | — | ✓ |
| Content (articles/FAQs/testimonials) | — | — | — | ✓ | ✓ |
| Reports / audit logs | — | — | — | ✓ | ✓ |

## Safety rules

- A user cannot change their own `role` (no endpoint exposes it; registration
  accepts only `candidate`/`employer`).
- The initial admin cannot suspend or demote themselves
  (`Admin\UserController` guards `auth()->id() === $user->id`).
- Suspended users are blocked by `EnsureNotSuspended` on every web request
  and get a 403 from the API.
- Login is throttled: 5 attempts/minute (`login` rate limiter).
- Employer access is always scoped: a job/application is reachable only if
  its company is in the user's memberships. Cross-company access is denied
  and covered by a feature test.
- CV downloads go through `ResumeDownloadController` / `ResumeController`
  with `ResumePolicy`; there are no public URLs to private CVs.
- All admin and ATS actions write to `audit_logs` with actor, action,
  target and IP.
