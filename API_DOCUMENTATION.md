# Inventory Management API Documentation

This document provides a comprehensive guide to the API endpoints available for the inventory management system.

## Base URL

`https://api.example.com/api/v1` (Replace with your actual API host)

## Authentication

Most endpoints require a JWT token in the `Authorization` header:
`Authorization: Bearer <your_access_token>`

---

## 1. Authentication Endpoints

### Register

`POST /register`

- **Request Body:**
  ```json
  {
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }
  ```
- **Response (201 Created):**
  ```json
  {
    "data": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "user",
      "permissions": ["categories.view", "products.view"],
      "active": true,
      "created_at": "2023-10-27 10:00:00"
    }
  }
  ```

### Login

`POST /login`

- **Request Body:**
  ```json
  {
    "email": "john@example.com",
    "password": "password123"
  }
  ```
- **Response (200 OK):**
  ```json
  {
    "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "token_type": "bearer",
    "expires_in": 900,
    "refresh_token": "xyz123...",
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

### Refresh Token

`POST /refresh`

- **Request Body:**
  ```json
  {
    "refresh_token": "xyz123..."
  }
  ```

### Logout

`POST /logout`

- **Request Body (Optional):**
  ```json
  {
    "refresh_token": "xyz123..."
  }
  ```

---

## 2. Categories

### List Categories

`GET /categories`

- **Query Params:** `page`, `per_page`, `search` (optional)
- **Response:**
  ```json
  {
    "data": [
      {
        "id": 1,
        "name": "Electronics",
        "slug": "electronics",
        "description": "Gadgets and devices",
        "active": true,
        "created_at": "2023-10-27 10:00:00",
        "updated_at": "2023-10-27 10:00:00"
      }
    ],
    "links": { "first": "...", "last": "...", "prev": null, "next": null },
    "meta": {
      "current_page": 1,
      "from": 1,
      "last_page": 1,
      "path": "...",
      "per_page": 15,
      "to": 1,
      "total": 1
    }
  }
  ```

### Create Category

`POST /categories`

- **Request Body:**
  ```json
  {
    "name": "Furniture",
    "description": "Tables, chairs, etc."
  }
  ```

### Toggle Active Status

`POST /categories/{id}/toggle-active`

- **Response:** (Returns the updated category resource)

### Dropdown (Optimized)

`GET /categories/dropdown`

- **Response:**
  ```json
  {
    "data": [
      { "id": 1, "name": "Electronics" },
      { "id": 2, "name": "Furniture" }
    ]
  }
  ```

---

## 3. Subcategories

Endpoints are identical in structure to Categories.

- `GET /subcategories`
- `POST /subcategories`
- `GET /subcategories/{id}`
- `PUT /subcategories/{id}`
- `DELETE /subcategories/{id}`
- `POST /subcategories/{id}/toggle-active`
- `GET /subcategories/dropdown`

---

## 4. Products

### List Products

`GET /products`

- **Query Params:** `page`, `per_page`, `search`
- **Response:**
  ```json
  {
    "data": [
      {
        "id": 1,
        "category_id": 1,
        "subcategory_id": null,
        "name": "iPhone 15",
        "sku": "IPH15-BLK",
        "description": "Apple iPhone 15",
        "image_url": "https://s3.example.com/products/iphone15.jpg?token=...",
        "price": 999.99,
        "quantity": 50,
        "active": true,
        "category": { "id": 1, "name": "Electronics" }
      }
    ]
  }
  ```

### Create Product

`POST /products` (Use `multipart/form-data` if uploading an image)

- **Fields:** `category_id`, `subcategory_id` (optional), `name`, `sku`, `price`, `quantity`, `description`, `image` (file)

### Dropdown (Optimized for Sales)

`GET /products/dropdown`

- **Response:**
  ```json
  {
    "data": [{ "id": 1, "name": "iPhone 15", "sku": "IPH15-BLK" }]
  }
  ```

---

## 5. Orders

### List Orders

`GET /orders`

- **Query Params:** `page`, `per_page`
- **Response:**
  ```json
  {
    "data": [
      {
        "id": 101,
        "customer_name": "Jane Smith",
        "total_amount": 1999.98,
        "status": "pending",
        "created_at": "2023-10-27 11:00:00"
      }
    ],
    "links": { "first": "...", "last": "...", "prev": null, "next": null },
    "meta": { "current_page": 1, "total": 1 }
  }
  ```

### Get Order Details

`GET /orders/{id}`

- **Response:**
  ```json
  {
    "data": {
      "id": 101,
      "customer_name": "Jane Smith",
      "total_amount": 1999.98,
      "status": "pending",
      "items": [
        {
          "product_id": 1,
          "product_name": "iPhone 15",
          "quantity": 2,
          "unit_price": 999.99,
          "total_price": 1999.98
        }
      ]
    }
  }
  ```

### Create Order

`POST /orders`

- **Request Body:**
  ```json
  {
    "customer_name": "Jane Smith",
    "customer_email": "jane@example.com",
    "payment_method": "online",
    "items": [{ "product_id": 1, "quantity": 2 }]
  }
  ```
- **Response (201 Created):**
  ```json
  {
    "data": {
      "id": 101,
      "customer_name": "Jane Smith",
      "total_amount": 1999.98,
      "status": "pending",
      "payment_method": "online",
      "payment_status": "pending",
      "items": [
        {
          "product_id": 1,
          "product_name": "iPhone 15",
          "quantity": 2,
          "unit_price": 999.99,
          "total_price": 1999.98
        }
      ],
      "created_at": "2023-10-27 11:00:00"
    }
  }
  ```

### Get Invoice

`GET /orders/{id}/invoice`

- **Response:**
  ```json
  {
    "data": {
      "id": "INV-101",
      "owner": "Jane Smith",
      "total": 1999.98,
      "url": "https://s3.example.com/invoices/INV-101.pdf"
    }
  }
  ```

---

## 6. Dashboard & Analytics

### Summary

`GET /dashboard/summary`

- **Response:**
  {
    "status": "success",
    "data": {
      "total_products": 4821,
      "total_categories": 12,
      "low_stock_count": 5,
      "total_value": 92450,
      "top_categories": [
        { "name": "Electronics", "total_sold": 1200 },
        { "name": "Clothing", "total_sold": 950 }
      ]
    }
  }

### Sales Chart

`GET /dashboard/chart`

- **Query Param:** `period=monthly`
- **Response:**
  {
    "status": "success",
    "data": {
      "labels": ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
      "data": [1200, 1500, 1100, 1800, 2000, 1700]
    }
  }

---

## 7. Miscellaneous

### User Settings

- `GET /settings`: Get all settings for current user.
- `POST /settings`: Update settings. Body: `{"key": "value"}`

### Public Image Upload

`POST /uploads/public`

- **Fields:** `image` (file)
- **Response:** `{"data": {"url": "..."}}`

### Notifications

- `GET /notifications`: List user notifications.
- `PATCH /notifications/{id}/read`: Mark one as read.
- `PATCH /notifications/read-all`: Mark all as read.

### Audit Logs

`GET /audit-logs`

- **Response:** Paginated list of system actions (e.g., "Product Created").
