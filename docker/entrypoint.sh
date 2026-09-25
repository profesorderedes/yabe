#!/bin/sh
set -eu

APP_DIR=/var/www/html
DATA_DIR="$APP_DIR/data"

cd "$APP_DIR"

# Create the directories Laravel needs to write to at runtime and grant the
# web user access to them (migrations and seed write to the SQLite database
# that lives inside the data volume).
mkdir -p \
    "$DATA_DIR" \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

touch "$DATA_DIR/database.sqlite"

chown -R www-data:www-data "$DATA_DIR" storage bootstrap/cache 2>/dev/null || true

# Bootstrap the environment file on first boot.
if [ ! -f .env ]; then
    cp .env.example .env
    chown www-data:www-data .env 2>/dev/null || true
fi

# Keep the SQLite path in .env as a fallback. Production passes it through the
# container environment, which `php artisan serve --no-reload` preserves.
if ! grep -qE '^DB_DATABASE=' .env; then
    printf '\nDB_DATABASE=%s\n' "$DATA_DIR/database.sqlite" >> .env
fi

as_www_data() {
    if [ "$(id -u)" = "0" ]; then
        runuser -u www-data -- "$@"
    else
        "$@"
    fi
}

# Generate the application key only when it is missing, so the key stays
# stable across container restarts.
if [ -z "${APP_KEY:-}" ] && ! grep -qE '^APP_KEY=base64:' .env; then
    echo "==> Generating application key"
    as_www_data php artisan key:generate --force
fi

echo "==> Running database migrations"
as_www_data php artisan migrate --force

echo "==> Seeding the database"
as_www_data php artisan db:seed --force

if [ "$#" -eq 0 ]; then
    set -- php artisan serve --host=0.0.0.0 --port=8000 --no-reload
fi

echo "==> Starting Laravel"
if [ "$(id -u)" = "0" ]; then
    exec runuser -u www-data -- "$@"
fi
exec "$@"