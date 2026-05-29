#!/usr/bin/env bash
# First-run bootstrap for the local dev container. Idempotent: safe to run on
# every `docker compose up`.
set -e

cd /var/www/html

# Ensure an .env exists.
if [ ! -f .env ]; then
    echo "[entrypoint] creating .env from .env.example"
    cp .env.example .env
fi

# Write DB/APP settings into .env. `php artisan serve` only forwards variables
# that exist in the .env file to its built-in server (compose `environment`
# alone reaches the CLI but NOT served HTTP requests), so these must live here.
set_env() {
    local key="$1" value="$2"
    if grep -qE "^${key}=" .env; then
        sed -i "s|^${key}=.*|${key}=${value}|" .env
    else
        printf '%s=%s\n' "$key" "$value" >> .env
    fi
}
set_env DB_CONNECTION "${DB_CONNECTION}"
set_env DB_HOST "${DB_HOST}"
set_env DB_PORT "${DB_PORT}"
set_env DB_DATABASE "${DB_DATABASE}"
set_env DB_USERNAME "${DB_USERNAME}"
set_env DB_PASSWORD "${DB_PASSWORD}"
set_env APP_URL "${APP_URL}"
set_env ORDER_INVITE_CODE "${ORDER_INVITE_CODE}"

# Check for the autoloader/bin, not the directory: the named volumes are
# mounted as empty dirs on first run, so a bare `-d` check would wrongly skip.
if [ ! -f vendor/autoload.php ]; then
    echo "[entrypoint] installing composer dependencies"
    composer install --no-interaction --prefer-dist
fi

if [ ! -d node_modules/.bin ]; then
    echo "[entrypoint] installing npm dependencies"
    npm install --legacy-peer-deps
fi

if ! grep -q '^APP_KEY=base64:' .env; then
    echo "[entrypoint] generating application key"
    php artisan key:generate
fi

# Public storage symlink for /storage/* image URLs.
if [ ! -L public/storage ]; then
    php artisan storage:link || true
fi

# Wait for the database before migrating.
echo "[entrypoint] waiting for database at ${DB_HOST}:${DB_PORT}..."
until php -r '
    try {
        new PDO(
            sprintf("mysql:host=%s;port=%s", getenv("DB_HOST"), getenv("DB_PORT") ?: 3306),
            getenv("DB_USERNAME"),
            getenv("DB_PASSWORD")
        );
        exit(0);
    } catch (Throwable $e) {
        exit(1);
    }
' 2>/dev/null; do
    sleep 2
done
echo "[entrypoint] database is up"

php artisan migrate --force

exec "$@"
