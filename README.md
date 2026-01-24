# Inventory Management System (Laravel 11)

A clean, loosely coupled, and production-ready Inventory Management API built with **Laravel 11** and **PHP 8.3**.

---

## 🚀 Features

-   **📦 Inventory Core**: Hierarchical **Categories**, **Subcategories**, and **Products**.
-   **🛒 Order Management**: Transactional order processing with automated stock deduction.
-   **🕵️ Automated Audit Logs**: Model Observers listen for database events to log every change automatically.
-   **🖼️ Media Management**: Seamless integration with **MinIO (S3-compatible)** for secure image storage.
-   **🔐 Security**: Stateless **JWT Authentication** with **RBAC** (Role-Based Access Control).
-   **⚡ Performance**: Built-in **Redis Caching** and eager-loaded Eloquent relationships.
-   **📖 API Documentation**: Auto-generated **OpenAPI/Swagger** docs (separated from controllers for cleanliness).

---

## 🏗️ Architecture: Clean & Loosely Coupled

This project follows a professional **Controller -> Service -> Repository Interface** architecture.

### 🧩 Loose Coupling (Dependency Inversion)
Unlike standard Laravel tutorials, this project depends on **Interfaces** rather than concrete classes.
-   **Controllers** type-hint an `Interface` (e.g., `ProductRepositoryInterface`).
-   **Laravel Service Container** binds the interface to an `Eloquent implementation` via `RepositoryServiceProvider`.
-   **Benefit**: You can swap the database or repository logic without touching the controllers.

### 📂 Directory Structure
-   `app/Interfaces`: Contains all Repository and Service contracts.
-   `app/Repositories`: Contains the Eloquent-specific implementations.
-   `app/Services`: Handles complex business logic (e.g., `OrderService`).
-   `app/Http/Resources`: Standardizes API output format.
-   `app/Observers`: Handles background tasks like audit logging.

---

## 🐳 Docker & DevOps

The project uses a high-performance **Nginx + PHP-FPM** multi-container setup.

### 🌐 Networking & External Services
As requested, this setup is designed to connect to services (MySQL, Redis, MinIO) already present on your host or VPS.
-   **`host.docker.internal`**: Used within the container to reach the host machine.
-   **`extra_hosts`**: Configured in `docker-compose.yml` to bridge the container-to-host gap.

### 🛠️ Local Deployment
1.  **Configure `.env`**:
    ```bash
    cp .env.example .env
    # Ensure DB_HOST=host.docker.internal
    # Ensure REDIS_HOST=host.docker.internal
    ```
2.  **Run with Docker Compose**:
    ```bash
    docker compose up -d --build
    ```
3.  **Access**: The app is available at `http://localhost`.

---

## 🚀 CI/CD with Jenkins

The included `Jenkinsfile` provides a sophisticated deployment pipeline:
1.  **Build**: Creates a Docker image for the PHP-FPM app.
2.  **Backup**: Renames current containers for zero-downtime rollback.
3.  **Deploy**: Uses `docker compose` to spin up the new App and Nginx sidecars.
4.  **Health Check**: Automatically verifies the new deployment before finishing.
5.  **Rollback**: Automatically restores the previous version if the health check fails.

---

## 📚 API documentation

View the interactive documentation at:
**`http://your-domain/api/documentation`**

To regenerate docs:
```bash
php artisan l5-swagger:generate
```

---

## ⚙️ Development Standards
-   **Validation**: Use `FormRequest`.
-   **Responses**: Use `JsonResource`.
-   **Business Logic**: Keep controllers thin; use `Services`.
-   **Data Access**: Always use `Repositories` via their `Interfaces`.
