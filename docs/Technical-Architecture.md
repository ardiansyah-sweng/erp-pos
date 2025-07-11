# Technical Architecture Document
## POS System Implementation Guide

### Version: 1.0
### Date: 11 Juli 2025

---

## 1. System Components Overview

### 1.1 Frontend Architecture (Store Level)
```
┌─────────────────────────────────────────────────────────┐
│                  POS Frontend                           │
├─────────────────────────────────────────────────────────┤
│  Presentation Layer                                     │
│  ├── Vue.js Components / Laravel Livewire              │
│  ├── POS Interface                                      │
│  ├── Reports Dashboard                                  │
│  └── Settings Panel                                     │
├─────────────────────────────────────────────────────────┤
│  Business Logic Layer                                   │
│  ├── Transaction Processing                             │
│  ├── Inventory Management                               │
│  ├── Customer Management                                │
│  └── Sync Management                                    │
├─────────────────────────────────────────────────────────┤
│  Data Access Layer                                      │
│  ├── Local SQLite Database                              │
│  ├── Cache Management                                   │
│  ├── Queue Management                                   │
│  └── API Client                                         │
├─────────────────────────────────────────────────────────┤
│  Hardware Integration                                   │
│  ├── Barcode Scanner                                    │
│  ├── Receipt Printer                                    │
│  ├── Cash Drawer                                        │
│  └── Payment Terminal                                   │
└─────────────────────────────────────────────────────────┘
```

### 1.2 Backend Architecture (Server Level)
```
┌─────────────────────────────────────────────────────────┐
│                  POS Backend                            │
├─────────────────────────────────────────────────────────┤
│  API Gateway Layer                                      │
│  ├── Authentication & Authorization                     │
│  ├── Rate Limiting                                      │
│  ├── Request Validation                                 │
│  └── Response Formatting                                │
├─────────────────────────────────────────────────────────┤
│  Application Layer                                      │
│  ├── Store Management Service                           │
│  ├── Product Management Service                         │
│  ├── Transaction Processing Service                     │
│  ├── Analytics Service                                  │
│  ├── Sync Management Service                            │
│  └── Notification Service                               │
├─────────────────────────────────────────────────────────┤
│  Data Layer                                             │
│  ├── MySQL Database                                     │
│  ├── Redis Cache                                        │
│  ├── Queue System (Redis/Database)                      │
│  └── File Storage                                       │
├─────────────────────────────────────────────────────────┤
│  Integration Layer                                      │
│  ├── Payment Gateway APIs                               │
│  ├── Email/SMS Services                                 │
│  ├── Third-party Integrations                           │
│  └── Webhook Handlers                                   │
└─────────────────────────────────────────────────────────┘
```

---

## 2. Database Schema Details

### 2.1 Frontend SQLite Schema

