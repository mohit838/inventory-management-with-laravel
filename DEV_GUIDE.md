# Developer Guide - Inventory Management API

This guide provides essential commands and information for developing, testing, and managing the Inventory Management API.

---

## 🔐 API Documentation (Swagger)

The API is fully documented using Swagger (OpenAPI 3.0).

- **Documentation URL**: `http://localhost:8000/api/documentation`
- **Regenerate Documentation**: Run this whenever you change controller attributes.
    ```bash
    php artisan l5-swagger:generate
    ```

### How to Authenticate in Swagger:

1.  **Login**: Use the `POST /api/v1/login` endpoint to get an `access_token`.
2.  **Authorize**: Click the **"Authorize"** button at the top of the Swagger UI.
3.  **Token**: Paste the token string (e.g., `eyJhbGci...`) into the value field and click **Authorize**.
4.  **Lock Icons**: Protected routes will show a closed lock icon, indicating your token is active.

---

## 🧪 Test User Credentials

The database seeder creates two default users for testing different RBAC permissions.

| Role           | Email                 | Password   | Permissions                                                 |
| :------------- | :-------------------- | :--------- | :---------------------------------------------------------- |
| **Superadmin** | `superadmin@test.com` | `password` | Total Control (All permissions + Users)                     |
| **Admin**      | `admin@test.com`      | `password` | High Level Access (All except Users Management)             |
| **User**       | `user@test.com`       | `password` | Restricted Access (View Categories/Products, Create Orders) |

---

## 🛠️ Essential Commands

### Database Management

- **Fresh Install (Migrations + Seeds)**:
    ```bash
    php artisan migrate:fresh --seed
    ```
- **Reset Permissions only**:
    ```bash
    php artisan db:seed --class=PermissionSeeder
    ```

### Cache & Optimization

- **Clear All Caches (Recommended after changes)**:
    ```bash
    php artisan optimize:clear
    ```
- **Clear Specific Caches**:
    ```bash
    php artisan config:clear
    # AND
    php artisan route:clear
    ```

### Docker Management

- **Start Services (Development)**:
    ```bash
    docker compose -f docker-compose.dev.yml up -d --build
    ```
- **Execute Commands in Container**:
    ```bash
    docker compose exec app php artisan <command>
    ```
- **Monitor Logs**:
    ```bash
    docker logs -f inventory_app
    ```

### 🔍 Debugging with Tinker

Tinker is essential for testing services and repository logic directly.

- **Check Service Status (e.g., MinIO)**:

    ```php
    // Verify MinioService instantiation and public link generation
    $minio = app(App\Services\MinioService::class);
    echo Storage::disk('minio')->url('test.png');
    ```

- **Query Database Models**:

    ```php
    // Check superadmin user
    App\Models\User::role('superadmin')->first();
    ```

- **Clear Cache Manually**:

    ```php
    Cache::flush();
    ```

- **Run Commands via Tinker**:
    ```bash
    php artisan tinker --execute="echo App\Models\Product::count();"
    ```

---

## 📂 Project Architecture Notes

- **JWT Authentication**: Managed by `JwtMiddleware`. Tokens are valid for 15 minutes.
- **RBAC**: Implemented using `spatie/laravel-permission`. Permissions are checked via middleware in `routes/api.php`.
- **Media**: Image uploads are handled by `MinioService` (S3 compatible).

## Project tree

- `Linux / macOS`

```bash
  tree -I "node_modules|dist|.git|.vscode|logs|tmp|coverage|vendor" > project-tree.txt
```
