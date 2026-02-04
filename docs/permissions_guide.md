# Developer Guide: Granular Permission System

This project uses a granular CRUD-level permission system built with `spatie/laravel-permission`.

## Core Philosophy
Avoid broad role checks (e.g., `@role('admin')`). Instead, always check for specific **Actions** (e.g., `@can('edit_users')`).

## Naming Convention
Permissions are formatted as `{action}_{module}`.

- **Actions**: `view`, `create`, `edit`, `delete`, `manage`
- **Modules**: `users`, `invitations`, `settings`, `inventory`, `permissions`

### Example Permissions
- `view_users`: Can see the user list.
- `delete_users`: Can deactivate/delete users.
- `manage_permissions`: Can access and edit the Security Matrix.

---

## 1. Adding a New Module
When creating a new module (e.g., `products`), follow these steps:

### A. Update the Seeder
Open `database/seeders/RolesAndPermissionsSeeder.php` and add your module to the `$modules` array:

```php
$modules = [
    // ...
    'products' => ['view', 'create', 'edit', 'delete'],
];
```

### B. Map to Roles
Decide which roles get these permissions by default:

```php
'admin' => [
    // ...
    'view_products', 'create_products', 
],
```

### C. Run Seeder
```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

---

## 2. Protecting Routes
Use the `permission` middleware in `web.php`:

```php
Route::get('/products', [ProductController::class, 'index'])
    ->middleware('permission:view_products');
```

---

## 3. Protecting Controllers
Use `Gate::authorize` or `$user->can()`:

```php
public function destroy(Product $product) {
    Gate::authorize('delete_products');
    // ...
}
```

---

## 4. UI Visibility
Wrap buttons or navigation links in `@can` blocks:

```html
@can('create_products')
    <button>Add Product</button>
@endcan
```

---

## Security Matrix
Superadmins can override these defaults dynamically via the **Security Matrix** in the sidebar. This page allows toggling any permission for any role (except Superadmin, which always has full access).