```sql
-- Store configuration
CREATE TABLE store_config (
    id INTEGER PRIMARY KEY,
    store_id VARCHAR(50) NOT NULL,
    store_name VARCHAR(255) NOT NULL,
    store_address TEXT,
    tax_rate DECIMAL(5,2) DEFAULT 0.00,
    currency VARCHAR(3) DEFAULT 'IDR',
    timezone VARCHAR(50) DEFAULT 'Asia/Jakarta',
    last_sync DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Users (local staff)
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    role ENUM('cashier', 'supervisor', 'manager') NOT NULL,
    is_active BOOLEAN DEFAULT 1,
    last_login DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Product categories
CREATE TABLE categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    server_id INTEGER, -- ID from backend
    name VARCHAR(255) NOT NULL,
    description TEXT,
    parent_id INTEGER,
    is_active BOOLEAN DEFAULT 1,
    sync_status ENUM('synced', 'pending', 'failed') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id)
);

-- Products
CREATE TABLE products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    server_id INTEGER, -- ID from backend
    barcode VARCHAR(255) UNIQUE,
    sku VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category_id INTEGER,
    unit VARCHAR(50) DEFAULT 'pcs',
    cost_price DECIMAL(15,2) DEFAULT 0.00,
    selling_price DECIMAL(15,2) NOT NULL,
    stock_quantity INTEGER DEFAULT 0,
    min_stock INTEGER DEFAULT 0,
    max_stock INTEGER DEFAULT 1000,
    is_active BOOLEAN DEFAULT 1,
    image_url VARCHAR(500),
    sync_status ENUM('synced', 'pending', 'failed') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Customers
CREATE TABLE customers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    server_id INTEGER, -- ID from backend
    customer_code VARCHAR(100),
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(20),
    address TEXT,
    date_of_birth DATE,
    loyalty_points INTEGER DEFAULT 0,
    total_purchases DECIMAL(15,2) DEFAULT 0.00,
    last_visit DATETIME,
    is_active BOOLEAN DEFAULT 1,
    sync_status ENUM('synced', 'pending', 'failed') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Transactions
CREATE TABLE transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    server_id INTEGER, -- ID from backend after sync
    transaction_number VARCHAR(100) UNIQUE NOT NULL,
    customer_id INTEGER,
    cashier_id INTEGER NOT NULL,
    transaction_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    subtotal DECIMAL(15,2) NOT NULL,
    tax_amount DECIMAL(15,2) DEFAULT 0.00,
    discount_amount DECIMAL(15,2) DEFAULT 0.00,
    total_amount DECIMAL(15,2) NOT NULL,
    payment_method ENUM('cash', 'card', 'e_wallet', 'bank_transfer', 'split') NOT NULL,
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    notes TEXT,
    sync_status ENUM('synced', 'pending', 'failed') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (cashier_id) REFERENCES users(id)
);

-- Transaction details
CREATE TABLE transaction_details (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    transaction_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    unit_price DECIMAL(15,2) NOT NULL,
    discount_amount DECIMAL(15,2) DEFAULT 0.00,
    total_price DECIMAL(15,2) NOT NULL,
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Payment details for split payments
CREATE TABLE payment_details (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    transaction_id INTEGER NOT NULL,
    payment_method ENUM('cash', 'card', 'e_wallet', 'bank_transfer') NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    reference_number VARCHAR(255),
    payment_status ENUM('pending', 'success', 'failed') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE
);

-- Sync queue for data synchronization
CREATE TABLE sync_queue (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    table_name VARCHAR(100) NOT NULL,
    record_id INTEGER,
    action ENUM('insert', 'update', 'delete') NOT NULL,
    data_json TEXT, -- JSON representation of the record
    sync_status ENUM('pending', 'processing', 'success', 'failed') DEFAULT 'pending',
    retry_count INTEGER DEFAULT 0,
    error_message TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    processed_at DATETIME
);

-- Stock movements
CREATE TABLE stock_movements (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id INTEGER NOT NULL,
    movement_type ENUM('in', 'out', 'adjustment') NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    reference_type ENUM('sale', 'purchase', 'adjustment', 'transfer') NOT NULL,
    reference_id INTEGER, -- transaction_id, adjustment_id, etc.
    notes TEXT,
    created_by INTEGER,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- System logs
CREATE TABLE system_logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    level ENUM('info', 'warning', 'error', 'critical') NOT NULL,
    message TEXT NOT NULL,
    context_json TEXT, -- JSON context data
    user_id INTEGER,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Create indexes for better performance
CREATE INDEX idx_products_barcode ON products(barcode);
CREATE INDEX idx_products_sku ON products(sku);
CREATE INDEX idx_products_name ON products(name);
CREATE INDEX idx_transactions_date ON transactions(transaction_date);
CREATE INDEX idx_transactions_cashier ON transactions(cashier_id);
CREATE INDEX idx_sync_queue_status ON sync_queue(sync_status);
CREATE INDEX idx_stock_movements_product ON stock_movements(product_id);
```

### 2.2 Backend MySQL Schema

