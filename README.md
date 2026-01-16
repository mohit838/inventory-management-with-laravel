# SaaS Inventory Management System

A production-ready, multi-tenant Inventory Management Microservice built with Laravel 11. Designed for scalability, security, and developer experience.

## 🚀 Overview

This Application serves as the core Inventory module in a microservices architecture. It handles:
- **Product Management**: Hierarchical Categories, Subcategories, and Products.
- **SaaS Storage**: Secure, tenant-isolated file storage using MinIO (S3-compatible).
- **Authentication**: Stateless JWT authentication with Role-Based Access Control (RBAC).
- **Performance**: High-performance caching (Redis) and standardized API responses.

## 🏗 Architecture

The system follows a loose-coupled architecture, allowing independent scaling and seamless integration with a Gateway.

```mermaid
graph TD
    User[Client / Frontend] -->|HTTPS| Gateway[API Gateway / Kong]
    Gateway -->|Forward Auth Headers| API[Inventory Service (Laravel)]
    
    subgraph "Inventory Service Infrastructure"
        API -->|Read/Write| DB[(MySQL Database)]
        API -->|Cache| Redis[(Redis Cache)]
        API -->|Secure Uploads| MinIO[(MinIO Object Storage)]
        MinIO -->|Private Bucket| TenantStorage[Tenant Data]
    end

    subgraph "Storage Security"
        API -- Signed URL Generation --> TenantStorage
        TenantStorage -.->|Direct Temporary Access| User
    end
```

## ✨ Key Features

- **Standardized API**: All endpoints return wrapped data `{"data": ...}` using Laravel's `JsonResource` for consistent consumption.
- **SaaS Storage Strategy** (`Phase 3.5`): 
  - **Isolation**: Files are stored in `tenants/{user_id}/...`.
  - **Security**: Public access is blocked. Images are served via **Signed URLs** (valid for 60m).
  - **Optimization**: Products images are auto-compressed to WebP.
- **Advanced Querying**: Search and Pagination built into the Repository layer.
- **Developer Experience**:
  - `make` commands for common tasks.
  - API Documentation via Scramble (`/docs/api`).
  - Comprehensive Feature Tests (`ApiFlowTest`).

## 🛠 Tech Stack

- **Framework**: Laravel 11 (PHP 8.2+)
- **Database**: MySQL 8.0
- **Cache**: Redis 7.0
- **Storage**: MinIO (S3 Compatible)
- **Docs**: Scramble / OpenApi

## 📦 Installation & Setup

### Docker (Recommended)

This project includes fully configured Docker environments for both Development and Production.

#### Development
The development environment includes **MySQL**, **Redis**, **MinIO**, and an automated **MinIO Setup** container that configures a strict "No Listing" policy for the dev user.

1. **Clone & Config**:
   ```bash
   cp .env.example .env
   # Ensure APP_ENV=local, DB_HOST=db, REDIS_HOST=redis, MINIO_ENDPOINT=http://minio:9000
   ```

2. **Start Services**:
   ```bash
   docker-compose -f docker-compose.dev.yml up -d --build
   ```

3. **Install Dependencies**:
   ```bash
   docker-compose -f docker-compose.dev.yml exec app composer install
   docker-compose -f docker-compose.dev.yml exec app php artisan migrate:fresh --seed
   ```

#### Production
The production setup runs ONLY the Application container. You must provide external Database, Redis, and MinIO/S3 credentials via environment variables.

1. **Build**:
   ```bash
   docker build -t inventory-service .
   ```

2. **Run**:
   ```bash
   docker run -d -p 8000:8000 --env-file .env.prod inventory-service
   ```
   *Or use `docker-compose.yml` for orchestration.*

## 🔒 Security

- **Trust Policy**: The service operates behind a secure Gateway. It trusts `X-User-Id` and `X-Role` headers (simulated via JWT in local dev).
- **Private Storage**: No public S3 buckets. All assets are private by default.

## 🧪 Testing

Run the full feature test suite:

```bash
php artisan test
```

## 📝 License

Proprietary / Private.
