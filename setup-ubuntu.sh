#!/usr/bin/env bash
# TalentFlow local setup for Ubuntu — no Docker.
# Usage:  bash setup-ubuntu.sh
# It is safe to run again: it never runs migrate:fresh / db:wipe.
set -u
cd "$(dirname "$0")"

fail() { echo "ERROR: $1" >&2; exit 1; }

echo "== TalentFlow setup =="
php -v >/dev/null 2>&1 || fail "PHP not found. Install PHP 8.2+ first (see INSTALLATION.md section 1)."
PHP_MAJOR=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')
echo "PHP: $PHP_MAJOR"
command -v composer >/dev/null 2>&1 || fail "Composer not found. Install it from https://getcomposer.org"
command -v node >/dev/null 2>&1 || fail "Node.js not found. Install Node.js 20+ first."
command -v npm >/dev/null 2>&1 || fail "npm not found."
command -v mariadb >/dev/null 2>&1 || command -v mysql >/dev/null 2>&1 || fail "MariaDB client not found. Install mariadb-server (see INSTALLATION.md section 1)."

# Start MariaDB if systemd/service is available (ignored if already running).
if command -v systemctl >/dev/null 2>&1; then sudo systemctl enable --now mariadb 2>/dev/null || true; fi
if command -v service >/dev/null 2>&1; then sudo service mariadb start 2>/dev/null || true; fi

# Create database + user (uses sudo mariadb, no password, as on a fresh Ubuntu install).
# If this fails, the script prints the manual SQL and stops — it does not guess passwords.
echo "Creating database 'talentflow' (if needed)..."
if sudo mariadb -e "CREATE DATABASE IF NOT EXISTS talentflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; CREATE USER IF NOT EXISTS 'talentflow'@'127.0.0.1' IDENTIFIED BY 'talentflow'; CREATE USER IF NOT EXISTS 'talentflow'@'localhost' IDENTIFIED BY 'talentflow'; GRANT ALL ON talentflow.* TO 'talentflow'@'127.0.0.1'; GRANT ALL ON talentflow.* TO 'talentflow'@'localhost'; FLUSH PRIVILEGES;" 2>/dev/null; then
  echo "Database ready."
else
  echo "Could not create the database automatically (MariaDB root access differs on this machine)."
  echo "Run this once in MariaDB as root, then re-run this script:"
  echo "  CREATE DATABASE talentflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
  echo "  CREATE USER 'talentflow'@'127.0.0.1' IDENTIFIED BY 'talentflow';"
  echo "  GRANT ALL ON talentflow.* TO 'talentflow'@'127.0.0.1'; FLUSH PRIVILEGES;"
  echo "Or edit DB_* in .env to match your own database credentials."
  exit 1
fi

if [ ! -f .env ]; then
  cp .env.example .env
  php artisan key:generate
  echo "Created .env from .env.example"
fi

echo "Installing PHP dependencies..."
composer install --no-interaction
echo "Installing JS dependencies..."
npm install
echo "Migrating + seeding demo data (36 jobs, 8 companies, demo accounts)..."
php artisan migrate --seed
echo "Building frontend assets..."
npm run build

echo ""
echo "Done. Start TalentFlow with:"
echo "  php artisan serve --host=127.0.0.1 --port=8000"
echo "Then open http://127.0.0.1:8000/ in your browser."
echo "Demo logins (password: password123): admin@talentflow.example, candidate@talentflow.example, employer@talentflow.example"
echo "Full guide: INSTALLATION.md — troubleshooting: TROUBLESHOOTING.md"
