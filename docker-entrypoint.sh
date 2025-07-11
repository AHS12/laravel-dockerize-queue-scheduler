#!/bin/sh

# Docker entrypoint script for Laravel application
set -e

echo "🚀 Starting Laravel application..."

# Wait for database to be ready
echo "⏳ Waiting for database connection..."
until php artisan migrate:status --database=mysql > /dev/null 2>&1; do
  echo "🔄 Database not ready, waiting 2 seconds..."
  sleep 2
done

echo "✅ Database connection established"

# Run database migrations
echo "🔧 Running database migrations..."
php artisan migrate --force

# Cache configuration and routes for better performance
echo "⚡ Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🎉 Laravel application is ready!"

# Execute the main command
exec "$@"
