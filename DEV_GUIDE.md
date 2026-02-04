# Laravel Developer Guide (Laravel 11/12+ Streamlined) 🚀

This guide targets the **modern Laravel (11/12+) project structure**. In this streamlined skeleton, high-level configuration lives in `bootstrap/app.php`, and the legacy "Kernel classes" approach is no longer the default.

---

## 0. Fundamental Concepts

### Patterns in Laravel
Laravel uses ~15–25 common design patterns. It’s not about the number, but about **which pattern solves which problem**. Service providers, container bindings, and drivers are the backbone of the framework.

---

## 1. Core Patterns & Problem Solving

| Pattern | Problem | Solution/Scenario |
| :--- | :--- | :--- |
| **MVC** | Mixing logic, UI, and HTTP concerns. | `OrderController` validates, `CheckoutService` processes, `JSON Resource` presents. |
| **Service Container / DI** | Tight coupling makes code hard to test. | Inject dependencies (`PaymentGateway`) instead of `new StripeGateway()`. |
| **Service Providers** | "Where do I register my bindings?" | Central bootstrapping via `bootstrap/providers.php`. |
| **Facades** | Passing objects everywhere is noisy. | "Static-looking" accessors like `Cache::remember()` or `Log::info()`. |
| **Active Record (Eloquent)** | Repetitive raw SQL strings. | Models representing rows: `User::query()->with('roles')->paginate()`. |
| **Builder Pattern** | Dynamic queries becoming ugly. | Fluent chaining: `Product::query()->whereActive()->search($query)->get()`. |
| **Middleware Chain** | Cross-cutting concerns cluttering controllers. | Layers like `EnsureUserIsSubscribed` running before/after requests. |
| **Observer vs Events** | Model side-effects or decoupled reactions. | **Observer**: tie to model lifecycle. **Events**: business workflows across modules. |
| **Strategy (Drivers)** | Different backends for same API. | Switch implementations (Redis vs Database) via `.env` config. |
| **Command Pattern** | Repeating admin tasks manually. | Build CLI automation with Artisan: `php artisan reports:daily`. |

---

## 2. Bootstrapping: `bootstrap/app.php`

### What & Why
In Laravel 11+, this is the **code-first configuration hub**. It replaces the old scattered config/kernel files with one high-level place to customize routing, middleware, and exceptions.

### Typical Shape
```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Alias, register, or append middleware here
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Logic for custom exception rendering/reporting
    })
    ->create();
```

---

## 3. Key Features: What / Why / When

### **A) Traits**
- **What**: Reusable "copy-paste" inheritance for methods.
- **Why**: Share behavior without deep inheritance chains.
- **When**: Small reusable logic like `Blameable` (audit stamping) or `Slugable`.

### **B) Events & Listeners**
- **What**: Dispatch a message (`event(new PaymentPaid)`) and let listeners react.
- **Why**: Decouple workflows; enables async work (queued listeners).
- **When**: Multi-step reactions like `UserRegistered` → Send Email, Notify Admin, Init Profile.

### **C) Observers**
- **What**: Classes for Eloquent lifecycle hooks (created, updated, deleted).
- **Why**: Keep models/controllers clean.
- **When**: Strictly tied to model side-effects (e.g., generating `order_no` on creation).

### **D) Caching**
- **What**: Storing expensive computation in RAM (Redis/Memcached).
- **Why**: Performance. Reduce database load and speed up responses.
- **When**: Frequently read, infrequently changed data (e.g., homepage featured products).

### **E) Sessions vs Cookies**
- **Sessions**: Server-side storage (with cookie ID). Best for **sensitive data**, Auth state, Carts.
- **Cookies**: Client-side storage. Best for **lightweight preferences** like theme or language.
- **Verdict**: Use **Sessions** as the default for user-specific states.

---

## 4. Dependency Injection (DI) & Services

### The DI Advantage
Instead of `new StripeGateway()`, type-hint `PaymentGateway $gateway`. The container resolves it. This allows easy swapping of implementations and painless testing/mocking.

