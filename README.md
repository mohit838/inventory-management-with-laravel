# Enterprise Inventory Management System (EIMS)

A premium, multi-tenant inventory management system built with Laravel 12, featuring a robust security matrix, enterprise observability, and a state-of-the-art UI/UX.

---

## 🌟 The "Why": Strategic Scenario

Imagine a global logistics provider that needs to provide unique inventory dashboards to hundreds of different partner organizations (Tenants). They required a system where:
1. **Isolated Data**: Organization A can never see Organization B's stock.
2. **Flexible Security**: An "Admin" in one organization might need different permissions than an "Admin" in another.
3. **Infrastructure Oversight**: The parent company (Superadmin) needs to see everything—system health, slow API calls, and global user distribution—without interfering with daily operations.

** EIMS was built to solve this exact scenario.** It transitions from a simple CRUD app to a high-scale, observable, and multi-tenant infrastructure platform.

---

## 🚀 Key Features (For Everyone)

- **Multi-Tenant Isolation**: Built-in logic ensuring every organization’s data is strictly partitioned and secure.
- **Dynamic Security Matrix**: A visual dashboard to assign permissions (Create, Delete, View) to different roles instantly.
- **2FA (Two-Factor Authentication)**: Banking-grade security with Google Authenticator support to protect sensitive inventory data.
- **Invitation-Only Growth**: Secure the platform by allowing new users to join only via tracked email invitations.
- **Premium Dark UI**: A high-fidelity, responsive sidebar and dashboard designed for professional long-term use.
- **System Health Monitor**: Real-time stats on CPU, Memory, and Database health for the platform owners.

---

## 🛠 Technical Architecture (For Developers)

### 1. Robust Permissions (RBAC)
We utilize **Spatie Laravel-Permission** combined with a custom **Security Matrix UI**. 
- **Superadmin Bypass**: Implemented via `Gate::before` in `AppServiceProvider` to ensure core infrastructure maintains access even during permission re-organizations.
- **Granular Guards**: Every route and UI component is guarded by specific capabilities (e.g., `view_diagnostics`, `manage_settings`).

### 2. Multi-Tenancy
- **Scoped Eloquent Models**: Every model is linked to a `tenant_id`. Global scopes ensure that data is auto-filtered based on the authenticated user's organization.

### 3. Observability & Performance
- **Performance Middleware**: An active interceptor that calculates request duration.
- **Redis Integration**: "Performance Culprits" (slow requests > 500ms) are pushed to a Redis list for high-speed, non-blocking telemetry.
- **Fault Manifest**: A secure log-parsing engine that surfaces the last 5 critical backend errors directly in the browser for Superadmins.

### 4. Code Standards
- **Centralized Literals**: `AppConstant.php` stores all roles, permissions, and system thresholds, preventing "magic strings" and making the code highly maintainable.
- **Dockerized Environment**: Optimized `Dockerfile` (PHP 8.2-FPM + Nginx) and `docker-compose.yml` for instant, consistent deployment with Redis and MySQL.

---

## 🏗 Setup & Installation

### Option 1: Docker (Recommended)
```bash
# Start the entire infrastructure (App, MySQL, Redis)
docker-compose up -d

# Install dependencies and setup database
docker exec -it inv-app composer install
docker exec -it inv-app php artisan migrate --seed
```

### Option 2: Local Development
1. **Configure Environment**: Copy `.env.example` to `.env` and set your DB/Redis credentials.
2. **Install**: `composer install` & `npm install`.
3. **Database**: `php artisan migrate --seed`.
4. **Link Storage**: `php artisan storage:link`.

---

## 📊 Standard Roles & Permissions
- **Superadmin**: Global infrastructure owner. Can see System Health and manage all tenants.
- **Owner**: The head of a Tenant organization. Can manage their organization's users and settings.
- **Admin**: Operational lead within a tenant. Handles inventory and invitations.
- **Employee**: Base staff. Can view inventory and manage their own profile settings.

---

## 💻 Tech Stack
- **Framework**: Laravel 12.x
- **Frontend**: Blade, Alpine.js, HTMX (for dynamic updates)
- **Styling**: Vanilla CSS (Premium Tailored Designs)
- **Database**: MySQL 8.0
- **Cache/Telemetry**: Redis
- **Security**: Google 2FA, Spatie RBAC

---
*Built with precision for high-performance enterprise needs.*
