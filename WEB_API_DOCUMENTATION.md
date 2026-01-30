# Inventia Web & API Documentation

## Table of Contents

1. [Overview](#overview)
2. [Authentication Architecture](#authentication-architecture)
3. [API Endpoints](#api-endpoints)
4. [Web Routes](#web-routes)
5. [Error Handling](#error-handling)
6. [Security](#security)
7. [Best Practices](#best-practices)

---

## Overview

**Inventia** is a comprehensive inventory management system built with Laravel, offering dual authentication mechanisms:

- **RESTful API** with JWT authentication for mobile apps, SPAs, and third-party integrations
- **Web Dashboard** with session-based authentication for admin CMS interface

Both systems share the same database and permission model, ensuring consistent authorization across all interfaces.

### Base URLs

- **API**: `https://your-domain.com/api/v1`
- **Web Dashboard**: `https://your-domain.com`

---

## Authentication Architecture

### API Authentication (JWT - Stateless)

**Purpose**: For mobile applications, single-page applications (SPAs), and third-party integrations.

**Flow**:
1. Client sends credentials to `/api/v1/login`
2. Server returns JWT access token (15 min expiry) and refresh token
3. Client includes access token in `Authorization: Bearer <token>` header
4. When access token expires, client uses refresh token to get new access token
5. Client sends logout request with refresh token to invalidate it

**Characteristics**:
- Stateless (no server-side session)
- Token-based authentication
- Suitable for distributed systems
- CORS-enabled for cross-origin requests

### Web Authentication (Session - Stateful)

**Purpose**: For the admin dashboard and CMS interface.

**Flow**:
1. User submits login form to `/login`
2. Server creates session and sets secure HTTP-only cookie
3. Session cookie is automatically sent with each request
4. CSRF token protects against cross-site request forgery
5. Logout destroys server-side session

**Characteristics**:
- Stateful (server maintains session)
- Cookie-based authentication
- CSRF protection enabled
- Suitable for traditional web applications

### Authentication Comparison

| Feature | API (JWT) | Web (Session) |
|---------|-----------|---------------|
| **State** | Stateless | Stateful |
| **Storage** | Client-side token | Server-side session |
| **Expiry** | 15 minutes (access), 30 days (refresh) | Configurable (default: 120 min) |
| **CSRF Protection** | Not needed | Required |
| **Use Case** | Mobile, SPA, Third-party | Admin dashboard |
| **Routes** | `/api/v1/*` | `/dashboard`, `/categories`, etc. |

---

## API Endpoints

### Authentication

#### Register User

```http
POST /api/v1/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "SecurePass123!",
  "password_confirmation": "SecurePass123!"
}
```

**Response (201 Created)**:
```json
{
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "user",
    "permissions": ["categories.view", "products.view"],
    "active": true,
    "created_at": "2026-01-30T10:00:00.000000Z"
  }
}
```

#### Login

```http
POST /api/v1/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "SecurePass123!"
}
```

**Response (200 OK)**:
```json
{
  "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "token_type": "bearer",
  "expires_in": 900,
  "refresh_token": "xyz123abc456...",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "superadmin",
    "permissions": ["*"],
    "active": true
  }
}
```

#### Refresh Token

```http
POST /api/v1/refresh
Content-Type: application/json

{
  "refresh_token": "xyz123abc456..."
}
```

**Response (200 OK)**:
```json
{
  "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "token_type": "bearer",
  "expires_in": 900
}
```

#### Logout

```http
POST /api/v1/logout
Authorization: Bearer <access_token>
Content-Type: application/json

{
  "refresh_token": "xyz123abc456..."
}
```

**Response (200 OK)**:
```json
{
  "message": "Logged out"
}
```

---

### Categories

#### List Categories

```http
GET /api/v1/categories?page=1&per_page=15&search=electronics&status=active
Authorization: Bearer <access_token>
```

**Query Parameters**:
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 15, max: 100)
- `search` (optional): Search term for name/description
- `status` (optional): `active` (default) or `archived`
- `include_archived` (optional): `true` to include both active and archived

**Response (200 OK)**:
```json
{
  "data": [
    {
      "id": 1,
      "name": "Electronics",
      "slug": "electronics",
      "description": "Electronic devices and gadgets",
      "active": true,
      "created_at": "2026-01-30T10:00:00.000000Z",
      "updated_at": "2026-01-30T10:00:00.000000Z"
    }
  ],
  "links": {
    "first": "https://api.example.com/api/v1/categories?page=1",
    "last": "https://api.example.com/api/v1/categories?page=5",
    "prev": null,
    "next": "https://api.example.com/api/v1/categories?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "path": "https://api.example.com/api/v1/categories",
    "per_page": 15,
    "to": 15,
    "total": 73
  }
}
```

#### Create Category

```http
POST /api/v1/categories
Authorization: Bearer <access_token>
Content-Type: application/json

{
  "name": "Furniture",
  "description": "Tables, chairs, and home furniture"
}
```

**Response (201 Created)**:
```json
{
  "data": {
    "id": 2,
    "name": "Furniture",
    "slug": "furniture",
    "description": "Tables, chairs, and home furniture",
    "active": true,
    "created_at": "2026-01-30T10:30:00.000000Z",
    "updated_at": "2026-01-30T10:30:00.000000Z"
  }
}
```

#### Get Category

```http
GET /api/v1/categories/{id}
Authorization: Bearer <access_token>
```

#### Update Category

```http
PUT /api/v1/categories/{id}
Authorization: Bearer <access_token>
Content-Type: application/json

{
  "name": "Home Furniture",
  "description": "Updated description"
}
```

#### Delete Category

```http
DELETE /api/v1/categories/{id}
Authorization: Bearer <access_token>
```

**Response (204 No Content)**

#### Toggle Active Status

```http
POST /api/v1/categories/{id}/toggle-active
Authorization: Bearer <access_token>
```

**Response (200 OK)**: Returns updated category resource

#### Get Categories Dropdown

```http
GET /api/v1/categories/dropdown
Authorization: Bearer <access_token>
```

**Response (200 OK)**:
```json
{
  "data": [
    { "id": 1, "name": "Electronics" },
    { "id": 2, "name": "Furniture" }
  ]
}
```

---

### Subcategories

Subcategory endpoints follow the same structure as categories:

- `GET /api/v1/subcategories`
- `POST /api/v1/subcategories`
- `GET /api/v1/subcategories/{id}`
- `PUT /api/v1/subcategories/{id}`
- `DELETE /api/v1/subcategories/{id}`
- `POST /api/v1/subcategories/{id}/toggle-active`
- `GET /api/v1/subcategories/dropdown`

---

### Products

#### List Products

```http
GET /api/v1/products?page=1&per_page=20&search=iphone&status=active
Authorization: Bearer <access_token>
```

**Response (200 OK)**:
```json
{
  "data": [
    {
      "id": 1,
      "category_id": 1,
      "subcategory_id": null,
      "name": "iPhone 15 Pro",
      "sku": "IPH15-PRO-BLK",
      "description": "Apple iPhone 15 Pro - Black Titanium",
      "image_url": "https://s3.example.com/products/iphone15.jpg?token=...",
      "price": 999.99,
      "quantity": 50,
      "active": true,
      "category": {
        "id": 1,
        "name": "Electronics"
      },
      "subcategory": null,
      "created_at": "2026-01-30T10:00:00.000000Z",
      "updated_at": "2026-01-30T10:00:00.000000Z"
    }
  ],
  "links": { "..." },
  "meta": { "..." }
}
```

#### Create Product

```http
POST /api/v1/products
Authorization: Bearer <access_token>
Content-Type: multipart/form-data

category_id: 1
subcategory_id: 3
name: iPhone 15 Pro
sku: IPH15-PRO-BLK
price: 999.99
quantity: 50
description: Apple iPhone 15 Pro - Black Titanium
image: <file>
```

**Response (201 Created)**: Returns created product resource

#### Update Product

```http
PUT /api/v1/products/{id}
Authorization: Bearer <access_token>
Content-Type: application/json

{
  "name": "iPhone 15 Pro Max",
  "price": 1199.99,
  "quantity": 30
}
```

#### Delete Product

```http
DELETE /api/v1/products/{id}
Authorization: Bearer <access_token>
```

#### Toggle Active Status

```http
POST /api/v1/products/{id}/toggle-active
Authorization: Bearer <access_token>
```

#### Get Products Dropdown

```http
GET /api/v1/products/dropdown
Authorization: Bearer <access_token>
```

**Response (200 OK)**:
```json
{
  "data": [
    { "id": 1, "name": "iPhone 15 Pro", "sku": "IPH15-PRO-BLK" }
  ]
}
```

---

### Orders

#### List Orders

```http
GET /api/v1/orders?page=1&status=active
Authorization: Bearer <access_token>
```

**Response (200 OK)**:
```json
{
  "data": [
    {
      "id": 101,
      "customer_name": "Jane Smith",
      "customer_email": "jane@example.com",
      "total_amount": 1999.98,
      "status": "pending",
      "payment_method": "online",
      "payment_status": "pending",
      "created_at": "2026-01-30T11:00:00.000000Z",
      "updated_at": "2026-01-30T11:00:00.000000Z"
    }
  ],
  "links": { "..." },
  "meta": { "..." }
}
```

#### Create Order

```http
POST /api/v1/orders
Authorization: Bearer <access_token>
Content-Type: application/json

{
  "customer_name": "Jane Smith",
  "customer_email": "jane@example.com",
  "payment_method": "online",
  "items": [
    {
      "product_id": 1,
      "quantity": 2
    },
    {
      "product_id": 5,
      "quantity": 1
    }
  ]
}
```

**Response (201 Created)**:
```json
{
  "data": {
    "id": 101,
    "customer_name": "Jane Smith",
    "customer_email": "jane@example.com",
    "total_amount": 1999.98,
    "status": "pending",
    "payment_method": "online",
    "payment_status": "pending",
    "items": [
      {
        "id": 1,
        "product_id": 1,
        "product_name": "iPhone 15 Pro",
        "quantity": 2,
        "unit_price": 999.99,
        "total_price": 1999.98
      }
    ],
    "created_at": "2026-01-30T11:00:00.000000Z",
    "updated_at": "2026-01-30T11:00:00.000000Z"
  }
}
```

#### Get Order Details

```http
GET /api/v1/orders/{id}
Authorization: Bearer <access_token>
```

**Response (200 OK)**: Returns detailed order with items

#### Get Order Invoice

```http
GET /api/v1/orders/{id}/invoice
Authorization: Bearer <access_token>
```

**Response (200 OK)**:
```json
{
  "data": {
    "id": "INV-101",
    "owner": "Jane Smith",
    "total": 1999.98,
    "url": "https://s3.example.com/invoices/INV-101.pdf?token=..."
  }
}
```

#### Toggle Order Active Status

```http
POST /api/v1/orders/{id}/toggle-active
Authorization: Bearer <access_token>
```

---

### Dashboard & Analytics

#### Get Dashboard Summary

```http
GET /api/v1/dashboard/summary
Authorization: Bearer <access_token>
```

**Response (200 OK)**:
```json
{
  "data": {
    "total_products": 150,
    "total_categories": 12,
    "total_value": 45000.50,
    "total_orders": 89,
    "pending_orders": 5,
    "low_stock_alerts": 3,
    "out_of_stock_count": 2,
    "top_categories": [
      { "name": "Electronics", "value": 45, "percentage": 30.5 },
      { "name": "Clothing", "value": 25, "percentage": 15.2 }
    ]
  }
}
```

#### Get Sales Chart Data

```http
GET /api/v1/dashboard/chart?period=monthly
Authorization: Bearer <access_token>
```

**Query Parameters**:
- `period`: `monthly` (default), `weekly`, or `yearly`

**Response (200 OK)**:
```json
{
  "data": {
    "labels": ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
    "revenue": [1200.50, 1500.00, 1100.00, 1800.00, 2000.00, 1700.00, 0, 0, 0, 0, 0, 0],
    "stock_variance": [-45, -50, -30, -60, -70, -55, 0, 0, 0, 0, 0, 0],
    "orders": [12, 15, 10, 18, 20, 17, 0, 0, 0, 0, 0, 0]
  }
}
```

---

### User Settings

#### Get User Settings

```http
GET /api/v1/settings
Authorization: Bearer <access_token>
```

**Response (200 OK)**:
```json
{
  "data": {
    "theme": "dark",
    "notifications_enabled": true,
    "language": "en"
  }
}
```

#### Update User Settings

```http
POST /api/v1/settings
Authorization: Bearer <access_token>
Content-Type: application/json

{
  "theme": "light",
  "notifications_enabled": false
}
```

---

### File Uploads

#### Upload Public Image

```http
POST /api/v1/uploads/public
Authorization: Bearer <access_token>
Content-Type: multipart/form-data

image: <file>
```

**Response (200 OK)**:
```json
{
  "data": {
    "url": "https://s3.example.com/uploads/abc123.jpg?token=..."
  }
}
```

---

### Notifications

#### List Notifications

```http
GET /api/v1/notifications
Authorization: Bearer <access_token>
```

**Response (200 OK)**:
```json
{
  "data": [
    {
      "id": "uuid-123",
      "type": "App\\Notifications\\LowStockAlert",
      "data": {
        "message": "Product 'iPhone 15 Pro' is low on stock (5 remaining)"
      },
      "read_at": null,
      "created_at": "2026-01-30T12:00:00.000000Z"
    }
  ]
}
```

#### Mark Notification as Read

```http
PATCH /api/v1/notifications/{id}/read
Authorization: Bearer <access_token>
```

#### Mark All Notifications as Read

```http
PATCH /api/v1/notifications/read-all
Authorization: Bearer <access_token>
```

---

### Audit Logs

#### List Audit Logs

```http
GET /api/v1/audit-logs?page=1&per_page=20
Authorization: Bearer <access_token>
```

**Response (200 OK)**:
```json
{
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "user_name": "John Doe",
      "action": "created",
      "model": "Product",
      "model_id": 15,
      "changes": {
        "name": "New Product",
        "price": 99.99
      },
      "ip_address": "192.168.1.1",
      "user_agent": "Mozilla/5.0...",
      "created_at": "2026-01-30T13:00:00.000000Z"
    }
  ],
  "links": { "..." },
  "meta": { "..." }
}
```

---

### Admin Logs (Superadmin Only)

#### View System Logs

```http
GET /api/v1/admin/logs
Authorization: Bearer <access_token>
```

**Requires**: `superadmin` role

#### Clear System Logs

```http
DELETE /api/v1/admin/logs
Authorization: Bearer <access_token>
```

**Requires**: `superadmin` role

---

## Web Routes

### Public Routes

| Method | Route | Description |
|--------|-------|-------------|
| GET | `/` | Login page (redirects to `/dashboard` if authenticated) |
| POST | `/login` | Process login form |
| GET | `/register` | Registration page |
| POST | `/register` | Process registration form |
| GET | `/forgot-password` | Password reset request page |
| POST | `/forgot-password` | Send password reset email |
| GET | `/reset-password/{token}` | Password reset form |
| POST | `/reset-password` | Process password reset |

### Protected Routes (Requires Web Authentication)

| Method | Route | Description | Permission |
|--------|-------|-------------|------------|
| GET | `/dashboard` | Main dashboard | `dashboard.view` |
| POST | `/logout` | Logout user | - |
| GET | `/categories` | List categories | `categories.view` |
| GET | `/categories/create` | Create category form | `categories.create` |
| POST | `/categories` | Store new category | `categories.create` |
| GET | `/categories/{id}/edit` | Edit category form | `categories.edit` |
| PUT | `/categories/{id}` | Update category | `categories.edit` |
| DELETE | `/categories/{id}` | Delete category | `categories.delete` |
| GET | `/products` | List products | `products.view` |
| GET | `/products/create` | Create product form | `products.create` |
| POST | `/products` | Store new product | `products.create` |
| GET | `/products/{id}/edit` | Edit product form | `products.edit` |
| PUT | `/products/{id}` | Update product | `products.edit` |
| DELETE | `/products/{id}` | Delete product | `products.delete` |
| GET | `/orders` | List orders | `orders.view` |
| GET | `/orders/{id}` | View order details | `orders.view` |
| GET | `/settings` | User settings page | `settings.manage` |
| POST | `/settings` | Update user settings | `settings.manage` |

---

## Error Handling

### API Error Responses

All API errors follow a consistent JSON format:

```json
{
  "message": "Human-readable error message",
  "errors": {
    "field_name": [
      "Specific validation error"
    ]
  }
}
```

### HTTP Status Codes

| Code | Meaning | Usage |
|------|---------|-------|
| 200 | OK | Successful GET, PUT, PATCH requests |
| 201 | Created | Successful POST request creating a resource |
| 204 | No Content | Successful DELETE request |
| 400 | Bad Request | Malformed request syntax |
| 401 | Unauthorized | Missing or invalid authentication |
| 403 | Forbidden | Authenticated but lacks permission |
| 404 | Not Found | Resource doesn't exist |
| 422 | Unprocessable Entity | Validation errors |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Internal Server Error | Server-side error |

### Validation Errors (422)

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": [
      "The email field is required."
    ],
    "password": [
      "The password must be at least 8 characters."
    ]
  }
}
```

### Authentication Errors (401)

```json
{
  "message": "Invalid credentials"
}
```

### Permission Errors (403)

```json
{
  "message": "You do not have permission to perform this action."
}
```

---

## Security

### API Security

1. **JWT Token Security**
   - Access tokens expire after 15 minutes
   - Refresh tokens expire after 30 days
   - Tokens are signed with HS256 algorithm
   - Refresh tokens are hashed (SHA-256) before storage

2. **HTTPS Required**
   - All API requests must use HTTPS in production
   - Tokens should never be transmitted over HTTP

3. **Rate Limiting**
   - API endpoints are rate-limited to prevent abuse
   - Default: 60 requests per minute per IP
   - Authenticated users: 100 requests per minute

4. **CORS Configuration**
   - Configure allowed origins in `.env`:
     ```
     CORS_ALLOWED_ORIGINS=https://app.example.com,https://mobile.example.com
     ```

### Web Security

1. **Session Security**
   - Sessions use secure, HTTP-only cookies
   - `SameSite=Lax` prevents CSRF attacks
   - Sessions expire after 120 minutes of inactivity

2. **CSRF Protection**
   - All POST, PUT, PATCH, DELETE requests require CSRF token
   - Token is automatically included via `@csrf` Blade directive

3. **Password Security**
   - Passwords are hashed using bcrypt
   - Minimum 8 characters required
   - Password confirmation required on registration

4. **XSS Protection**
   - Blade templates automatically escape output
   - Use `{!! !!}` only for trusted content

### Permission System

Both API and web routes use the same permission middleware:

```php
// API Route
Route::middleware(['jwt.auth', 'permission:products.create'])
    ->post('/api/v1/products', [ProductController::class, 'store']);

// Web Route
Route::middleware(['auth:web', 'permission:products.create'])
    ->post('/products', [WebProductController::class, 'store']);
```

**Available Permissions**:
- `categories.view`, `categories.create`, `categories.edit`, `categories.delete`
- `products.view`, `products.create`, `products.edit`, `products.delete`
- `orders.view`, `orders.create`, `orders.edit`, `orders.delete`
- `dashboard.view`
- `settings.manage`
- `*` (superadmin - all permissions)

---

## Best Practices

### API Clients

1. **Token Management**
   ```javascript
   // Store tokens securely
   localStorage.setItem('access_token', response.access_token);
   localStorage.setItem('refresh_token', response.refresh_token);
   
   // Include token in requests
   axios.defaults.headers.common['Authorization'] = `Bearer ${access_token}`;
   
   // Refresh token when access token expires
   axios.interceptors.response.use(
     response => response,
     async error => {
       if (error.response.status === 401) {
         const newToken = await refreshAccessToken();
         error.config.headers['Authorization'] = `Bearer ${newToken}`;
         return axios.request(error.config);
       }
       return Promise.reject(error);
     }
   );
   ```

2. **Error Handling**
   ```javascript
   try {
     const response = await api.createProduct(data);
     console.log('Product created:', response.data);
   } catch (error) {
     if (error.response.status === 422) {
       // Validation errors
       console.error('Validation errors:', error.response.data.errors);
     } else if (error.response.status === 403) {
       // Permission denied
       console.error('Permission denied');
     }
   }
   ```

3. **Pagination**
   ```javascript
   // Fetch all pages
   let allProducts = [];
   let currentPage = 1;
   let lastPage = 1;
   
   do {
     const response = await api.getProducts({ page: currentPage });
     allProducts = [...allProducts, ...response.data];
     lastPage = response.meta.last_page;
     currentPage++;
   } while (currentPage <= lastPage);
   ```

### Web Dashboard

1. **CSRF Token**
   ```blade
   <form method="POST" action="{{ route('products.store') }}">
       @csrf
       <!-- form fields -->
   </form>
   ```

2. **Flash Messages**
   ```php
   // Controller
   return redirect()->route('products.index')
       ->with('success', 'Product created successfully');
   ```
   
   ```blade
   <!-- View -->
   @if(session('success'))
       <div class="alert alert-success">
           {{ session('success') }}
       </div>
   @endif
   ```

3. **Form Validation**
   ```blade
   <input type="text" name="name" value="{{ old('name') }}" class="@error('name') is-invalid @enderror">
   @error('name')
       <span class="invalid-feedback">{{ $message }}</span>
   @enderror
   ```

### Performance Optimization

1. **Use Dropdown Endpoints**
   - Use `/categories/dropdown` instead of `/categories` for select inputs
   - Reduces payload size significantly

2. **Pagination**
   - Always paginate large datasets
   - Adjust `per_page` based on use case (default: 15)

3. **Caching**
   - Dashboard summary data is cached for 5 minutes
   - Clear cache after data modifications if needed

4. **Eager Loading**
   - Product listings include category relationships
   - Reduces N+1 query problems

---

## Changelog

### Version 1.0.0 (2026-01-30)

- Initial release with dual authentication system
- RESTful API with JWT authentication
- Web dashboard with session authentication
- Complete CRUD operations for categories, products, and orders
- Dashboard analytics and reporting
- Permission-based access control
- Audit logging
- File upload support

---

## Support

For issues, questions, or feature requests, please contact:
- **Email**: support@inventia.example.com
- **Documentation**: https://docs.inventia.example.com
- **GitHub**: https://github.com/your-org/inventia

---

**Last Updated**: January 30, 2026  
**Version**: 1.0.0
