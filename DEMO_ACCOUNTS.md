# DEMO_ACCOUNTS.md — DEVELOPMENT ONLY

> ⚠️ **Do not use these accounts in production.** Change every password,
> disable or delete the demo accounts, and re-seed with real data before
> any real deployment. All demo identities are fictional.

All demo accounts use the password: **`password123`**

| Role | Email | Notes |
|---|---|---|
| Administrator | `admin@talentflow.example` | Full admin dashboard, user/company moderation, settings, exports |
| Moderator / Support | `support@talentflow.example` | Moderation, content, reports, audit logs — no user management or settings |
| Candidate | `candidate@talentflow.example` | Profile, CVs, applications, saved jobs, alerts |
| Employer (company owner) | `employer@talentflow.example` | Owner of NexaTech Solutions; full company + job + team access |
| Recruiter | `recruiter@talentflow.example` | NexaTech recruiter; jobs + applicants, no team management |

Additional seeded accounts (same password):

- Employers: `employer1@talentflow.example` … `employer8@talentflow.example`
  (each owns one of the 8 demo companies).
- Candidates: `ayesha.khan@example.com`, `bilal.ahmed@example.com` and
  other fictional candidates created by `DemoSeeder`.

## How to log in

1. Start the app: `php artisan serve --host=127.0.0.1 --port=8000`
2. Open http://127.0.0.1:8000/login
3. Use any email above with password `password123`. The login page also
   lists the demo accounts.

## Resetting demo data

```bash
# Development only — destroys all data in the talentflow database:
php artisan migrate:fresh --seed
```

Never run this on a database containing real user data.
