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
- **Intelligent entrypoint scripts** for reliable database connectivity and deployment
- **Advanced database readiness checks** with fallback mechanisms
- **Organized script structure** in dedicated `scripts/` folder

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
   - Health Check: http://localhost:8000/api/health

## Testing the Setup

After starting the containers, you can test various endpoints:

```bash
# Test basic health check
curl http://localhost:8000/api/health/simple

# Test detailed health check (includes database, cache, app status)
curl http://localhost:8000/api/health

# Test queue functionality
curl http://localhost:8000/api/queue-job
```

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
- **Health Check**: HTTP endpoints `/api/health` and `/api/health/simple`
- **Entrypoint**: `scripts/docker-entrypoint.sh`

### Queue Worker Container
- **Base**: PHP 8.2 CLI Alpine
- **Features**: Optimized for background processing
- **Command**: `php artisan queue:work --verbose --sleep=3 --tries=3`
- **Health Check**: Process monitoring
- **Entrypoint**: `scripts/docker-entrypoint-queue.sh`

### Scheduler Container
- **Base**: PHP 8.2 CLI Alpine
- **Features**: Laravel task scheduling
- **Command**: `php artisan schedule:work --verbose`
- **Health Check**: Process monitoring
- **Entrypoint**: `scripts/docker-entrypoint-scheduler.sh`

## Monitoring and Health Checks

All containers include comprehensive health checks:

- **App**: HTTP health endpoints (`/api/health` for detailed status, `/api/health/simple` for basic check)
- **Queue**: Process monitoring for queue workers
- **Scheduler**: Process monitoring for scheduler

### Available Health Endpoints

```bash
# Basic health check - returns simple OK status
GET /api/health/simple
# Response: {"status":"ok","timestamp":"2025-07-12T..."}

# Detailed health check - includes database, cache, and app status
GET /api/health  
# Response: {"status":"ok","timestamp":"...","checks":{"database":"ok","cache":"ok","app":"ok"}}
```

## Entrypoint Scripts Automation

Each container includes intelligent entrypoint scripts located in the `scripts/` folder that handle deployment automatically:

### App Container (`scripts/docker-entrypoint.sh`)
- **Advanced Database Connection**: Multi-stage database readiness checks with PHP PDO validation
- **Fallback Script**: Uses `scripts/wait-for-db.sh` for advanced connectivity testing
- **Automatic Migrations**: Runs `php artisan migrate --force` on startup
- **Performance Optimization**: Caches config, routes, and views
- **Graceful Error Handling**: Comprehensive logging and error recovery

### Queue & Scheduler Containers (`scripts/docker-entrypoint-queue.sh`, `scripts/docker-entrypoint-scheduler.sh`)
- **Smart Waiting**: Initial 60-second wait for app container deployment
- **Migration Verification**: Checks that migrations are complete before starting
- **Database Connectivity**: Validates database connection before proceeding
- **Exponential Backoff**: Intelligent retry logic with increasing wait times
- **Graceful Failure**: Exits cleanly if database isn't ready after max attempts

### Database Readiness Script (`scripts/wait-for-db.sh`)
- **Advanced Connection Testing**: Tests database connectivity with multiple methods
- **Timeout Handling**: Configurable timeout with detailed logging
- **Troubleshooting Support**: Provides detailed connection diagnostics

## Optimization Features

- **Multi-stage builds** to reduce image size
- **Production OPcache configuration**
- **Optimized Composer autoloading**
- **Proper file permissions** and security
- **Alpine Linux** for minimal footprint
- **Automated deployment scripts** for zero-touch deployments
- **Organized script structure** in dedicated `scripts/` folder
- **Advanced database readiness checks** with fallback mechanisms
- **Comprehensive logging** for debugging and monitoring

## Project Structure

```
├── app/                           # Laravel application code
├── scripts/                       # Docker entrypoint and utility scripts
│   ├── docker-entrypoint.sh       # Main app container entrypoint
│   ├── docker-entrypoint-queue.sh # Queue worker entrypoint
│   ├── docker-entrypoint-scheduler.sh # Scheduler entrypoint
│   └── wait-for-db.sh             # Advanced database connectivity script
├── Dockerfile                     # Main app container
├── Dockerfile.queue               # Queue worker container
├── Dockerfile.cron                # Scheduler container
├── docker-compose.yml             # Local development setup
├── TROUBLESHOOTING.md             # Diagnostic and recovery guide
└── README.md                      # This file
```

## Support

For issues and questions, please open an issue in the repository.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