```sql
-- Stores
CREATE TABLE stores (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    store_code VARCHAR(50) UNIQUE NOT NULL,
    store_name VARCHAR(255) NOT NULL,
    address TEXT,
    city VARCHAR(100),
    province VARCHAR(100),
    postal_code VARCHAR(10),
    phone VARCHAR(20),
    email VARCHAR(255),
    manager_id BIGINT UNSIGNED,
    tax_rate DECIMAL(5,2) DEFAULT 0.00,
    currency VARCHAR(3) DEFAULT 'IDR',
    timezone VARCHAR(50) DEFAULT 'Asia/Jakarta',
    is_active BOOLEAN DEFAULT TRUE,
    last_sync TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_store_code (store_code),
    INDEX idx_store_active (is_active)
);

-- Users (all system users)
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'admin', 'regional_manager', 'store_manager', 'supervisor', 'cashier') NOT NULL,
    store_id BIGINT UNSIGNED,
    permissions JSON,
    is_active BOOLEAN DEFAULT TRUE,
    email_verified_at TIMESTAMP NULL,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id),
    INDEX idx_users_email (email),
    INDEX idx_users_role (role),
    INDEX idx_users_store (store_id)
);

-- Product categories
CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    parent_id BIGINT UNSIGNED,
    sort_order INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id),
    INDEX idx_categories_parent (parent_id),
    INDEX idx_categories_active (is_active)
);

-- Products master data
CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    barcode VARCHAR(255) UNIQUE,
    sku VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category_id BIGINT UNSIGNED,
    brand VARCHAR(255),
    unit VARCHAR(50) DEFAULT 'pcs',
    weight DECIMAL(8,2),
    dimensions VARCHAR(100), -- "L x W x H"
    cost_price DECIMAL(15,2) DEFAULT 0.00,
    suggested_price DECIMAL(15,2),
    min_price DECIMAL(15,2),
    max_price DECIMAL(15,2),
    is_active BOOLEAN DEFAULT TRUE,
    image_url VARCHAR(500),
    images JSON, -- Array of image URLs
    attributes JSON, -- Product attributes like color, size, etc.
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    INDEX idx_products_barcode (barcode),
    INDEX idx_products_sku (sku),
    INDEX idx_products_name (name),
    INDEX idx_products_category (category_id),
    INDEX idx_products_active (is_active)
);

-- Store-specific product data
CREATE TABLE store_products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    store_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    selling_price DECIMAL(15,2) NOT NULL,
    stock_quantity INTEGER DEFAULT 0,
    min_stock INTEGER DEFAULT 0,
    max_stock INTEGER DEFAULT 1000,
    location VARCHAR(100), -- Shelf location
    is_available BOOLEAN DEFAULT TRUE,
    last_restocked TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    UNIQUE KEY unique_store_product (store_id, product_id),
    INDEX idx_store_products_store (store_id),
    INDEX idx_store_products_product (product_id),
    INDEX idx_store_products_stock (stock_quantity)
);

-- Customers (consolidated from all stores)
CREATE TABLE customers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_code VARCHAR(100) UNIQUE,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    date_of_birth DATE,
    gender ENUM('male', 'female', 'other'),
    loyalty_points INTEGER DEFAULT 0,
    total_purchases DECIMAL(15,2) DEFAULT 0.00,
    total_visits INTEGER DEFAULT 0,
    first_visit TIMESTAMP NULL,
    last_visit TIMESTAMP NULL,
    preferred_store_id BIGINT UNSIGNED,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (preferred_store_id) REFERENCES stores(id),
    INDEX idx_customers_email (email),
    INDEX idx_customers_phone (phone),
    INDEX idx_customers_code (customer_code)
);

-- Transactions (aggregated from all stores)
CREATE TABLE transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    store_id BIGINT UNSIGNED NOT NULL,
    local_id INTEGER, -- ID from local store database
    transaction_number VARCHAR(100) UNIQUE NOT NULL,
    customer_id BIGINT UNSIGNED,
    cashier_id BIGINT UNSIGNED NOT NULL,
    transaction_date TIMESTAMP NOT NULL,
    subtotal DECIMAL(15,2) NOT NULL,
    tax_amount DECIMAL(15,2) DEFAULT 0.00,
    discount_amount DECIMAL(15,2) DEFAULT 0.00,
    total_amount DECIMAL(15,2) NOT NULL,
    payment_method ENUM('cash', 'card', 'e_wallet', 'bank_transfer', 'split') NOT NULL,
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    notes TEXT,
    synced_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (cashier_id) REFERENCES users(id),
    INDEX idx_transactions_store (store_id),
    INDEX idx_transactions_date (transaction_date),
    INDEX idx_transactions_cashier (cashier_id),
    INDEX idx_transactions_customer (customer_id),
    INDEX idx_transactions_number (transaction_number)
);

-- Transaction details
CREATE TABLE transaction_details (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    transaction_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    unit_price DECIMAL(15,2) NOT NULL,
    discount_amount DECIMAL(15,2) DEFAULT 0.00,
    total_price DECIMAL(15,2) NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    INDEX idx_transaction_details_transaction (transaction_id),
    INDEX idx_transaction_details_product (product_id)
);

-- Payment details
CREATE TABLE payment_details (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    transaction_id BIGINT UNSIGNED NOT NULL,
    payment_method ENUM('cash', 'card', 'e_wallet', 'bank_transfer') NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    reference_number VARCHAR(255),
    gateway_response JSON,
    payment_status ENUM('pending', 'success', 'failed') DEFAULT 'pending',
    processed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
    INDEX idx_payment_details_transaction (transaction_id),
    INDEX idx_payment_details_method (payment_method)
);

-- Sync logs
CREATE TABLE sync_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    store_id BIGINT UNSIGNED NOT NULL,
    sync_type ENUM('upload', 'download') NOT NULL,
    table_name VARCHAR(100) NOT NULL,
    records_count INTEGER DEFAULT 0,
    success_count INTEGER DEFAULT 0,
    failed_count INTEGER DEFAULT 0,
    status ENUM('started', 'completed', 'failed') NOT NULL,
    error_details JSON,
    started_at TIMESTAMP NOT NULL,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id),
    INDEX idx_sync_logs_store (store_id),
    INDEX idx_sync_logs_type (sync_type),
    INDEX idx_sync_logs_status (status)
);

-- Stock movements (consolidated)
CREATE TABLE stock_movements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    store_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    movement_type ENUM('in', 'out', 'adjustment', 'transfer') NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    reference_type ENUM('sale', 'purchase', 'adjustment', 'transfer', 'return') NOT NULL,
    reference_id BIGINT UNSIGNED,
    from_store_id BIGINT UNSIGNED, -- For transfers
    to_store_id BIGINT UNSIGNED, -- For transfers
    notes TEXT,
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (from_store_id) REFERENCES stores(id),
    FOREIGN KEY (to_store_id) REFERENCES stores(id),
    INDEX idx_stock_movements_store (store_id),
    INDEX idx_stock_movements_product (product_id),
    INDEX idx_stock_movements_type (movement_type)
);

-- System audit logs
CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED,
    store_id BIGINT UNSIGNED,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(100),
    record_id BIGINT UNSIGNED,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (store_id) REFERENCES stores(id),
    INDEX idx_audit_logs_user (user_id),
    INDEX idx_audit_logs_store (store_id),
    INDEX idx_audit_logs_action (action),
    INDEX idx_audit_logs_table (table_name)
);
```

