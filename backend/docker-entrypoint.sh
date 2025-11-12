#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

mkdir -p storage bootstrap/cache

if [ ! -d vendor ] || [ ! -f vendor/autoload.php ]; then
  echo "==> Installing composer dependencies..."
  composer install --no-interaction --prefer-dist
fi

echo "==> Ensuring permissions for storage paths..."
chown -R www-data:www-data vendor storage bootstrap/cache || true

if [ -f package.json ]; then
  if [ ! -d node_modules ] || [ -z "$(ls -A node_modules 2>/dev/null)" ]; then
    echo "==> Installing npm dependencies..."
    npm install
    chown -R www-data:www-data node_modules || true
  fi
fi

exec "$@"
