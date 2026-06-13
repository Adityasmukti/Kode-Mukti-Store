@echo off
echo === Kode Mukti Store Deployment ===

REM Check if .env exists
if not exist .env (
    echo Creating .env from .env.docker...
    copy .env.docker .env
    echo Please edit .env and set your passwords and Cloudflared token!
    exit /b 1
)

REM Build and start containers
echo Building and starting containers...
docker compose up -d --build

REM Wait for MySQL
echo Waiting for MySQL to be ready...
timeout /t 15 /nobreak > nul

REM Run migrations
echo Running database migrations...
docker compose exec app php artisan migrate --force

REM Seed admin user
echo Seeding admin user...
docker compose exec app php artisan db:seed --class=AdminUserSeeder --force

REM Create storage link
echo Creating storage symlink...
docker compose exec app php artisan storage:link --force

REM Optimize
echo Optimizing application...
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache

REM Set permissions
echo Setting storage permissions...
docker compose exec app chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
docker compose exec app chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo.
echo === Deployment Complete ===
echo Application should be available at https://store.kodemukti.online
echo.
echo To check logs: docker compose logs -f
echo To stop: docker compose down
