# INSTALLATION.md — TalentFlow on Ubuntu (no Docker)

Verified on **Ubuntu 24.04.5 LTS, PHP 8.3.6, MariaDB 10.11.14, Node.js 24, Composer 2.10, Laravel 12.70**.

## 1. System dependencies

```bash
sudo apt-get update
sudo apt-get install -y php8.3-cli php8.3-common php8.3-mbstring php8.3-xml \
  php8.3-zip php8.3-sqlite3 php8.3-mysql php8.3-curl php8.3-gd php8.3-intl \
  php8.3-bcmath unzip git curl ca-certificates mariadb-server mariadb-client
```

If `apt-get update` is slow or a mirror hangs, use only the main archive with no
translations (this was needed on the build machine):

```bash
printf 'deb http://archive.ubuntu.com/ubuntu noble main restricted universe\ndeb http://archive.ubuntu.com/ubuntu noble-updates main restricted universe\ndeb http://archive.ubuntu.com/ubuntu noble-security main restricted universe\n' > /tmp/apt-src/ubuntu.list
mkdir -p /tmp/aptlists /tmp/apt-src/empty /tmp/apt-cache/archives/partial
apt-get update -o Dir::Etc::sourcelist=/tmp/apt-src/ubuntu.list \
  -o Dir::Etc::sourceparts=/tmp/apt-src/empty -o Dir::State::lists=/tmp/aptlists \
  -o Dir::Cache=/tmp/apt-cache -o Acquire::Languages=none
```

Install Composer (https://getcomposer.org):

```bash
curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
composer --version
```

## 2. Start MariaDB and create the database

```bash
mkdir -p /run/mysqld /var/lib/mysql
chown -R mysql:mysql /run/mysqld /var/lib/mysql
# Only on first install:
mariadb-install-db --user=mysql --datadir=/var/lib/mysql --auth-root-authentication-method=normal

mysqld_safe --user=mysql --datadir=/var/lib/mysql \
  --socket=/run/mysqld/mysqld.sock --pid-file=/run/mysqld/mysqld.pid \
  --bind-address=127.0.0.1 --port=3306 > /tmp/mariadb.log 2>&1 &

mariadb -u root -e "CREATE DATABASE IF NOT EXISTS talentflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'talentflow'@'127.0.0.1' IDENTIFIED BY 'talentflow';
CREATE USER IF NOT EXISTS 'talentflow'@'localhost' IDENTIFIED BY 'talentflow';
GRANT ALL ON talentflow.* TO 'talentflow'@'127.0.0.1';
GRANT ALL ON talentflow.* TO 'talentflow'@'localhost';
FLUSH PRIVILEGES;"
```

On a machine with systemd, `sudo systemctl enable --now mariadb` replaces the
`mysqld_safe` step. **Change the password** in production; never use the demo
password above outside local development.

## 3. Application setup

```bash
cd talentflow
composer install
npm install
cp .env.example .env
php artisan key:generate
```

`.env` (defaults in `.env.example`):

```env
APP_NAME=TalentFlow
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=talentflow
DB_USERNAME=talentflow
DB_PASSWORD=talentflow
MAIL_MAILER=log
MAIL_FROM_ADDRESS="hello@talentflow.example"
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
```

SQLite fallback (no MariaDB): set `DB_CONNECTION=sqlite` and
`DB_DATABASE=/absolute/path/database.sqlite`, then `touch` the file. All
migrations are portable; tests already use SQLite in memory.

## 4. Migrate and seed

```bash
php artisan migrate --seed
```

This creates 45 tables and seeds lookups, 21 users, 8 companies, 36 jobs
(29 published), 100 applications, interviews, articles, FAQs and testimonials.
**Never run `migrate:fresh` / `db:wipe` on a database containing real data.**
Seeding is idempotent for lookups, users, companies and jobs (via
`firstOrCreate`), but re-running it may add duplicate notifications.

## 5. Build assets and run

```bash
npm run build
php artisan serve --host=127.0.0.1 --port=8000
```

For development with hot reload: `npm run dev` in a second terminal (Vite).
Open http://127.0.0.1:8000.

## 6. Mail (local testing)

Default is the **log mailer**: verification links, password-reset links and
notification emails are written to `storage/logs/laravel.log`. To send real
mail, set in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="hello@talentflow.example"
MAIL_FROM_NAME="TalentFlow"
```

Then test with `php artisan tinker` sending a mail, or use the forgot-password
form and check delivery at your provider.

## 7. Queues and scheduler

- Local default `QUEUE_CONNECTION=sync`: queued notifications run immediately.
- For a real worker: set `QUEUE_CONNECTION=database` and run
  `php artisan queue:work` (supervise it with systemd in production).
- Job alerts and job expiry run via the scheduler:
  `Schedule::command('talentflow:job-alerts')->daily()` in `routes/console.php`.
  Run the scheduler with cron: `* * * * * cd /path/to/talentflow && php artisan schedule:run >> /dev/null 2>&1`
  or run the command manually: `php artisan talentflow:job-alerts`.

## 8. Tests

```bash
php artisan test
./vendor/bin/pint --test
```

Tests use SQLite in memory (`phpunit.xml`) and do not touch MariaDB.

## 9. Production notes

- Set `APP_ENV=production`, `APP_DEBUG=false`, a strong `APP_KEY`, and real
  `DB_PASSWORD` / mail credentials. Use HTTPS (`APP_URL=https://…`).
- Run `php artisan config:cache route:cache view:cache`, `npm run build`,
  and serve `public/` behind Nginx/Apache with PHP-FPM.
- Set up a supervised queue worker, the scheduler cron, log rotation, and
  backups of MariaDB and `storage/app/private` (CVs). Change all demo
  passwords and disable/rotate the seeded demo accounts.
- CVs are stored under `storage/app/private/resumes/` and served only through
  authorised controller downloads — do not symlink that directory publicly.
