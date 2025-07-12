<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<h1 align="center">Laravel Dockerized with Queue & Scheduler image (Dokploy Support)</h1>

## Overview

This repository contains a **production-ready Laravel application** with optimized Docker images for scalable deployment. The application is containerized into three separate services: web application, queue worker, and scheduler, each with its own optimized Dockerfile.

## Architecture

The application is split into three optimized Docker images:

1. **App Container** (`Dockerfile`) - Web application with PHP-FPM and built-in server
2. **Queue Worker** (`Dockerfile.queue`) - Background job processor
3. **Scheduler** (`Dockerfile.cron`) - Laravel task scheduler(cron)

## Key Features

- **Multi-stage builds** for optimized production images
- **Production-ready PHP configuration** with OPcache optimization
- **Health checks** for all services
- **Database-driven queue system** for reliable job processing
- **Separate containers** for better resource management and scaling
- **Dokploy compatible** for easy deployment
- **Custom entrypoint script** for not overloading server during deployment

## Quick Start

### Prerequisites

- Docker and Docker Compose installed

### Local Development Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/AHS12/laravel-dockerize-queue-scheduler-dokploy.git
   cd laravel-dockerize-queue-schedular-dokploy
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

   > **Note**: Migrations are handled automatically by the entrypoint scripts. The app container runs migrations on startup, and the queue/scheduler containers wait for migrations to complete before starting.

5. **Access the application**
   - Web Application: http://localhost:8000
   - Database: localhost:3306

## Production Deployment

### Using Dokploy

These Dockerfiles are specifically designed for **Dokploy** deployment. Since Dokploy currently **does not support worker nodes**, we've created separate Docker images for each service to work around this limitation.

1. **Deploy App Service**
   - Use `Dockerfile` for the main web application
   - Configure environment variables in Dokploy

2. **Deploy Queue Worker**
   - Use `Dockerfile.queue` for background job processing
   - Deploy as a separate service in Dokploy since worker nodes aren't supported
   - Ensure database connectivity

3. **Deploy Scheduler**
   - Use `Dockerfile.cron` for Laravel task scheduling
   - Deploy as a separate service in Dokploy since worker nodes aren't supported
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

### Primary Reason: Dokploy Limitation

**Dokploy currently does not support worker nodes**, which means you cannot run background processes (queues, schedulers) as workers within the same service. This architectural choice solves that limitation by creating separate deployable services.

### Additional Benefits of Separation

1. **Dokploy Compatibility**: Works around the current worker node limitation
2. **Resource Optimization**: Each service can be scaled independently based on demand
3. **Fault Isolation**: If one service fails, others continue running
4. **Specialized Configuration**: Each container is optimized for its specific role
5. **Better Monitoring**: Individual health checks and logging per service
6. **Deployment Flexibility**: Deploy and update services independently


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

## Entrypoint Scripts Automation

Each container includes intelligent entrypoint scripts that handle deployment automatically:

### App Container (`docker-entrypoint.sh`)
- **Database Connection**: Waits for database to be ready
- **Automatic Migrations**: Runs `php artisan migrate --force` on startup
- **Performance Optimization**: Caches config, routes, and views

### Queue & Scheduler Containers
- **Smart Waiting**: Initial 60-second wait for app container deployment
- **Migration Verification**: Checks that migrations are complete before starting
- **Exponential Backoff**: Intelligent retry logic with increasing wait times
- **Graceful Failure**: Exits cleanly if database isn't ready after max attempts

## Optimization Features

- **Multi-stage builds** to reduce image size
- **Production OPcache configuration**
- **Optimized Composer autoloading**
- **Proper file permissions** and security
- **Alpine Linux** for minimal footprint
- **Automated deployment scripts** for zero-touch deployments

## Support

For issues and questions, please open an issue in the repository.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
