# Project Overview & Standards

This document outlines the architectural patterns and coding standards used in the Inventory Management API. Follow these guidelines to ensure consistency and maintainability.

---

## 🏗️ Architecture: Layered Pattern
We follow a strict **Controller -> Service -> Repository** flow to separate concerns.

### 1. Controllers (Request Handling)
Controllers should be lean. Their only job is to receive a request, call a service/repository, and return a resource.
- **Rules**: Use **FormRequests** for validation and **API Resources** for responses.
- **Example**:
```php
public function store(ProductStoreRequest $request)
{
    $item = $this->repo->create($request->validated());
    return (new ProductResource($item))->response()->setStatusCode(201);
}
```

### 2. Services (Business Logic)
Services handle complex logic that doesn't belong in a model or repository (e.g., calculations, external APIs, multi-table transactions).
- **Example**: `OrderService` handles stock deduction and invoice generation.

### 3. Repositories (Data Access)
All database interactions go through repositories. We use `EloquentBaseRepository` for common operations (CRUD, Pagination, Search).
- **Example**:
```php
$this->repo->paginate($perPage, ['*'], ['category']);
```

---

## 🔐 Security & RBAC
- **Authentication**: Stateless JWT-based authentication.
- **Roles**: `superadmin` > `admin` > `user`.
- **Enforcement**: Roles and permissions are checked via middleware in `routes/api.php` and Gates.

---

## 📡 API Best Practices

### Validation (FormRequests)
Never validate inside a controller. Create a dedicated request:
`php artisan make:request MyRequest`

### Responses (API Resources)
Never return `response()->json($model)`. Use Resources:
`php artisan make:resource MyResource`

---

## ⚡ Performance: Caching
Use the `CacheServiceInterface` for a professional, decoupled caching layer.
- **Usage**:
```php
return $this->cache->remember("key", 300, function() {
    return MyModel::all();
}, ['tag_name']);
```

---

## 📂 Media Management
Images are stored in **MinIO** (S3 compatible). Use `MinioService` for uploads and `ProductResource` for generating temporary URLs.

---

## 📖 Documentation
We use **Swagger (L5-Swagger)**.
- **Command**: `php artisan l5-swagger:generate`
- **Annotations**: Always add `#[OA\...]` attributes to your controller methods.

> [!TIP]
> Always run `php artisan migrate:fresh --seed` after major changes to ensure your local environment matches the latest schema and permissions.
