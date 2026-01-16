# Inventory management With Laravel

Quick start (API-focused):

Requirements: PHP 8.2+, Composer, MySQL/SQLite.

Install dependencies and set up env:

```bash
composer install
cp .env.example .env
php artisan key:generate
# configure DB in .env
php artisan migrate
```

Run dev server:

```bash
php artisan serve
```

Auth (JWT):

-   POST /api/v1/register {name,email,password,password_confirmation}
-   POST /api/v1/login {email,password}
    -   returns `access_token` (Bearer JWT, 15m) and `refresh_token` (plain token)
-   POST /api/v1/refresh {refresh_token} -> returns new access_token
-   POST /api/v1/logout {refresh_token} -> revokes refresh token

Example protected endpoints (use `Authorization: Bearer <access_token>`):

-   GET /api/v1/categories
-   POST /api/v1/categories {name,slug,description,active}
-   GET /api/v1/products
-   POST /api/v1/products {category_id,subcategory_id?,name,sku,price,quantity}

Notes:

-   No hard deletes: use `active` boolean and `toggle-active` endpoints.
-   API returns JSON and is designed to be loosely coupled to any frontend.
-   Refresh tokens are stored hashed server-side; refresh and logout use the plain refresh token returned at login.

Security:

-   Access tokens are short-lived (15 minutes). Refresh tokens are long-lived (default 30 days) and stored hashed.
-   Ensure `APP_KEY`/`APP_URL` are correct and that HTTPS is used in production.