---

## 3. API Endpoints Specification

### 3.1 Authentication & Authorization

```php
// Authentication endpoints
POST   /api/auth/login
{
    "username": "string",
    "password": "string",
    "store_id": "string"
}
Response: {
    "access_token": "string",
    "token_type": "Bearer",
    "expires_in": 3600,
    "user": {
        "id": 1,
        "username": "string",
        "full_name": "string",
        "role": "string",
        "store_id": 1,
        "permissions": []
    }
}

POST   /api/auth/refresh
Headers: Authorization: Bearer {token}
Response: {
    "access_token": "string",
    "token_type": "Bearer",
    "expires_in": 3600
}

POST   /api/auth/logout
Headers: Authorization: Bearer {token}
Response: {
    "message": "Successfully logged out"
}
```

### 3.2 Synchronization Endpoints

```php
// Download data from server to store
GET    /api/sync/download/{store_id}
Query Parameters:
- last_sync: timestamp (optional)
- tables: array of table names (optional)

Response: {
    "success": true,
    "data": {
        "products": [...],
        "categories": [...],
        "customers": [...],
        "settings": {...}
    },
    "last_sync": "2025-07-11T10:30:00Z"
}

// Upload data from store to server
POST   /api/sync/upload
{
    "store_id": "string",
    "data": {
        "transactions": [...],
        "customers": [...],
        "stock_movements": [...]
    }
}
Response: {
    "success": true,
    "processed": {
        "transactions": {
            "success": 10,
            "failed": 0,
            "errors": []
        },
        "customers": {
            "success": 5,
            "failed": 0,
            "errors": []
        }
    }
}

// Get sync status
GET    /api/sync/status/{store_id}
Response: {
    "last_sync": "2025-07-11T10:30:00Z",
    "pending_uploads": 5,
    "sync_errors": [],
    "status": "up_to_date"
}
```

