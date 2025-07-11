# API Documentation
## POS System REST API

### Version: 1.0
### Base URL: `https://api.yourpos.com`
### Date: 11 Juli 2025

---

## Table of Contents
1. [Authentication](#authentication)
2. [Error Handling](#error-handling)
3. [Rate Limiting](#rate-limiting)
4. [Data Synchronization](#data-synchronization)
5. [Product Management](#product-management)
6. [Transaction Management](#transaction-management)
7. [Customer Management](#customer-management)
8. [Store Management](#store-management)
9. [Reports & Analytics](#reports--analytics)
10. [System Administration](#system-administration)

---

## Authentication

### Overview
The API uses JWT (JSON Web Tokens) for authentication. All authenticated requests must include the `Authorization` header with a valid Bearer token.

### Headers
```
Authorization: Bearer {access_token}
Content-Type: application/json
Accept: application/json
X-Store-ID: {store_id} (required for store-specific operations)
```

### Login
**Endpoint:** `POST /api/auth/login`

**Request Body:**
```json
{
    "username": "string", // required
    "password": "string", // required
    "store_id": "string", // optional, for store-specific login
    "remember_me": boolean // optional, default: false
}
```

**Response:** `200 OK`
```json
{
    "success": true,
    "data": {
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "token_type": "Bearer",
        "expires_in": 3600,
        "user": {
            "id": 1,
            "username": "admin",
            "full_name": "System Administrator",
            "email": "admin@yourpos.com",
            "role": "admin",
            "store_id": 1,
            "store": {
                "id": 1,
                "store_code": "ST001",
                "store_name": "Main Store",
                "address": "123 Main Street"
            },
            "permissions": [
                "view_dashboard",
                "manage_products",
                "process_transactions",
                "view_reports"
            ],
            "last_login": "2025-07-11T10:30:00Z"
        }
    },
    "message": "Login successful"
}
```

**Error Response:** `401 Unauthorized`
```json
{
    "success": false,
    "error": {
        "code": "INVALID_CREDENTIALS",
        "message": "Invalid username or password",
        "details": null
    },
    "timestamp": "2025-07-11T10:30:00Z"
}
```

### Refresh Token
**Endpoint:** `POST /api/auth/refresh`

**Headers:**
```
Authorization: Bearer {access_token}
```

**Response:** `200 OK`
```json
{
    "success": true,
    "data": {
        "access_token": "new_jwt_token",
        "token_type": "Bearer",
        "expires_in": 3600
    }
}
```

### Logout
**Endpoint:** `POST /api/auth/logout`

**Headers:**
```
Authorization: Bearer {access_token}
```

**Response:** `200 OK`
```json
{
    "success": true,
    "message": "Successfully logged out"
}
```

---

## Error Handling

### Error Response Format
All API errors follow a consistent format:

```json
{
    "success": false,
    "error": {
        "code": "ERROR_CODE",
        "message": "Human readable error message",
        "details": {
            "field": ["Specific validation error"]
        }
    },
    "timestamp": "2025-07-11T10:30:00Z",
    "request_id": "uuid-string"
}
```

### Common Error Codes

| HTTP Status | Error Code | Description |
|-------------|------------|-------------|
| 400 | VALIDATION_ERROR | Request validation failed |
| 401 | UNAUTHORIZED | Authentication required |
| 403 | FORBIDDEN | Access denied |
| 404 | NOT_FOUND | Resource not found |
| 409 | CONFLICT | Resource conflict (duplicate data) |
| 422 | UNPROCESSABLE_ENTITY | Invalid entity state |
| 429 | RATE_LIMIT_EXCEEDED | Too many requests |
| 500 | INTERNAL_ERROR | Server error |
| 503 | SERVICE_UNAVAILABLE | Service temporarily unavailable |

### Validation Errors
**Response:** `400 Bad Request`
```json
{
    "success": false,
    "error": {
        "code": "VALIDATION_ERROR",
        "message": "The given data was invalid",
        "details": {
            "email": ["The email field is required"],
            "price": ["The price must be greater than 0"]
        }
    },
    "timestamp": "2025-07-11T10:30:00Z"
}
```

---

## Rate Limiting

### Rate Limits
- **Authentication endpoints:** 5 requests per minute
- **General API endpoints:** 1000 requests per hour
- **Sync endpoints:** 100 requests per hour
- **Report endpoints:** 50 requests per hour

### Rate Limit Headers
```
X-RateLimit-Limit: 1000
X-RateLimit-Remaining: 999
X-RateLimit-Reset: 1625731200
```

### Rate Limit Exceeded Response
**Response:** `429 Too Many Requests`
```json
{
    "success": false,
    "error": {
        "code": "RATE_LIMIT_EXCEEDED",
        "message": "Too many requests. Please try again later.",
        "details": {
            "retry_after": 3600
        }
    },
    "timestamp": "2025-07-11T10:30:00Z"
}
```

---

## Data Synchronization

### Download Store Data
**Endpoint:** `GET /api/sync/download/{store_id}`

**Parameters:**
- `last_sync` (query, optional): ISO 8601 timestamp of last sync
- `tables` (query, optional): Comma-separated list of tables to sync

**Example:** `GET /api/sync/download/1?last_sync=2025-07-11T10:00:00Z&tables=products,categories`

**Response:** `200 OK`
```json
{
    "success": true,
    "data": {
        "products": [
            {
                "id": 1,
                "barcode": "1234567890123",
                "sku": "PROD001",
                "name": "Sample Product",
                "description": "Product description",
                "category_id": 1,
                "unit": "pcs",
                "cost_price": 15000.00,
                "selling_price": 25000.00,
                "stock_quantity": 100,
                "min_stock": 10,
                "max_stock": 500,
                "is_active": true,
                "image_url": "https://cdn.yourpos.com/products/1.jpg",
                "created_at": "2025-07-11T10:00:00Z",
                "updated_at": "2025-07-11T10:30:00Z"
            }
        ],
        "categories": [
            {
                "id": 1,
                "name": "Electronics",
                "description": "Electronic products",
                "parent_id": null,
                "is_active": true,
                "created_at": "2025-07-11T10:00:00Z",
                "updated_at": "2025-07-11T10:00:00Z"
            }
        ],
        "store_settings": {
            "tax_rate": 10.00,
            "currency": "IDR",
            "timezone": "Asia/Jakarta",
            "receipt_template": "default"
        }
    },
    "meta": {
        "sync_timestamp": "2025-07-11T10:30:00Z",
        "total_records": 150,
        "checksum": "sha256_hash"
    }
}
```

### Upload Store Data
**Endpoint:** `POST /api/sync/upload`

**Request Body:**
```json
{
    "store_id": 1,
    "sync_timestamp": "2025-07-11T10:30:00Z",
    "data": {
        "transactions": [
            {
                "local_id": 1,
                "transaction_number": "TRX20250711001",
                "customer_id": null,
                "cashier_id": 1,
                "transaction_date": "2025-07-11T10:15:00Z",
                "subtotal": 25000.00,
                "tax_amount": 2500.00,
                "discount_amount": 0.00,
                "total_amount": 27500.00,
                "payment_method": "cash",
                "payment_status": "paid",
                "notes": null,
                "details": [
                    {
                        "product_id": 1,
                        "quantity": 1,
                        "unit_price": 25000.00,
                        "discount_amount": 0.00,
                        "total_price": 25000.00
                    }
                ],
                "payments": [
                    {
                        "payment_method": "cash",
                        "amount": 27500.00,
                        "reference_number": null,
                        "payment_status": "success"
                    }
                ]
            }
        ],
        "customers": [
            {
                "local_id": 1,
                "name": "John Doe",
                "email": "john@example.com",
                "phone": "+6281234567890",
                "address": "123 Customer Street",
                "loyalty_points": 100,
                "created_at": "2025-07-11T10:00:00Z"
            }
        ],
        "stock_movements": [
            {
                "product_id": 1,
                "movement_type": "out",
                "quantity": 1,
                "reference_type": "sale",
                "reference_id": 1,
                "notes": "Sale transaction",
                "created_at": "2025-07-11T10:15:00Z"
            }
        ]
    }
}
```

**Response:** `200 OK`
```json
{
    "success": true,
    "data": {
        "processed": {
            "transactions": {
                "success": 1,
                "failed": 0,
                "mappings": {
                    "1": 1001  // local_id: server_id
                },
                "errors": []
            },
            "customers": {
                "success": 1,
                "failed": 0,
                "mappings": {
                    "1": 501
                },
                "errors": []
            },
            "stock_movements": {
                "success": 1,
                "failed": 0,
                "errors": []
            }
        }
    },
    "meta": {
        "sync_id": "sync_uuid",
        "processed_at": "2025-07-11T10:30:00Z",
        "next_sync_after": "2025-07-11T11:30:00Z"
    }
}
```

### Sync Status
**Endpoint:** `GET /api/sync/status/{store_id}`

**Response:** `200 OK`
```json
{
    "success": true,
    "data": {
        "store_id": 1,
        "last_sync": "2025-07-11T10:30:00Z",
        "sync_status": "up_to_date",
        "pending_uploads": 0,
        "last_download": "2025-07-11T10:25:00Z",
        "last_upload": "2025-07-11T10:30:00Z",
        "sync_errors": [],
        "data_integrity": {
            "products": {
                "local_count": 150,
                "server_count": 150,
                "last_updated": "2025-07-11T09:00:00Z"
            },
            "transactions": {
                "pending_sync": 0,
                "last_synced": "2025-07-11T10:30:00Z"
            }
        }
    }
}
```

---

## Product Management

### Get Products
**Endpoint:** `GET /api/products`

**Parameters:**
- `store_id` (query, required): Store ID
- `category_id` (query, optional): Filter by category
- `search` (query, optional): Search in name, SKU, or barcode
- `is_active` (query, optional): Filter by active status
- `per_page` (query, optional): Number of items per page (default: 50, max: 100)
- `page` (query, optional): Page number (default: 1)

**Response:** `200 OK`
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "barcode": "1234567890123",
            "sku": "PROD001",
            "name": "Sample Product",
            "description": "Product description",
            "category": {
                "id": 1,
                "name": "Electronics"
            },
            "unit": "pcs",
            "cost_price": 15000.00,
            "selling_price": 25000.00,
            "stock_quantity": 100,
            "min_stock": 10,
            "is_active": true,
            "image_url": "https://cdn.yourpos.com/products/1.jpg"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 3,
        "per_page": 50,
        "total": 150,
        "from": 1,
        "to": 50
    }
}
```

### Get Product by ID
**Endpoint:** `GET /api/products/{id}`

**Parameters:**
- `store_id` (query, required): Store ID

**Response:** `200 OK`
```json
{
    "success": true,
    "data": {
        "id": 1,
        "barcode": "1234567890123",
        "sku": "PROD001",
        "name": "Sample Product",
        "description": "Detailed product description",
        "category": {
            "id": 1,
            "name": "Electronics",
            "parent": null
        },
        "brand": "Sample Brand",
        "unit": "pcs",
        "weight": 0.5,
        "dimensions": "10x5x2",
        "cost_price": 15000.00,
        "selling_price": 25000.00,
        "min_price": 20000.00,
        "max_price": 30000.00,
        "stock_quantity": 100,
        "min_stock": 10,
        "max_stock": 500,
        "location": "A1-B2",
        "is_active": true,
        "images": [
            "https://cdn.yourpos.com/products/1-main.jpg",
            "https://cdn.yourpos.com/products/1-alt.jpg"
        ],
        "attributes": {
            "color": "Blue",
            "size": "Medium"
        },
        "created_at": "2025-07-11T10:00:00Z",
        "updated_at": "2025-07-11T10:30:00Z"
    }
}
```

### Create Product
**Endpoint:** `POST /api/products`

**Request Body:**
```json
{
    "barcode": "1234567890123", // optional
    "sku": "PROD001", // required, unique
    "name": "New Product", // required
    "description": "Product description", // optional
    "category_id": 1, // required
    "brand": "Brand Name", // optional
    "unit": "pcs", // optional, default: "pcs"
    "weight": 0.5, // optional
    "dimensions": "10x5x2", // optional
    "cost_price": 15000.00, // optional, default: 0
    "suggested_price": 25000.00, // optional
    "min_price": 20000.00, // optional
    "max_price": 30000.00, // optional
    "is_active": true, // optional, default: true
    "attributes": { // optional
        "color": "Blue",
        "size": "Medium"
    },
    "store_products": [ // Store-specific data
        {
            "store_id": 1,
            "selling_price": 25000.00,
            "stock_quantity": 100,
            "min_stock": 10,
            "max_stock": 500,
            "location": "A1-B2"
        }
    ]
}
```

**Response:** `201 Created`
```json
{
    "success": true,
    "data": {
        "id": 2,
        "barcode": "1234567890123",
        "sku": "PROD001",
        "name": "New Product",
        // ... full product data
    },
    "message": "Product created successfully"
}
```

### Update Product
**Endpoint:** `PUT /api/products/{id}`

**Request Body:** (same as create, all fields optional)
```json
{
    "name": "Updated Product Name",
    "selling_price": 27000.00,
    "stock_quantity": 150
}
```

**Response:** `200 OK`
```json
{
    "success": true,
    "data": {
        // ... updated product data
    },
    "message": "Product updated successfully"
}
```

### Get Product by Barcode
**Endpoint:** `GET /api/products/barcode/{barcode}`

**Parameters:**
- `store_id` (query, required): Store ID

**Response:** `200 OK` (same format as Get Product by ID)

### Update Stock
**Endpoint:** `PUT /api/products/{id}/stock`

**Request Body:**
```json
{
    "store_id": 1, // required
    "quantity": 50, // required, can be negative for reduction
    "movement_type": "adjustment", // required: "in", "out", "adjustment"
    "reference_type": "manual", // optional: "sale", "purchase", "adjustment", "transfer"
    "reference_id": null, // optional
    "notes": "Manual stock adjustment" // optional
}
```

**Response:** `200 OK`
```json
{
    "success": true,
    "data": {
        "product_id": 1,
        "old_stock": 100,
        "new_stock": 150,
        "movement": {
            "id": 101,
            "movement_type": "adjustment",
            "quantity": 50,
            "notes": "Manual stock adjustment",
            "created_at": "2025-07-11T10:30:00Z"
        }
    },
    "message": "Stock updated successfully"
}
```

---

## Transaction Management

### Create Transaction
**Endpoint:** `POST /api/transactions`

**Request Body:**
```json
{
    "store_id": 1, // required
    "customer_id": null, // optional
    "cashier_id": 1, // required
    "transaction_date": "2025-07-11T10:15:00Z", // optional, default: now
    "items": [ // required, minimum 1 item
        {
            "product_id": 1, // required
            "quantity": 2, // required
            "unit_price": 25000.00, // required
            "discount_amount": 0.00, // optional, default: 0
            "notes": "Special request" // optional
        }
    ],
    "discount_amount": 0.00, // optional, transaction-level discount
    "tax_amount": 5000.00, // optional, will be calculated if not provided
    "payments": [ // required
        {
            "payment_method": "cash", // required: "cash", "card", "e_wallet", "bank_transfer"
            "amount": 55000.00, // required
            "reference_number": null // optional, required for non-cash payments
        }
    ],
    "notes": "Customer notes", // optional
    "receipt_email": "customer@example.com" // optional
}
```

**Response:** `201 Created`
```json
{
    "success": true,
    "data": {
        "id": 1001,
        "transaction_number": "TRX20250711001",
        "store": {
            "id": 1,
            "name": "Main Store"
        },
        "customer": null,
        "cashier": {
            "id": 1,
            "name": "John Cashier"
        },
        "transaction_date": "2025-07-11T10:15:00Z",
        "subtotal": 50000.00,
        "tax_amount": 5000.00,
        "discount_amount": 0.00,
        "total_amount": 55000.00,
        "payment_method": "cash",
        "payment_status": "paid",
        "details": [
            {
                "id": 1,
                "product": {
                    "id": 1,
                    "name": "Sample Product",
                    "sku": "PROD001"
                },
                "quantity": 2,
                "unit_price": 25000.00,
                "discount_amount": 0.00,
                "total_price": 50000.00
            }
        ],
        "payments": [
            {
                "id": 1,
                "payment_method": "cash",
                "amount": 55000.00,
                "payment_status": "success",
                "processed_at": "2025-07-11T10:15:00Z"
            }
        ],
        "receipt_url": "https://api.yourpos.com/receipts/TRX20250711001.pdf",
        "created_at": "2025-07-11T10:15:00Z"
    },
    "message": "Transaction created successfully"
}
```

### Get Transactions
**Endpoint:** `GET /api/transactions`

**Parameters:**
- `store_id` (query, optional): Filter by store
- `cashier_id` (query, optional): Filter by cashier
- `customer_id` (query, optional): Filter by customer
- `payment_method` (query, optional): Filter by payment method
- `payment_status` (query, optional): Filter by payment status
- `date_from` (query, optional): Start date (ISO 8601)
- `date_to` (query, optional): End date (ISO 8601)
- `search` (query, optional): Search in transaction number
- `per_page` (query, optional): Items per page (default: 50)
- `page` (query, optional): Page number (default: 1)

**Response:** `200 OK`
```json
{
    "success": true,
    "data": [
        {
            "id": 1001,
            "transaction_number": "TRX20250711001",
            "store_name": "Main Store",
            "customer_name": null,
            "cashier_name": "John Cashier",
            "transaction_date": "2025-07-11T10:15:00Z",
            "total_amount": 55000.00,
            "payment_method": "cash",
            "payment_status": "paid",
            "items_count": 2
        }
    ],
    "meta": {
        "current_page": 1,
        "total": 100,
        "per_page": 50
    }
}
```

### Get Transaction Details
**Endpoint:** `GET /api/transactions/{id}`

**Response:** `200 OK` (same format as Create Transaction response)

### Void Transaction
**Endpoint:** `POST /api/transactions/{id}/void`

**Request Body:**
```json
{
    "reason": "Customer request", // required
    "voided_by": 1 // required, user ID
}
```

**Response:** `200 OK`
```json
{
    "success": true,
    "data": {
        "id": 1001,
        "status": "voided",
        "voided_at": "2025-07-11T10:30:00Z",
        "voided_by": {
            "id": 1,
            "name": "Manager"
        },
        "void_reason": "Customer request"
    },
    "message": "Transaction voided successfully"
}
```

### Process Refund
**Endpoint:** `POST /api/transactions/{id}/refund`

**Request Body:**
```json
{
    "items": [ // optional, if not provided, full refund
        {
            "transaction_detail_id": 1,
            "quantity": 1,
            "reason": "Defective item"
        }
    ],
    "refund_amount": 25000.00, // optional, calculated if not provided
    "refund_method": "cash", // required
    "processed_by": 1, // required, user ID
    "notes": "Customer complaint"
}
```

**Response:** `201 Created`
```json
{
    "success": true,
    "data": {
        "refund_id": 1,
        "original_transaction_id": 1001,
        "refund_transaction_number": "REF20250711001",
        "refund_amount": 25000.00,
        "refund_method": "cash",
        "processed_by": {
            "id": 1,
            "name": "Manager"
        },
        "processed_at": "2025-07-11T10:30:00Z",
        "refunded_items": [
            {
                "product_name": "Sample Product",
                "quantity": 1,
                "refund_amount": 25000.00
            }
        ]
    },
    "message": "Refund processed successfully"
}
```

---

## Customer Management

### Get Customers
**Endpoint:** `GET /api/customers`

**Parameters:**
- `store_id` (query, optional): Filter by preferred store
- `search` (query, optional): Search in name, email, or phone
- `is_active` (query, optional): Filter by active status
- `per_page` (query, optional): Items per page (default: 50)

**Response:** `200 OK`
```json
{
    "success": true,
    "data": [
        {
            "id": 501,
            "customer_code": "CUST001",
            "name": "John Doe",
            "email": "john@example.com",
            "phone": "+6281234567890",
            "loyalty_points": 150,
            "total_purchases": 500000.00,
            "total_visits": 5,
            "last_visit": "2025-07-10T15:30:00Z",
            "is_active": true
        }
    ],
    "meta": {
        "current_page": 1,
        "total": 50,
        "per_page": 50
    }
}
```

### Create Customer
**Endpoint:** `POST /api/customers`

**Request Body:**
```json
{
    "name": "Jane Doe", // required
    "email": "jane@example.com", // optional
    "phone": "+6281234567891", // optional
    "address": "123 Customer Street", // optional
    "city": "Jakarta", // optional
    "date_of_birth": "1990-01-01", // optional
    "gender": "female", // optional: "male", "female", "other"
    "preferred_store_id": 1 // optional
}
```

**Response:** `201 Created`
```json
{
    "success": true,
    "data": {
        "id": 502,
        "customer_code": "CUST002",
        "name": "Jane Doe",
        "email": "jane@example.com",
        "phone": "+6281234567891",
        "address": "123 Customer Street",
        "city": "Jakarta",
        "date_of_birth": "1990-01-01",
        "gender": "female",
        "loyalty_points": 0,
        "total_purchases": 0.00,
        "total_visits": 0,
        "preferred_store": {
            "id": 1,
            "name": "Main Store"
        },
        "is_active": true,
        "created_at": "2025-07-11T10:30:00Z"
    },
    "message": "Customer created successfully"
}
```

### Update Customer
**Endpoint:** `PUT /api/customers/{id}`

**Request Body:** (same as create, all fields optional)

**Response:** `200 OK` (same format as create response)

### Customer Purchase History
**Endpoint:** `GET /api/customers/{id}/transactions`

**Parameters:**
- `date_from` (query, optional): Start date
- `date_to` (query, optional): End date
- `per_page` (query, optional): Items per page

**Response:** `200 OK`
```json
{
    "success": true,
    "data": [
        {
            "id": 1001,
            "transaction_number": "TRX20250711001",
            "store_name": "Main Store",
            "transaction_date": "2025-07-11T10:15:00Z",
            "total_amount": 55000.00,
            "payment_method": "cash",
            "points_earned": 55
        }
    ],
    "meta": {
        "total_transactions": 5,
        "total_amount": 500000.00,
        "total_points": 500
    }
}
```

### Update Loyalty Points
**Endpoint:** `PUT /api/customers/{id}/loyalty-points`

**Request Body:**
```json
{
    "points": 100, // required, can be negative
    "reason": "Manual adjustment", // required
    "adjusted_by": 1 // required, user ID
}
```

**Response:** `200 OK`
```json
{
    "success": true,
    "data": {
        "customer_id": 501,
        "old_points": 150,
        "new_points": 250,
        "adjustment": 100,
        "reason": "Manual adjustment",
        "adjusted_by": {
            "id": 1,
            "name": "Manager"
        },
        "adjusted_at": "2025-07-11T10:30:00Z"
    },
    "message": "Loyalty points updated successfully"
}
```

---

## Store Management

### Get Stores
**Endpoint:** `GET /api/stores`

**Parameters:**
- `search` (query, optional): Search in store name or code
- `city` (query, optional): Filter by city
- `is_active` (query, optional): Filter by active status

**Response:** `200 OK`
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "store_code": "ST001",
            "store_name": "Main Store",
            "address": "123 Main Street",
            "city": "Jakarta",
            "phone": "+6221234567890",
            "manager": {
                "id": 5,
                "name": "Store Manager"
            },
            "is_active": true,
            "last_sync": "2025-07-11T10:30:00Z"
        }
    ]
}
```

### Get Store Details
**Endpoint:** `GET /api/stores/{id}`

**Response:** `200 OK`
```json
{
    "success": true,
    "data": {
        "id": 1,
        "store_code": "ST001",
        "store_name": "Main Store",
        "address": "123 Main Street",
        "city": "Jakarta",
        "province": "DKI Jakarta",
        "postal_code": "12345",
        "phone": "+6221234567890",
        "email": "mainstore@yourpos.com",
        "manager": {
            "id": 5,
            "name": "Store Manager",
            "email": "manager@yourpos.com"
        },
        "tax_rate": 10.00,
        "currency": "IDR",
        "timezone": "Asia/Jakarta",
        "settings": {
            "receipt_template": "default",
            "auto_print_receipt": true,
            "require_customer_info": false
        },
        "staff_count": 5,
        "product_count": 150,
        "is_active": true,
        "last_sync": "2025-07-11T10:30:00Z",
        "created_at": "2025-01-01T00:00:00Z"
    }
}
```

---

## Reports & Analytics

### Sales Summary
**Endpoint:** `GET /api/reports/sales-summary`

**Parameters:**
- `store_id` (query, optional): Filter by store
- `date_from` (query, required): Start date (ISO 8601)
- `date_to` (query, required): End date (ISO 8601)
- `group_by` (query, optional): "hour", "day", "week", "month" (default: "day")

**Response:** `200 OK`
```json
{
    "success": true,
    "data": {
        "summary": {
            "total_sales": 1500000.00,
            "total_transactions": 50,
            "average_transaction": 30000.00,
            "total_tax": 150000.00,
            "total_discount": 50000.00,
            "gross_profit": 500000.00,
            "profit_margin": 33.33
        },
        "payment_methods": [
            {
                "method": "cash",
                "count": 30,
                "amount": 900000.00,
                "percentage": 60.0
            },
            {
                "method": "card",
                "count": 15,
                "amount": 450000.00,
                "percentage": 30.0
            },
            {
                "method": "e_wallet",
                "count": 5,
                "amount": 150000.00,
                "percentage": 10.0
            }
        ],
        "hourly_sales": [
            {
                "hour": "09:00",
                "transactions": 5,
                "amount": 150000.00
            },
            {
                "hour": "10:00",
                "transactions": 8,
                "amount": 240000.00
            }
        ],
        "daily_sales": [
            {
                "date": "2025-07-11",
                "transactions": 50,
                "amount": 1500000.00
            }
        ]
    }
}
```

### Product Performance
**Endpoint:** `GET /api/reports/product-performance`

**Parameters:**
- `store_id` (query, optional): Filter by store
- `date_from` (query, required): Start date
- `date_to` (query, required): End date
- `category_id` (query, optional): Filter by category
- `limit` (query, optional): Number of top products (default: 20)

**Response:** `200 OK`
```json
{
    "success": true,
    "data": {
        "top_selling_products": [
            {
                "product_id": 1,
                "product_name": "Sample Product",
                "sku": "PROD001",
                "category": "Electronics",
                "quantity_sold": 100,
                "total_revenue": 2500000.00,
                "profit": 1000000.00,
                "profit_margin": 40.0
            }
        ],
        "slow_moving_products": [
            {
                "product_id": 10,
                "product_name": "Slow Product",
                "sku": "PROD010",
                "stock_quantity": 50,
                "last_sold": "2025-07-01T10:00:00Z",
                "days_since_last_sale": 10
            }
        ],
        "low_stock_products": [
            {
                "product_id": 5,
                "product_name": "Low Stock Product",
                "sku": "PROD005",
                "current_stock": 3,
                "min_stock": 10,
                "reorder_quantity": 50
            }
        ]
    }
}
```

### Customer Analytics
**Endpoint:** `GET /api/reports/customer-analytics`

**Parameters:**
- `store_id` (query, optional): Filter by store
- `date_from` (query, required): Start date
- `date_to` (query, required): End date

**Response:** `200 OK`
```json
{
    "success": true,
    "data": {
        "customer_summary": {
            "total_customers": 500,
            "new_customers": 25,
            "returning_customers": 475,
            "average_purchase": 55000.00,
            "total_loyalty_points_issued": 15000
        },
        "top_customers": [
            {
                "customer_id": 501,
                "customer_name": "John Doe",
                "total_purchases": 500000.00,
                "visit_count": 10,
                "last_visit": "2025-07-10T15:30:00Z"
            }
        ],
        "customer_segments": [
            {
                "segment": "high_value",
                "customer_count": 50,
                "avg_purchase": 100000.00,
                "total_revenue": 5000000.00
            },
            {
                "segment": "regular",
                "customer_count": 300,
                "avg_purchase": 50000.00,
                "total_revenue": 15000000.00
            },
            {
                "segment": "occasional",
                "customer_count": 150,
                "avg_purchase": 25000.00,
                "total_revenue": 3750000.00
            }
        ]
    }
}
```

---

## System Administration

### System Health Check
**Endpoint:** `GET /api/system/health`

**Response:** `200 OK`
```json
{
    "success": true,
    "data": {
        "status": "healthy",
        "timestamp": "2025-07-11T10:30:00Z",
        "checks": {
            "database": {
                "status": "ok",
                "response_time": 15,
                "connections": {
                    "active": 5,
                    "max": 100
                }
            },
            "redis": {
                "status": "ok",
                "response_time": 2,
                "memory_usage": "45MB"
            },
            "storage": {
                "status": "ok",
                "disk_usage": "65%",
                "available_space": "50GB"
            },
            "queue": {
                "status": "ok",
                "pending_jobs": 0,
                "failed_jobs": 0
            }
        },
        "version": "1.0.0",
        "uptime": "15 days 6 hours"
    }
}
```

### System Configuration
**Endpoint:** `GET /api/system/config`

**Response:** `200 OK`
```json
{
    "success": true,
    "data": {
        "app": {
            "name": "POS System",
            "version": "1.0.0",
            "environment": "production"
        },
        "features": {
            "offline_mode": true,
            "loyalty_program": true,
            "multi_currency": false,
            "barcode_generation": true
        },
        "limits": {
            "max_transaction_items": 100,
            "max_file_upload_size": "10MB",
            "api_rate_limit": 1000
        },
        "integrations": {
            "payment_gateways": ["midtrans", "xendit"],
            "email_service": "mailgun",
            "sms_service": "twilio"
        }
    }
}
```

### Audit Logs
**Endpoint:** `GET /api/system/audit-logs`

**Parameters:**
- `user_id` (query, optional): Filter by user
- `store_id` (query, optional): Filter by store
- `action` (query, optional): Filter by action type
- `date_from` (query, optional): Start date
- `date_to` (query, optional): End date
- `per_page` (query, optional): Items per page

**Response:** `200 OK`
```json
{
    "success": true,
    "data": [
        {
            "id": 1001,
            "user": {
                "id": 1,
                "name": "Admin User"
            },
            "store": {
                "id": 1,
                "name": "Main Store"
            },
            "action": "product_updated",
            "table_name": "products",
            "record_id": 1,
            "changes": {
                "selling_price": {
                    "old": 25000.00,
                    "new": 27000.00
                }
            },
            "ip_address": "192.168.1.100",
            "user_agent": "Mozilla/5.0...",
            "created_at": "2025-07-11T10:30:00Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "total": 1000,
        "per_page": 50
    }
}
```

---

## Response Codes Summary

| Code | Description |
|------|-------------|
| 200 | OK - Request successful |
| 201 | Created - Resource created successfully |
| 204 | No Content - Request successful, no response body |
| 400 | Bad Request - Invalid request data |
| 401 | Unauthorized - Authentication required |
| 403 | Forbidden - Access denied |
| 404 | Not Found - Resource not found |
| 409 | Conflict - Resource conflict |
| 422 | Unprocessable Entity - Validation failed |
| 429 | Too Many Requests - Rate limit exceeded |
| 500 | Internal Server Error - Server error |
| 503 | Service Unavailable - Service temporarily unavailable |

---

## SDK Examples

### PHP SDK Usage
```php
use YourPOS\ApiClient;

$client = new ApiClient([
    'base_url' => 'https://api.yourpos.com',
    'api_token' => 'your_api_token',
    'store_id' => 1
]);

// Create transaction
$transaction = $client->transactions()->create([
    'items' => [
        [
            'product_id' => 1,
            'quantity' => 2,
            'unit_price' => 25000.00
        ]
    ],
    'payments' => [
        [
            'payment_method' => 'cash',
            'amount' => 50000.00
        ]
    ]
]);

// Get products
$products = $client->products()->list([
    'search' => 'electronics',
    'per_page' => 20
]);

// Sync data
$syncResult = $client->sync()->upload([
    'transactions' => $pendingTransactions,
    'customers' => $newCustomers
]);
```

### JavaScript SDK Usage
```javascript
import { PosApiClient } from '@yourpos/api-client';

const client = new PosApiClient({
    baseUrl: 'https://api.yourpos.com',
    apiToken: 'your_api_token',
    storeId: 1
});

// Create transaction
const transaction = await client.transactions.create({
    items: [{
        product_id: 1,
        quantity: 2,
        unit_price: 25000.00
    }],
    payments: [{
        payment_method: 'cash',
        amount: 50000.00
    }]
});

// Get products
const products = await client.products.list({
    search: 'electronics',
    per_page: 20
});

// Real-time sync
client.sync.onUpdate((data) => {
    console.log('Sync update received:', data);
});
```

---

**Document Version:** 1.0  
**Last Updated:** 11 Juli 2025  
**Maintained by:** Development Team
