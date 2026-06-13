#!/bin/bash
set -e

echo "=== Kode Mukti Store Deployment ==="

# Check if .env exists
if [ ! -f .env ]; then
    echo "Creating .env from .env.docker..."
    cp .env.docker .env
    echo "Please edit .env and set your passwords and Cloudflared token!"
    exit 1
fi

# Generate APP_KEY if not set
if grep -q "APP_KEY=$" .env; then
    echo "Generating APP_KEY..."
    docker compose exec app php artisan key:generate --force
fi

# Build and start containers
echo "Building and starting containers..."
docker compose up -d --build

# Wait for MySQL to be healthy
echo "Waiting for MySQL to be ready..."
sleep 10

# Run migrations
echo "Running database migrations..."
docker compose exec app php artisan migrate --force

# Seed admin user
echo "Seeding admin user..."
docker compose exec app php artisan db:seed --class=AdminUserSeeder --force

# Create storage link
echo "Creating storage symlink..."
docker compose exec app php artisan storage:link --force

# Clear and optimize cache
echo "Optimizing application..."
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache

# Set permissions
echo "Setting storage permissions..."
docker compose exec app chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
docker compose exec app chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo ""
echo "=== Deployment Complete ==="
echo "Application should be available at https://store.kodemukti.online"
echo ""
echo "To check logs: docker compose logs -f"
echo "To stop: docker compose down"
