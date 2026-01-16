````md
# Inventory Management with Laravel

# SaaS Inventory Management System

A production-ready, **multi-tenant Inventory Management Microservice** built with **Laravel 11 (PHP 8.2+)**. Designed for scalability, security, and developer experience in a microservices architecture.

---

## 🚀 Overview

This application serves as the **core Inventory module** in a microservices architecture. It provides:

-   **Product Management**: Hierarchical **Categories**, **Subcategories**, and **Products**
-   **SaaS Storage**: Secure, tenant-isolated file storage using **MinIO (S3-compatible)**
-   **Authentication**: Stateless **JWT** authentication with **RBAC**
-   **Performance**: High-performance caching (**Redis**) and standardized API responses
-   **Developer Experience**: Make commands, API docs, and comprehensive tests

---

## ✅ Requirements

-   PHP **8.2+**
-   Composer
-   MySQL 8.0 (or SQLite for local testing)
-   (Optional) Redis 7.0
-   (Optional) MinIO / S3-compatible storage

---

## ⚡ Quick Start (Local / Non-Docker)

> These are **terminal commands**. Run them inside your Laravel project root (where `artisan` and `composer.json` exist).

```bash
composer install
cp .env.example .env
php artisan key:generate
# configure DB credentials in .env
php artisan migrate
php artisan serve
```
````

---

## 🏗 Architecture

Loose-coupled design for independent scaling and seamless Gateway integration.

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

---

## ✨ Key Features

### Standardized API

All endpoints return wrapped data like:

```json
{ "data": ... }
```

Implemented using Laravel **JsonResource** for consistent response structures.

### SaaS Storage Strategy (Phase 3.5)

-   **Isolation**: Files stored under: `tenants/{user_id}/...`
-   **Security**: Public access blocked; serve via **Signed URLs** (valid for **60 minutes**)
-   **Optimization**: Product images auto-compressed to **WebP**

### Advanced Querying

Search + pagination are built into the **Repository layer**.

### Developer Experience

-   Helpful `make` commands (optional)
-   API Documentation via **Scramble**: `/docs/api`
-   Comprehensive Feature Tests: `ApiFlowTest`

---

## 🛠 Tech Stack

-   **Framework**: Laravel 11 (PHP 8.2+)
-   **Database**: MySQL 8.0
-   **Cache**: Redis 7.0
-   **Storage**: MinIO (S3 Compatible)
-   **Docs**: Scramble / OpenAPI

---

## 🔐 Authentication (JWT)

### Auth Endpoints

-   `POST /api/v1/register`
    Body: `{ name, email, password, password_confirmation }`

-   `POST /api/v1/login`
    Body: `{ email, password }`
    Returns:

    -   `access_token` (Bearer JWT, **15m**)
    -   `refresh_token` (plain token)

-   `POST /api/v1/refresh`
    Body: `{ refresh_token }`
    Returns: new `access_token`

-   `POST /api/v1/logout`
    Body: `{ refresh_token }`
    Revokes refresh token

### Protected Requests

Add this header:

```
Authorization: Bearer <access_token>
```

---

## 📦 Core Inventory Endpoints (Examples)

-   `GET  /api/v1/categories`

-   `POST /api/v1/categories`
    Body: `{ name, slug, description, active }`

-   `GET  /api/v1/products`

-   `POST /api/v1/products`
    Body: `{ category_id, subcategory_id?, name, sku, price, quantity }`

---

## 🧱 Data & Deletion Strategy

-   **No hard deletes**
    Use `active` boolean and `toggle-active` endpoints.

---

## 📦 Installation & Setup (Docker Recommended)

This project includes fully configured Docker environments for **Development** and **Production**.

---

### ✅ Development (Docker)

Includes:

-   MySQL
-   Redis
-   MinIO
-   MinIO setup container with strict **"No Listing"** policy for dev user

#### 1) Config

```bash
cp .env.example .env
# Ensure:
# APP_ENV=local
# DB_HOST=db
# REDIS_HOST=redis
# MINIO_ENDPOINT=http://minio:9000
```

#### 2) Start Services

```bash
docker-compose -f docker-compose.dev.yml up -d --build
```

#### 3) Install Dependencies + Migrate

```bash
docker-compose -f docker-compose.dev.yml exec app composer install
docker-compose -f docker-compose.dev.yml exec app php artisan key:generate
docker-compose -f docker-compose.dev.yml exec app php artisan migrate:fresh --seed
```

---

### ✅ Production

Production runs **ONLY the app container**.
You must provide **external** Database, Redis, and MinIO/S3 credentials via env vars.

#### 1) Build

```bash
docker build -t inventory-service .
```

#### 2) Run

```bash
docker run -d -p 8000:8000 --env-file .env.prod inventory-service
```

Or use `docker-compose.yml` for orchestration.

---

## 🔒 Security

-   **Trust Policy**: Service runs behind a secure Gateway and trusts:

    -   `X-User-Id`
    -   `X-Role`

    > (Simulated via JWT in local dev)

-   **Private Storage by Default**

    -   No public S3/MinIO buckets
    -   Assets served only via **Signed URLs**

-   **Refresh Token Safety**

    -   Refresh tokens stored **hashed server-side**
    -   Refresh/logout use the **plain** refresh token returned at login

**Production Notes**

-   Ensure `APP_KEY` and `APP_URL` are correct
-   Use **HTTPS** in production

---

## 🧪 Testing

Run the full feature test suite:

```bash
php artisan test
```

---

## 📚 API Documentation

If Scramble is enabled:

-   Open: `/docs/api`

---

## 📝 License

Proprietary / Private.