### "Service" vs "Utils"
- **Service Class**: Handles business operations (Order processing, API integration). Can have dependencies.
- **Utils Class**: Only for **pure, stateless, deterministic** formatting (e.g., `Money::format()`). Should have no side effects.

---

## 5. High-Impact Concepts You Shouldn't Miss

1.  **Form Requests**: Move validation logic out of controllers.
2.  **Policies / Gates**: Centralize authorization (e.g., `UpdateProjectPolicy`).
3.  **Jobs & Queues**: Move slow tasks (emails, video processing) off the request cycle.
4.  **Notifications**: Multi-channel messaging (Mail, Slack, Database).
5.  **Logging**: Structured logs with correlation IDs for easier debugging.
6.  **API Resources**: Control the shape of your JSON responses consistently.
7.  **Testing**: Use Feature tests for endpoints and Unit tests for business logic.

---

## 6. Practical Cheat Sheet

| Need | Best Tool | Example |
| :--- | :--- | :--- |
| Filter/Block Request | Middleware | `auth`, `role:admin`, `throttle` |
| Model Side-effect | Observer | Audit logging on model update |
| Async Workflow | Events + Queues | Send notification after order complete |
| Performance Boost | Cache | Cache product categories for 24h |
| State/Auth | Session | Shopping cart, Logged-in state |
| Preferences | Cookie | User language preference |
| Swap Logic | DI + Contracts | Swap Stripe for another payment provider |

---

## 7. Essential Artisan Commands 🛠️

| Command | Usage |
| :--- | :--- |
| `php artisan make:model -mfs` | Create Model + Migration + Factory + Seeder. |
| `php artisan migrate` | Sync DB schema. |
| `php artisan tinker` | Interactive shell to test code. |
| `php artisan route:list` | Verify all registered routes. |
| `php artisan cache:clear` | Fix "stuck" config/cache issues. |
| `php artisan event:generate`| Scaffold listeners from EventServiceProvider. |

---

---

## 8. External Package Integrations 📦

### **A) Spatie Laravel-Permission (RBAC)**
**What**: Robust Roles & Permissions management.
**Installation**:
```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```
**Usage**:
1.  **Setup Model**: Add `use HasRoles;` trait to your `User` model.
2.  **Assigning**: `$user->assignRole('admin');` or `$user->givePermissionTo('edit articles');`.
3.  **Middleware**: Protect routes in `bootstrap/app.php` or controller constructors:
    ```php
    // In router
    ->middleware('role:admin');
    ```

### **B) L5-Swagger (API Documentation)**
**What**: OpenApi/Swagger documentation for your APIs.
**Installation**:
```bash
composer require "darkaonline/l5-swagger"
php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider"
```
**Usage**:
1.  **Generate**: `php artisan l5-swagger:generate`.
2.  **Annotations**: Add doc-blocks to your Controllers:
    ```php
    /**
     * @OA\Get(
     *     path="/api/products",
     *     @OA\Response(response="200", description="Display a listing of products.")
     * )
     */
    ```
3.  **Access**: Visit `/api/documentation` to view the UI.

### **C) JWT Auth (Stateless Auth)**
**What**: JSON Web Token authentication for REST APIs.
**Installation** (`tymon/jwt-auth`):
```bash
composer require tymon/jwt-auth
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
php artisan jwt:secret
```
**Usage**:
1.  **User Model**: Implement `JWTSubject` interface and its methods (`getJWTIdentifier`, `getJWTCustomClaims`).
2.  **Auth Config**: Change the API driver to `jwt` in `config/auth.php`.
3.  **Controller**: Use `auth('api')->attempt($credentials)` to generate a token.
4.  **Guarding**: Protect routes with `auth:api` middleware.

---

## Appendix: Onboarding Check-list
1.  **`composer setup`**: Custom script in `composer.json` for full environment init.
2.  **`php artisan migrate --seed`**: Set up database with dummy data.
3.  **`npm install && npm run dev`**: Launch Vite for frontend assets.