### 3.3 Product Management

```php
// Get products for store
GET    /api/products
Query Parameters:
- store_id: integer (required)
- category_id: integer (optional)
- search: string (optional)
- per_page: integer (default: 50)

Response: {
    "data": [
        {
            "id": 1,
            "barcode": "string",
            "sku": "string",
            "name": "string",
            "category": {...},
            "selling_price": 25000,
            "stock_quantity": 100,
            "is_available": true
        }
    ],
    "meta": {
        "current_page": 1,
        "total": 100,
        "per_page": 50
    }
}

// Update product stock
PUT    /api/products/{id}/stock
{
    "store_id": 1,
    "quantity": 50,
    "movement_type": "adjustment",
    "notes": "Manual adjustment"
}

// Get product by barcode
GET    /api/products/barcode/{barcode}
Query Parameters:
- store_id: integer (required)
```

---

## 4. Synchronization Process Flow

### 4.1 Data Flow Diagram
```
Store Database (SQLite)          Backend Database (MySQL)
        │                                  │
        ├─── Local Operations ────────────▶ │
        │    (Transactions, etc.)          │
        │                                  │
        │    ◄────── Sync Queue ──────────▶ │
        │                                  │
        │    ◄─── Product Updates ─────────┤
        │                                  │
        └─── Status Updates ──────────────▶ │
```

### 4.2 Sync Implementation

```php
// Laravel Command for Frontend Sync
class SyncWithBackendCommand extends Command
{
    protected $signature = 'pos:sync {--force}';
    
    public function handle()
    {
        // 1. Upload pending transactions
        $this->uploadTransactions();
        
        // 2. Download product updates
        $this->downloadProducts();
        
        // 3. Sync customer data
        $this->syncCustomers();
        
        // 4. Update sync status
        $this->updateSyncStatus();
    }
    
    private function uploadTransactions()
    {
        $pendingTransactions = Transaction::where('sync_status', 'pending')
            ->with(['details', 'payments'])
            ->get();
            
        foreach ($pendingTransactions->chunk(10) as $chunk) {
            $response = Http::post('/api/sync/transactions', [
                'transactions' => $chunk->toArray()
            ]);
            
            if ($response->successful()) {
                $chunk->each(function($transaction) {
                    $transaction->update(['sync_status' => 'synced']);
                });
            }
        }
    }
}
```

---

## 5. Performance Optimization

### 5.1 Database Optimization
- **Indexing Strategy**: Critical indexes on frequently queried columns
- **Query Optimization**: Use of eager loading and proper joins
- **Data Archiving**: Move old transactions to archive tables
- **Connection Pooling**: Efficient database connection management

### 5.2 Frontend Performance
- **Local Caching**: Cache frequently accessed data
- **Lazy Loading**: Load data on demand
- **Background Sync**: Non-blocking synchronization
- **Compression**: Compress sync data transfers

### 5.3 Backend Performance
- **Redis Caching**: Cache frequently accessed data
- **Queue Processing**: Background job processing
- **API Rate Limiting**: Prevent system overload
- **Database Replication**: Read/write separation

---

## 6. Security Implementation

