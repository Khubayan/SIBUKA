#!/usr/bin/env bash
# SIBUKA — skrip deploy produksi (Linux)
# Usage: ./deploy/deploy.sh
set -euo pipefail

APP_DIR="$(cd "$(dirname "$0")/.." && pwd)"
cd "$APP_DIR"

echo "==> Deploy SIBUKA di ${APP_DIR}"

if [[ "${APP_ENV:-}" != "production" ]]; then
    echo "Peringatan: APP_ENV bukan production. Set APP_ENV=production di .env sebelum deploy."
fi

echo "==> Maintenance mode"
php artisan down --retry=60 || true

echo "==> Install dependencies"
composer install --optimize-autoloader --no-dev --no-interaction

echo "==> Migrate"
php artisan migrate --force

echo "==> Cache & optimize"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "==> Storage link"
php artisan storage:link --force 2>/dev/null || true

echo "==> Permission (Linux)"
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo "==> Restart queue workers"
php artisan queue:restart

echo "==> Maintenance mode off"
php artisan up

echo "==> Deploy selesai. Jalankan: php artisan test (di staging) sebelum go-live."
