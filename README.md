<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<h1 align="center">Laravel Dockerized with Queue & Scheduler (Dokploy Support)</h1>

## Overview

This repository contains a **production-ready Laravel application** with optimized Docker images for scalable deployment. The application is containerized into three separate services: web application, queue worker, and scheduler, each with its own optimized Dockerfile.

## Architecture

The application is split into three optimized Docker images:

1. **App Container** (`Dockerfile`) - Web application with PHP-FPM and built-in server
2. **Queue Worker** (`Dockerfile.queue`) - Background job processor
3. **Scheduler** (`Dockerfile.cron`) - Laravel task scheduler

## Key Features

- **Multi-stage builds** for optimized production images
- **Production-ready PHP configuration** with OPcache optimization
- **Health checks** for all services
- **Database-driven queue system** for reliable job processing
- **Separate containers** for better resource management and scaling
- **Dokploy compatible** for easy deployment

## Quick Start

### Prerequisites

- Docker and Docker Compose installed
- PHP 8.2+ (for local development)
- MySQL 8.0+

### Local Development Setup

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd laravel-dockerize-queue-schedular
   ```

2. **Copy environment file**
   ```bash
   cp .env.example .env
   ```

3. **Configure your .env file**
   ```env
   APP_NAME=Laravel
   APP_ENV=local
   APP_KEY=base64:your-app-key-here
   APP_DEBUG=true
   APP_URL=http://localhost:8000

   DB_CONNECTION=mysql
   DB_HOST=db
   DB_PORT=3306
   DB_DATABASE=laravel
   DB_USERNAME=laravel
   DB_PASSWORD=secret

   QUEUE_CONNECTION=database
   ```

4. **Start the development environment**
   ```bash
   docker-compose up -d
   ```

5. **Run initial setup**
   ```bash
   # Generate application key
   docker-compose exec app php artisan key:generate

   # Run migrations
   docker-compose exec app php artisan migrate

   # Create queue table
   docker-compose exec app php artisan queue:table
   docker-compose exec app php artisan migrate
   ```

6. **Access the application**
   - Web Application: http://localhost:8000
   - Database: localhost:3306

## Production Deployment

### Using Dokploy

These Dockerfiles are specifically designed for **Dokploy** deployment, which uses individual Dockerfiles to build separate services.

1. **Deploy App Service**
   - Use `Dockerfile` for the main web application
   - Configure environment variables in Dokploy

2. **Deploy Queue Worker**
   - Use `Dockerfile.queue` for background job processing
   - Ensure database connectivity

3. **Deploy Scheduler**
   - Use `Dockerfile.cron` for Laravel task scheduling
   - Configure as a separate service in Dokploy

### Manual Docker Deployment

For manual deployment, build each image separately:

```bash
# Build app image
docker build -t laravel-app .

# Build queue worker image
docker build -f Dockerfile.queue -t laravel-queue .

# Build scheduler image
docker build -f Dockerfile.cron -t laravel-scheduler .
```

## Why Three Separate Images?

### Benefits of Separation

1. **Resource Optimization**: Each service can be scaled independently based on demand
2. **Fault Isolation**: If one service fails, others continue running
3. **Specialized Configuration**: Each container is optimized for its specific role
4. **Better Monitoring**: Individual health checks and logging per service
5. **Deployment Flexibility**: Deploy and update services independently

### Queue Driver: Database

This application uses **database** as the queue driver because:

- **Reliability**: Database transactions ensure job persistence
- **Simplicity**: No additional infrastructure (Redis/RabbitMQ) required
- **Consistency**: Jobs are stored alongside application data
- **Monitoring**: Easy to query and monitor job status

## Docker Compose for Local Development Only

The `docker-compose.yml` file is included **only for local development**. It provides:

- **MySQL database** for development
- **Volume mounts** for live code editing
- **Service dependencies** and health checks
- **Development-friendly configuration**

For production deployments, use the individual Dockerfiles with your deployment platform (Dokploy, Kubernetes, etc.).

## Environment Configuration

### Required Environment Variables

```env
# Application
APP_NAME=Laravel
APP_ENV=production
APP_KEY=base64:your-app-key-here
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=your-database
DB_USERNAME=your-username
DB_PASSWORD=your-password

# Queue
QUEUE_CONNECTION=database
```

## Container Details

### App Container
- **Base**: PHP 8.2 FPM Alpine
- **Features**: OPcache, production PHP settings, health checks
- **Port**: 8000
- **Health Check**: HTTP endpoint `/api/health/simple`

### Queue Worker Container
- **Base**: PHP 8.2 CLI Alpine
- **Features**: Optimized for background processing
- **Command**: `php artisan queue:work --verbose --sleep=3 --tries=3`
- **Health Check**: Process monitoring

### Scheduler Container
- **Base**: PHP 8.2 CLI Alpine
- **Features**: Laravel task scheduling
- **Command**: `php artisan schedule:work --verbose`
- **Health Check**: Process monitoring

## Monitoring and Health Checks

All containers include health checks:

- **App**: HTTP health endpoint
- **Queue**: Process monitoring for queue workers
- **Scheduler**: Process monitoring for scheduler

## Optimization Features

- **Multi-stage builds** to reduce image size
- **Production OPcache configuration**
- **Optimized Composer autoloading**
- **Proper file permissions** and security
- **Alpine Linux** for minimal footprint

## Support

For issues and questions, please open an issue in the repository.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
