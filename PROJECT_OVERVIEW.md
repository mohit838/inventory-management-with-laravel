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
    return new ProductResource($item);
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

## 🚀 Recent Optimizations (January 2026)

We have recently refactored the codebase to move from "Standard CRUD" to a more "Scalable & Maintainable" architecture:

### 1. Automated Audit Logging (Model Observers)
- **Why**: Manual logging in controllers is error-prone and clutters the code.
- **Pattern**: `Model Observers` (`ProductObserver`, etc.). 
- **Benefit**: Audit logs are generated automatically on DB events, ensuring 100% coverage without manual calls.

### 2. Lean Controllers & Extracted Documentation
- **Why**: Swagger annotations were making controllers unreadable.
- **Pattern**: `Docs` classes (`ProductDoc`) to house OpenAPI attributes.
- **Benefit**: Controllers only contain action logic, making them easier to test and maintain.

### 3. API Resources (Data Transformation)
- **Why**: Manual array formatting in controllers is inconsistent.
- **Pattern**: `JsonResource` (`ProductResource`).
- **Benefit**: Decouples DB schema from API output. Handles complex logic like temporary S3 URLs cleanly.

---

## ⚖️ Scalability & Over-engineering Analysis

### Why this is NOT Over-engineering
- **DRY Repositories**: Instead of repeating code, the `EloquentBaseRepository` provides common functionality (search, paginate, toggleActive). Child repositories are usually <10 lines.
- **Focused Classes**: Each class has one job (SRP). Observers handle logging, Resources handle formatting, Controllers handle routing. This actually *reduces* cognitive load during debugging.

### Scalability Performance
| Aspect | Status | Why? |
|--------|---------|------|
| **Write Throughput** | ✅ Scalable | Observers are fast. For huge volumes, they can easily be converted to asynchronous Jobs. |
| **Read Performance** | ✅ Scalable | API Resources work seamlessly with Eloquent’s eager loading, and Redis caching is built-in. |
| **Developer Velocity** | ✅ Highly Scalable | New developers can understand the flow easily: Route -> Controller -> Repo -> Resource. |

### Pros & Cons
**Pros:**
- **Testability**: Every layer can be mocked and tested in isolation.
- **Consistency**: All API endpoints follow the same response structure.
- **Maintainability**: Changing the database field doesn't break the API (Resource mapping).

**Cons:**
- **Initial Setup**: Slightly more files to create initially (Resource, Observer, Doc).
- **Indirection**: Requires following the flow across multiple files.

---

## 📂 Media Management
Images are stored in **MinIO** (S3 compatible). Use `MinioService` for uploads and `ProductResource` for generating temporary URLs.

---

## 📖 Documentation
We use **Swagger (L5-Swagger)**.
- **Command**: `php artisan l5-swagger:generate`
- **Annotations**: Extracted to `app/Http/Controllers/Api/Docs` to keep controllers clean.

> [!TIP]
> Always run `php artisan migrate:fresh --seed` after major changes to ensure your local environment matches the latest schema and permissions.