### 6.1 API Security
```php
// API Middleware
class ApiAuthMiddleware
{
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();
        
        if (!$token || !$this->validateToken($token)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $user = $this->getUserFromToken($token);
        $request->setUserResolver(function() use ($user) {
            return $user;
        });
        
        return $next($request);
    }
}

// Rate Limiting
class ApiRateLimitMiddleware
{
    public function handle($request, Closure $next, $maxAttempts = 60)
    {
        $key = $this->resolveRequestSignature($request);
        
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return response()->json(['error' => 'Too many requests'], 429);
        }
        
        RateLimiter::hit($key);
        
        return $next($request);
    }
}
```

### 6.2 Data Encryption
```php
// Sensitive Data Encryption
class EncryptionService
{
    public function encryptSensitiveData($data)
    {
        return Crypt::encrypt([
            'data' => $data,
            'timestamp' => now()->timestamp,
            'checksum' => hash('sha256', json_encode($data))
        ]);
    }
    
    public function decryptSensitiveData($encrypted)
    {
        $decrypted = Crypt::decrypt($encrypted);
        
        // Verify checksum
        $expectedChecksum = hash('sha256', json_encode($decrypted['data']));
        if ($decrypted['checksum'] !== $expectedChecksum) {
            throw new InvalidDataException('Data integrity check failed');
        }
        
        return $decrypted['data'];
    }
}
```

---

## 7. Error Handling & Logging

### 7.1 Error Handling Strategy
```php
// Global Exception Handler
class ApiExceptionHandler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson()) {
            return $this->handleApiException($exception);
        }
        
        return parent::render($request, $exception);
    }
    
    private function handleApiException(Throwable $exception)
    {
        $response = [
            'error' => true,
            'message' => $exception->getMessage(),
            'timestamp' => now()->toISOString()
        ];
        
        if (config('app.debug')) {
            $response['trace'] = $exception->getTraceAsString();
        }
        
        $statusCode = $this->getStatusCode($exception);
        
        // Log the error
        Log::error('API Exception', [
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'request' => $request->all()
        ]);
        
        return response()->json($response, $statusCode);
    }
}
```

### 7.2 Comprehensive Logging
```php
// Custom Log Channels
// config/logging.php
'channels' => [
    'pos_transactions' => [
        'driver' => 'daily',
        'path' => storage_path('logs/pos/transactions.log'),
        'level' => 'info',
        'days' => 14,
    ],
    
    'pos_sync' => [
        'driver' => 'daily',
        'path' => storage_path('logs/pos/sync.log'),
        'level' => 'debug',
        'days' => 30,
    ],
    
    'pos_security' => [
        'driver' => 'daily',
        'path' => storage_path('logs/pos/security.log'),
        'level' => 'warning',
        'days' => 90,
    ]
];

// Usage in Controllers
class TransactionController extends Controller
{
    public function store(Request $request)
    {
        try {
            $transaction = $this->createTransaction($request->all());
            
            Log::channel('pos_transactions')->info('Transaction created', [
                'transaction_id' => $transaction->id,
                'amount' => $transaction->total_amount,
                'cashier_id' => $transaction->cashier_id,
                'store_id' => $transaction->store_id
            ]);
            
            return response()->json($transaction, 201);
            
        } catch (Exception $e) {
            Log::channel('pos_transactions')->error('Transaction creation failed', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            
            throw $e;
        }
    }
}
```

---

## 8. Testing Strategy Implementation

### 8.1 Unit Testing
```php
// Example Unit Test
class ProductServiceTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_can_create_product_with_valid_data()
    {
        $productData = [
            'sku' => 'TEST001',
            'name' => 'Test Product',
            'category_id' => 1,
            'selling_price' => 25000
        ];
        
        $product = ProductService::create($productData);
        
        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('TEST001', $product->sku);
        $this->assertDatabaseHas('products', $productData);
    }
    
    public function test_cannot_create_product_with_duplicate_sku()
    {
        Product::factory()->create(['sku' => 'TEST001']);
        
        $this->expectException(ValidationException::class);
        
        ProductService::create([
            'sku' => 'TEST001',
            'name' => 'Another Product',
            'selling_price' => 30000
        ]);
    }
}
```

