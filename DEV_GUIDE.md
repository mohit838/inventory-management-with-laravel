@@ -1,47 +1,111 @@
# Inventory management With Laravel
# SaaS Inventory Management System

Quick start (API-focused):
A production-ready, multi-tenant Inventory Management Microservice built with Laravel 11. Designed for scalability, security, and developer experience.

Requirements: PHP 8.2+, Composer, MySQL/SQLite.
## 🚀 Overview

Install dependencies and set up env:
This Application serves as the core Inventory module in a microservices architecture. It handles:
- **Product Management**: Hierarchical Categories, Subcategories, and Products.
- **SaaS Storage**: Secure, tenant-isolated file storage using MinIO (S3-compatible).
- **Authentication**: Stateless JWT authentication with Role-Based Access Control (RBAC).
- **Performance**: High-performance caching (Redis) and standardized API responses.

```bash
composer install
cp .env.example .env
php artisan key:generate
# configure DB in .env
php artisan migrate
```