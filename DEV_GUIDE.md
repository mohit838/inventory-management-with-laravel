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

| Role | Email | Password | Permissions |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@test.com` | `password` | Full Access (Create, View, Edit, Delete) |
| **User** | `user@test.com` | `password` | Balanced Access (View Categories/Products, Create Orders) |

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

### Development Server
- **Run Locally**:
  ```bash
  php artisan serve
  ```

---

## 📂 Project Architecture Notes
- **JWT Authentication**: Managed by `JwtMiddleware`. Tokens are valid for 15 minutes.
- **RBAC**: Implemented using `spatie/laravel-permission`. Permissions are checked via middleware in `routes/api.php`.
- **Media**: Image uploads are handled by `MinioService` (S3 compatible).
