# Installation & Deployment Guide
## POS System Setup Guide

### Version: 1.0
### Date: 11 Juli 2025

---

## Table of Contents
1. [System Requirements](#system-requirements)
2. [Backend Installation](#backend-installation)
3. [Frontend Installation](#frontend-installation)
4. [Database Setup](#database-setup)
5. [Configuration](#configuration)
6. [Deployment](#deployment)
7. [Monitoring & Maintenance](#monitoring--maintenance)
8. [Troubleshooting](#troubleshooting)

---

## System Requirements

### Backend Server Requirements
- **Operating System**: Ubuntu 20.04+ / CentOS 8+ / Windows Server 2019+
- **Web Server**: Nginx 1.18+ / Apache 2.4+
- **PHP**: 8.2+ with extensions:
  - BCMath
  - Ctype
  - Fileinfo
  - JSON
  - Mbstring
  - OpenSSL
  - PDO
  - Tokenizer
  - XML
  - cURL
  - GD
  - Redis
- **Database**: MySQL 8.0+ / MariaDB 10.5+
- **Cache**: Redis 6.0+
- **Queue**: Redis / Database
- **Memory**: Minimum 4GB RAM (8GB+ recommended)
- **Storage**: Minimum 50GB SSD
- **Network**: Stable internet connection

### Frontend (Store) Requirements
- **Operating System**: Windows 10+ / Ubuntu 18.04+ / macOS 10.15+
- **PHP**: 8.2+ with SQLite extension
- **Database**: SQLite 3.35+
- **Web Browser**: Chrome 90+ / Firefox 88+ / Safari 14+
- **Memory**: Minimum 2GB RAM (4GB+ recommended)
- **Storage**: Minimum 10GB available space
- **Hardware**: 
  - Barcode scanner (USB/Bluetooth)
  - Receipt printer (ESC/POS compatible)
  - Cash drawer (optional)
  - Payment terminal (optional)

---

## Backend Installation

### 1. Server Preparation

#### Ubuntu/Debian
```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y \
    nginx \
    mysql-server \
    redis-server \
    php8.2 \
    php8.2-fpm \
    php8.2-mysql \
    php8.2-redis \
    php8.2-xml \
    php8.2-gd \
    php8.2-curl \
    php8.2-mbstring \
    php8.2-zip \
    php8.2-bcmath \
    composer \
    git \
    supervisor \
    certbot \
    python3-certbot-nginx

# Start and enable services
sudo systemctl start nginx mysql redis-server php8.2-fpm
sudo systemctl enable nginx mysql redis-server php8.2-fpm
```

#### CentOS/RHEL
```bash
# Install EPEL and Remi repositories
sudo dnf install epel-release -y
sudo dnf install https://rpms.remirepo.net/enterprise/remi-release-8.rpm -y

# Enable PHP 8.2
sudo dnf module enable php:remi-8.2 -y

# Install packages
sudo dnf install -y \
    nginx \
    mysql-server \
    redis \
    php \
    php-fpm \
    php-mysqlnd \
    php-redis \
    php-xml \
    php-gd \
    php-curl \
    php-mbstring \
    php-zip \
    php-bcmath \
    composer \
    git \
    supervisor

# Start services
sudo systemctl start nginx mysqld redis php-fpm
sudo systemctl enable nginx mysqld redis php-fpm
```

### 2. Download and Setup Application

```bash
# Create application directory
sudo mkdir -p /var/www/pos-backend
cd /var/www/pos-backend

# Clone repository (replace with your actual repository)
sudo git clone https://github.com/yourcompany/pos-backend.git .

# Set proper ownership
sudo chown -R www-data:www-data /var/www/pos-backend

# Switch to www-data user for remaining commands
sudo -u www-data bash

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Set proper permissions
chmod -R 755 storage bootstrap/cache
chmod -R 777 storage/logs storage/framework
```

### 3. Database Configuration

```bash
# Secure MySQL installation
sudo mysql_secure_installation

# Create database and user
sudo mysql -u root -p
```

```sql
-- In MySQL console
CREATE DATABASE pos_backend CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'pos_user'@'localhost' IDENTIFIED BY 'SecurePassword123!';
GRANT ALL PRIVILEGES ON pos_backend.* TO 'pos_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 4. Environment Configuration

Edit `/var/www/pos-backend/.env`:

```bash
APP_NAME="POS Backend"
APP_ENV=production
APP_KEY=base64:your_generated_key_here
APP_DEBUG=false
APP_URL=https://api.yourpos.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_backend
DB_USERNAME=pos_user
DB_PASSWORD=SecurePassword123!

BROADCAST_DRIVER=redis
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls

# JWT Configuration
JWT_SECRET=your_jwt_secret_key
JWT_TTL=60

# File Storage
FILESYSTEM_DISK=local
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

# Rate Limiting
API_RATE_LIMIT=1000
SYNC_RATE_LIMIT=100

# Backup Configuration
BACKUP_DATABASE=true
BACKUP_FILES=false
BACKUP_DESTINATION=s3
```

### 5. Database Migration

```bash
# Run database migrations
php artisan migrate

# Seed initial data
php artisan db:seed

# Create storage link
php artisan storage:link
```

### 6. Nginx Configuration

Create `/etc/nginx/sites-available/pos-backend`:

```nginx
server {
    listen 80;
    server_name api.yourpos.com;
    root /var/www/pos-backend/public;
    index index.php index.html;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # Gzip compression
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;

    # File upload size
    client_max_body_size 100M;

    # Handle Laravel routes
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM configuration
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        
        # Security
        fastcgi_hide_header X-Powered-By;
        fastcgi_read_timeout 300;
    }

    # Deny access to sensitive files
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static files
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Block access to hidden files
    location ~ /\. {
        deny all;
    }
}
```

Enable the site:
```bash
sudo ln -s /etc/nginx/sites-available/pos-backend /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 7. SSL Certificate

```bash
# Install SSL certificate using Let's Encrypt
sudo certbot --nginx -d api.yourpos.com

# Auto-renewal
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

### 8. Queue Worker Setup

Create `/etc/supervisor/conf.d/laravel-worker.conf`:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/pos-backend/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=8
redirect_stderr=true
stdout_logfile=/var/www/pos-backend/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

### 9. Scheduled Tasks

Add to crontab:
```bash
sudo crontab -e -u www-data
# Add: * * * * * cd /var/www/pos-backend && php artisan schedule:run >> /dev/null 2>&1
```

---

## Frontend Installation

### 1. Download Application

```bash
# Create application directory
mkdir -p /var/www/pos-frontend
cd /var/www/pos-frontend

# Clone repository (replace with your actual repository)
git clone https://github.com/yourcompany/pos-frontend.git .

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node.js dependencies
npm install
npm run production
```

### 2. Database Setup

```bash
# Create SQLite database
touch database/pos_store.sqlite

# Set permissions
chmod 664 database/pos_store.sqlite
chmod 775 database/

# Run migrations
php artisan migrate

# Seed initial data
php artisan db:seed
```

### 3. Environment Configuration

Create `.env` file:

```bash
APP_NAME="POS Frontend"
APP_ENV=production
APP_KEY=base64:your_generated_key_here
APP_DEBUG=false
APP_URL=http://localhost:8000

LOG_CHANNEL=daily
LOG_LEVEL=info

DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/pos_store.sqlite

# Backend API Configuration
BACKEND_API_URL=https://api.yourpos.com
BACKEND_API_TOKEN=your_api_token_here

# Store Configuration
STORE_ID=1
STORE_CODE=ST001
STORE_NAME="Main Store"

# Sync Configuration
SYNC_ENABLED=true
SYNC_INTERVAL=3600
OFFLINE_MODE_ENABLED=true
AUTO_SYNC_ON_STARTUP=true

# Hardware Configuration
BARCODE_SCANNER_ENABLED=true
RECEIPT_PRINTER_ENABLED=true
CASH_DRAWER_ENABLED=false

# Receipt Configuration
RECEIPT_PRINTER_NAME="POS-58"
RECEIPT_WIDTH=58
RECEIPT_LOGO_PATH=null

# Cache Configuration
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database

# Security
SESSION_LIFETIME=120
BCRYPT_ROUNDS=12
```

### 4. Web Server Setup (Development)

For development/testing:
```bash
# Start PHP built-in server
php artisan serve --host=0.0.0.0 --port=8000
```

For production, use Apache or Nginx:

#### Apache Configuration
Create `/etc/apache2/sites-available/pos-frontend.conf`:

```apache
<VirtualHost *:80>
    ServerName pos-store.local
    DocumentRoot /var/www/pos-frontend/public
    
    <Directory /var/www/pos-frontend/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/pos-frontend_error.log
    CustomLog ${APACHE_LOG_DIR}/pos-frontend_access.log combined
</VirtualHost>
```

```bash
sudo a2ensite pos-frontend
sudo a2enmod rewrite
sudo systemctl reload apache2
```

### 5. Hardware Integration

#### Barcode Scanner Setup
```bash
# Install USB HID support
sudo apt install libhidapi-dev

# Configure scanner permissions
echo 'SUBSYSTEM=="hidraw", ATTRS{idVendor}=="your_vendor_id", MODE="0666"' | sudo tee /etc/udev/rules.d/99-barcode-scanner.rules
sudo udevadm control --reload-rules
```

#### Receipt Printer Setup
```bash
# Install CUPS (if not already installed)
sudo apt install cups

# Add printer (ESC/POS thermal printer)
sudo lpadmin -p POS-58 -E -v usb://path/to/printer -m everywhere

# Test print
echo "Test Print" | lp -d POS-58
```

### 6. Synchronization Setup

```bash
# Create sync command
php artisan make:command SyncData

# Add to crontab for auto-sync
crontab -e
# Add: 0 * * * * cd /var/www/pos-frontend && php artisan sync:data >> /dev/null 2>&1
```

---

## Database Setup

### Backend Database Schema

```sql
-- Create optimized indexes
CREATE INDEX idx_transactions_store_date ON transactions(store_id, transaction_date);
CREATE INDEX idx_transaction_details_product ON transaction_details(product_id);
CREATE INDEX idx_customers_search ON customers(name, email, phone);
CREATE INDEX idx_products_search ON products(name, sku, barcode);
CREATE INDEX idx_sync_logs_store_status ON sync_logs(store_id, status);

-- Create views for reporting
CREATE VIEW v_daily_sales AS
SELECT 
    store_id,
    DATE(transaction_date) as sale_date,
    COUNT(*) as transaction_count,
    SUM(total_amount) as total_sales,
    SUM(tax_amount) as total_tax,
    SUM(discount_amount) as total_discount
FROM transactions 
WHERE payment_status = 'paid'
GROUP BY store_id, DATE(transaction_date);

CREATE VIEW v_product_performance AS
SELECT 
    p.id,
    p.name,
    p.sku,
    SUM(td.quantity) as total_sold,
    SUM(td.total_price) as total_revenue,
    COUNT(DISTINCT t.id) as transaction_count
FROM products p
LEFT JOIN transaction_details td ON p.id = td.product_id
LEFT JOIN transactions t ON td.transaction_id = t.id
WHERE t.payment_status = 'paid'
GROUP BY p.id, p.name, p.sku;
```

### Frontend Database Schema

```sql
-- SQLite optimizations
PRAGMA journal_mode = WAL;
PRAGMA synchronous = NORMAL;
PRAGMA cache_size = 1000;
PRAGMA temp_store = memory;

-- Create triggers for auto-sync queue
CREATE TRIGGER tr_transactions_sync 
AFTER INSERT ON transactions
BEGIN
    INSERT INTO sync_queue (table_name, record_id, action, data_json)
    VALUES ('transactions', NEW.id, 'insert', json_object(
        'id', NEW.id,
        'transaction_number', NEW.transaction_number,
        'customer_id', NEW.customer_id,
        'cashier_id', NEW.cashier_id,
        'transaction_date', NEW.transaction_date,
        'subtotal', NEW.subtotal,
        'tax_amount', NEW.tax_amount,
        'discount_amount', NEW.discount_amount,
        'total_amount', NEW.total_amount,
        'payment_method', NEW.payment_method,
        'payment_status', NEW.payment_status
    ));
END;

-- Backup commands
CREATE TABLE IF NOT EXISTS backup_log (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    backup_file VARCHAR(255),
    backup_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    file_size INTEGER,
    status VARCHAR(20) DEFAULT 'completed'
);
```

---

## Configuration

### Performance Optimization

#### PHP Configuration (`/etc/php/8.2/fpm/php.ini`)
```ini
; Memory and execution limits
memory_limit = 512M
max_execution_time = 300
max_input_time = 300

; File upload limits
upload_max_filesize = 100M
post_max_size = 100M

; OPcache optimization
opcache.enable = 1
opcache.memory_consumption = 256
opcache.interned_strings_buffer = 16
opcache.max_accelerated_files = 10000
opcache.validate_timestamps = 0
opcache.save_comments = 1
opcache.fast_shutdown = 1

; Session configuration
session.driver = redis
session.connection = default
session.store = redis
```

#### MySQL Configuration (`/etc/mysql/mysql.conf.d/mysqld.cnf`)
```ini
[mysqld]
# Basic settings
bind-address = 127.0.0.1
port = 3306

# Performance settings
innodb_buffer_pool_size = 2G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
innodb_file_per_table = 1

# Connection settings
max_connections = 200
connect_timeout = 10
wait_timeout = 600
interactive_timeout = 600

# Query cache
query_cache_type = 1
query_cache_size = 256M
query_cache_limit = 2M

# Slow query log
slow_query_log = 1
slow_query_log_file = /var/log/mysql/slow.log
long_query_time = 2
```

#### Redis Configuration (`/etc/redis/redis.conf`)
```conf
# Memory optimization
maxmemory 1gb
maxmemory-policy allkeys-lru

# Persistence
save 900 1
save 300 10
save 60 10000

# Network
bind 127.0.0.1
port 6379
timeout 300

# Security
requirepass your_redis_password

# Logging
loglevel notice
logfile /var/log/redis/redis-server.log
```

---

## Deployment

### Production Deployment with Docker

#### Docker Compose Configuration

Create `docker-compose.prod.yml`:

```yaml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile.prod
    container_name: pos_backend
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
      - ./storage:/var/www/storage
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
    networks:
      - pos_network
    depends_on:
      - mysql
      - redis

  nginx:
    image: nginx:alpine
    container_name: pos_nginx
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./:/var/www
      - ./docker/nginx/prod.conf:/etc/nginx/conf.d/default.conf
      - ./docker/ssl:/etc/nginx/ssl
    networks:
      - pos_network
    depends_on:
      - app

  mysql:
    image: mysql:8.0
    container_name: pos_mysql
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: ${DB_DATABASE}
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD}
      MYSQL_USER: ${DB_USERNAME}
      MYSQL_PASSWORD: ${DB_PASSWORD}
    volumes:
      - mysql_data:/var/lib/mysql
      - ./docker/mysql/my.cnf:/etc/mysql/conf.d/my.cnf
    ports:
      - "3306:3306"
    networks:
      - pos_network

  redis:
    image: redis:alpine
    container_name: pos_redis
    restart: unless-stopped
    command: redis-server --requirepass ${REDIS_PASSWORD}
    volumes:
      - redis_data:/data
    networks:
      - pos_network

  worker:
    build:
      context: .
      dockerfile: Dockerfile.prod
    container_name: pos_worker
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
    command: php artisan queue:work redis --sleep=3 --tries=3
    networks:
      - pos_network
    depends_on:
      - mysql
      - redis

  scheduler:
    build:
      context: .
      dockerfile: Dockerfile.prod
    container_name: pos_scheduler
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
    command: php artisan schedule:work
    networks:
      - pos_network
    depends_on:
      - mysql
      - redis

networks:
  pos_network:
    driver: bridge

volumes:
  mysql_data:
    driver: local
  redis_data:
    driver: local
```

#### Production Dockerfile

Create `Dockerfile.prod`:

```dockerfile
FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    mysql-client \
    supervisor

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev --no-scripts

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Copy supervisor configuration
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose port
EXPOSE 9000

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
```

### CI/CD Pipeline (GitHub Actions)

Create `.github/workflows/deploy.yml`:

```yaml
name: Deploy to Production

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: 8.2
        extensions: mbstring, xml, ctype, iconv, intl, pdo, pdo_mysql, dom, filter, gd, json
    
    - name: Install dependencies
      run: composer install --prefer-dist --no-progress --no-dev --optimize-autoloader
    
    - name: Run tests
      run: php artisan test
    
    - name: Deploy to server
      uses: appleboy/ssh-action@v0.1.7
      with:
        host: ${{ secrets.HOST }}
        username: ${{ secrets.USERNAME }}
        key: ${{ secrets.SSH_KEY }}
        script: |
          cd /var/www/pos-backend
          git pull origin main
          composer install --no-dev --optimize-autoloader
          php artisan migrate --force
          php artisan config:cache
          php artisan route:cache
          php artisan view:cache
          sudo supervisorctl restart laravel-worker:*
          sudo systemctl reload nginx
```

---

## Monitoring & Maintenance

### System Monitoring

#### Log Monitoring Script

Create `/usr/local/bin/pos-monitor.sh`:

```bash
#!/bin/bash

LOG_DIR="/var/www/pos-backend/storage/logs"
EMAIL="admin@yourpos.com"
THRESHOLD_ERROR_COUNT=10

# Check for errors in the last hour
ERROR_COUNT=$(grep -c "ERROR" "$LOG_DIR/laravel-$(date +%Y-%m-%d).log" 2>/dev/null || echo 0)

if [ "$ERROR_COUNT" -gt "$THRESHOLD_ERROR_COUNT" ]; then
    echo "High error count detected: $ERROR_COUNT errors in the last hour" | \
    mail -s "POS System Alert: High Error Count" "$EMAIL"
fi

# Check disk space
DISK_USAGE=$(df /var/www | awk 'NR==2 {print $5}' | sed 's/%//')
if [ "$DISK_USAGE" -gt 80 ]; then
    echo "Disk usage is at $DISK_USAGE%" | \
    mail -s "POS System Alert: High Disk Usage" "$EMAIL"
fi

# Check MySQL connection
if ! mysqladmin ping -h localhost --silent; then
    echo "MySQL is not responding" | \
    mail -s "POS System Alert: MySQL Down" "$EMAIL"
fi

# Check Redis connection
if ! redis-cli ping > /dev/null 2>&1; then
    echo "Redis is not responding" | \
    mail -s "POS System Alert: Redis Down" "$EMAIL"
fi
```

Add to crontab:
```bash
sudo crontab -e
# Add: */5 * * * * /usr/local/bin/pos-monitor.sh
```

### Backup Strategy

#### Automated Backup Script

Create `/usr/local/bin/pos-backup.sh`:

```bash
#!/bin/bash

BACKUP_DIR="/backup/pos"
DATE=$(date +%Y%m%d_%H%M%S)
MYSQL_USER="backup_user"
MYSQL_PASSWORD="backup_password"
DATABASE="pos_backend"

# Create backup directory
mkdir -p "$BACKUP_DIR/$DATE"

# Database backup
mysqldump -u $MYSQL_USER -p$MYSQL_PASSWORD $DATABASE | gzip > "$BACKUP_DIR/$DATE/database.sql.gz"

# Files backup
tar -czf "$BACKUP_DIR/$DATE/files.tar.gz" /var/www/pos-backend/storage/app

# Redis backup
cp /var/lib/redis/dump.rdb "$BACKUP_DIR/$DATE/redis.rdb"

# Remove backups older than 30 days
find "$BACKUP_DIR" -type d -mtime +30 -exec rm -rf {} \;

# Upload to cloud storage (optional)
# aws s3 sync "$BACKUP_DIR/$DATE" "s3://your-backup-bucket/pos/$DATE"
```

### Performance Monitoring

#### Application Performance Monitoring

Add to Laravel application (`app/Http/Middleware/PerformanceMonitoring.php`):

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PerformanceMonitoring
{
    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);
        $startMemory = memory_get_usage(true);
        
        $response = $next($request);
        
        $executionTime = microtime(true) - $start;
        $memoryUsage = memory_get_usage(true) - $startMemory;
        
        // Log slow requests
        if ($executionTime > 1.0) {
            Log::warning('Slow request detected', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'execution_time' => $executionTime,
                'memory_usage' => $memoryUsage,
                'user_id' => $request->user()?->id
            ]);
        }
        
        // Log high memory usage
        if ($memoryUsage > 50 * 1024 * 1024) { // 50MB
            Log::warning('High memory usage detected', [
                'url' => $request->fullUrl(),
                'memory_usage' => $memoryUsage,
                'execution_time' => $executionTime
            ]);
        }
        
        return $response;
    }
}
```

---

## Troubleshooting

### Common Issues and Solutions

#### 1. Database Connection Issues

**Problem**: Cannot connect to MySQL database
```
SQLSTATE[HY000] [2002] Connection refused
```

**Solution**:
```bash
# Check MySQL status
sudo systemctl status mysql

# Start MySQL if stopped
sudo systemctl start mysql

# Check MySQL logs
sudo tail -f /var/log/mysql/error.log

# Test connection
mysql -u pos_user -p pos_backend
```

#### 2. Redis Connection Issues

**Problem**: Redis connection failed
```
Connection refused [tcp://127.0.0.1:6379]
```

**Solution**:
```bash
# Check Redis status
sudo systemctl status redis

# Start Redis if stopped
sudo systemctl start redis

# Test Redis connection
redis-cli ping

# Check Redis configuration
sudo nano /etc/redis/redis.conf
```

#### 3. Permission Issues

**Problem**: Permission denied errors
```
file_put_contents(/var/www/pos-backend/storage/logs/laravel.log): failed to open stream: Permission denied
```

**Solution**:
```bash
# Fix ownership
sudo chown -R www-data:www-data /var/www/pos-backend

# Fix permissions
sudo chmod -R 755 /var/www/pos-backend
sudo chmod -R 777 /var/www/pos-backend/storage
sudo chmod -R 777 /var/www/pos-backend/bootstrap/cache
```

#### 4. Queue Worker Issues

**Problem**: Queue jobs not processing
```
Queue worker not responding
```

**Solution**:
```bash
# Check supervisor status
sudo supervisorctl status

# Restart workers
sudo supervisorctl restart laravel-worker:*

# Check worker logs
tail -f /var/www/pos-backend/storage/logs/worker.log

# Clear failed jobs
php artisan queue:failed
php artisan queue:flush
```

#### 5. SSL Certificate Issues

**Problem**: SSL certificate expired or invalid

**Solution**:
```bash
# Check certificate status
sudo certbot certificates

# Renew certificate
sudo certbot renew

# Test auto-renewal
sudo certbot renew --dry-run
```

#### 6. Frontend Sync Issues

**Problem**: Data not syncing between frontend and backend

**Solution**:
```bash
# Check sync logs
tail -f storage/logs/sync.log

# Test API connection
curl -H "Authorization: Bearer token" https://api.yourpos.com/api/sync/status/1

# Clear sync queue
php artisan sync:clear-queue

# Force sync
php artisan sync:force
```

### Debug Commands

```bash
# Check system status
php artisan pos:system-check

# Clear all caches
php artisan optimize:clear

# Recreate optimized files
php artisan optimize

# Check database connectivity
php artisan tinker
>>> DB::connection()->getPdo();

# Test email configuration
php artisan tinker
>>> Mail::raw('Test email', function($msg) { $msg->to('test@example.com')->subject('Test'); });

# Monitor real-time logs
tail -f storage/logs/laravel.log

# Check queue status
php artisan queue:monitor
```

### Support Information

**Technical Support**: tech-support@yourpos.com  
**Emergency Contact**: +62-XXX-XXXX-XXXX  
**Documentation**: https://docs.yourpos.com  
**Status Page**: https://status.yourpos.com  

---

**Document Version:** 1.0  
**Last Updated:** 11 Juli 2025  
**Maintained by:** DevOps Team