### 8.2 Integration Testing
```php
// Sync Integration Test
class SyncIntegrationTest extends TestCase
{
    public function test_transaction_sync_from_store_to_backend()
    {
        // Create test transaction in store database
        $transaction = Transaction::factory()->create([
            'sync_status' => 'pending'
        ]);
        
        // Mock HTTP client
        Http::fake([
            'api/sync/upload' => Http::response([
                'success' => true,
                'processed' => ['transactions' => ['success' => 1, 'failed' => 0]]
            ], 200)
        ]);
        
        // Run sync command
        Artisan::call('pos:sync');
        
        // Assert transaction is marked as synced
        $transaction->refresh();
        $this->assertEquals('synced', $transaction->sync_status);
        
        // Assert HTTP request was made
        Http::assertSent(function ($request) {
            return $request->url() === 'api/sync/upload' &&
                   isset($request['transactions']);
        });
    }
}
```

---

## 9. Deployment Configuration

### 9.1 Environment Configuration
```bash
# Frontend (.env)
APP_NAME="POS Frontend"
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=http://localhost

DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite

BACKEND_API_URL=https://api.yourpos.com
BACKEND_API_TOKEN=your_api_token

SYNC_INTERVAL=3600 # seconds
OFFLINE_MODE_ENABLED=true
```

```bash
# Backend (.env)
APP_NAME="POS Backend"
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://api.yourpos.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_backend
DB_USERNAME=pos_user
DB_PASSWORD=secure_password

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

QUEUE_CONNECTION=redis
```

### 9.2 Docker Configuration
```dockerfile
# Backend Dockerfile
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --optimize-autoloader --no-dev

RUN chown -R www-data:www-data \
    /var/www/storage \
    /var/www/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
```

```yaml
# docker-compose.yml
version: '3.8'
services:
  backend:
    build: .
    container_name: pos_backend
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
      - ./docker/php/local.ini:/usr/local/etc/php/conf.d/local.ini
    networks:
      - pos_network

  nginx:
    image: nginx:alpine
    container_name: pos_nginx
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./:/var/www
      - ./docker/nginx:/etc/nginx/conf.d
    networks:
      - pos_network

  mysql:
    image: mysql:8.0
    container_name: pos_mysql
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: pos_backend
      MYSQL_ROOT_PASSWORD: root_password
      MYSQL_USER: pos_user
      MYSQL_PASSWORD: secure_password
    volumes:
      - mysql_data:/var/lib/mysql
    networks:
      - pos_network

  redis:
    image: redis:alpine
    container_name: pos_redis
    restart: unless-stopped
    networks:
      - pos_network

networks:
  pos_network:
    driver: bridge

volumes:
  mysql_data:
    driver: local
```

---

## 10. Monitoring & Maintenance

### 10.1 Health Checks
```php
// Health Check Endpoint
class HealthController extends Controller
{
    public function check()
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'redis' => $this->checkRedis(),
            'storage' => $this->checkStorage(),
            'queue' => $this->checkQueue()
        ];
        
        $healthy = array_reduce($checks, function($carry, $check) {
            return $carry && $check['status'] === 'ok';
        }, true);
        
        return response()->json([
            'status' => $healthy ? 'healthy' : 'unhealthy',
            'checks' => $checks,
            'timestamp' => now()->toISOString()
        ], $healthy ? 200 : 503);
    }
    
    private function checkDatabase()
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'ok', 'message' => 'Database connection successful'];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
```

### 10.2 Performance Monitoring
```php
// Performance Middleware
class PerformanceMonitoringMiddleware
{
    public function handle($request, Closure $next)
    {
        $start = microtime(true);
        $startMemory = memory_get_usage(true);
        
        $response = $next($request);
        
        $executionTime = microtime(true) - $start;
        $memoryUsage = memory_get_usage(true) - $startMemory;
        
        if ($executionTime > 1.0) { // Log slow requests
            Log::warning('Slow request detected', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'execution_time' => $executionTime,
                'memory_usage' => $memoryUsage,
                'user_id' => $request->user()?->id
            ]);
        }
        
        return $response;
    }
}
```

This technical architecture document provides comprehensive implementation details for the POS system, covering database design, API specifications, synchronization processes, security measures, and deployment strategies.
