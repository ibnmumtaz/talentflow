# TROUBLESHOOTING.md

## Database / migrations

**`SQLSTATE[HY000] [2002] Connection refused`**
MariaDB is not running. Start it (see INSTALLATION.md §2) and check
`/tmp/mariadb.log`. Confirm `DB_HOST=127.0.0.1`, `DB_PORT=3306` in `.env`.

**`Table 'talentflow.jobs' already exists` / queue collision**
The queue table is `queue_jobs`, not `jobs`. If a stale migration created
a queue `jobs` table, check `config/queue.php` (`DB_QUEUE_TABLE=queue_jobs`)
and migration `0001_01_01_000002_create_jobs_table.php`. Do not rename the
domain `jobs` table.

**`candidate_education doesn't exist` / similar singular table errors**
Models use explicit `$table` names. If you add a candidate model, set
`protected $table = 'candidate_...'` explicitly — Laravel singularises
these names incorrectly.

**Migration fails on a fresh database**
Run `php artisan migrate:status` to find the failing migration, fix the
cause, and re-run `php artisan migrate`. Never run `migrate:fresh` on a
database with real data.

## Assets

**Styles look unstyled / 404 on `build/assets/*`**
Run `npm run build`. For hot reload, run `npm run dev` instead and keep
it running. After a rebuild, asset hashes change — hard-refresh the browser.

**`npm install` fails**
Check Node ≥ 20 (`node -v`). Delete `node_modules` and `package-lock.json`
only as a last resort, then `npm install` again.

## Auth / access

**Redirected to onboarding after login (employer)**
The employer has no company membership yet. Complete company setup at
`/employer/company` (or accept a team invitation). Demo employer
`employer@talentflow.example` already has an owner membership.

**403 on a page you should access**
Check `users.role` and `company_memberships` for that user, and that the
account is not suspended (`is_suspended`). Recruiters cannot access
`/employer/team` by design.

**Login throttled (429)**
5 attempts/minute. Wait a minute or clear the rate limiter cache:
`php artisan cache:clear` (database cache store).

**Email verification link goes to localhost / log**
Set `APP_URL` correctly in `.env`. Locally, verification and reset links
are written to `storage/logs/laravel.log` (log mailer) — copy the URL
from the log.

## Files / CVs

**CV download returns 404**
The file may have been deleted from `storage/app/private/resumes/`. The
controller handles this gracefully with a 404 and a flash message; re-upload
the CV. Never symlink `storage/app/private` to `public/`.

**Upload rejected**
Allowed: PDF, DOC, DOCX, max 5 MB (configurable in the Form Request /
`platform_settings`). Check `upload_max_filesize` / `post_max_size` in
`php.ini` if larger files fail silently.

## Mail / notifications

**No emails arrive**
Locally this is expected — check `storage/logs/laravel.log`. For real
delivery, configure SMTP (INSTALLATION.md §6) and confirm
`MAIL_FROM_ADDRESS` is a verified sender at your provider.

**Notifications not appearing**
With `QUEUE_CONNECTION=sync` they are created immediately. With
`database`, run `php artisan queue:work`. Check the `notifications` table
directly: `mariadb -u root talentflow -e "SELECT * FROM notifications ORDER BY created_at DESC LIMIT 5;"`.

## Performance / errors

**500 on a page**
Check `storage/logs/laravel.log` for the stack trace. Common causes:
missing table (run migrations), missing seeded lookup (run seeders),
or a stale compiled view (`php artisan view:clear`).

**Slow homepage**
The homepage aggregates counts; ensure the indexes in DATABASE.md exist
(they are created by the migrations). Avoid adding unindexed filters.

## Environment

**`apt-get update` hangs**
See INSTALLATION.md §1 for the archive-only sources workaround used on
the build machine.

**PHP version mismatch**
TalentFlow needs PHP ≥ 8.2 (built on 8.3). `php -v` to check. Laravel 12
will refuse to boot on PHP 8.1.
