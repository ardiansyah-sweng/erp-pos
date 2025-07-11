# 🏪 ERP-POS System

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

## 📋 About ERP-POS System

ERP-POS adalah sistem Point of Sales terintegrasi dengan arsitektur hybrid yang menggabungkan backend server terpusat (MySQL) dan frontend lokal di setiap toko (SQLite). Sistem ini dirancang untuk mendukung operasional multi-toko dengan kemampuan offline dan sinkronisasi data otomatis.

## ✨ Key Features

- **🏪 Multi-Store Management**: Kelola multiple toko dari satu dashboard terpusat
- **💰 Advanced POS Operations**: Transaksi lengkap dengan barcode scanner, multiple payment methods
- **📊 Real-time Analytics**: Dashboard analytics dan reporting komprehensif  
- **🔄 Offline Capability**: Operasional tetap berjalan meski tanpa internet
- **👥 Employee Management**: Manajemen karyawan dengan integrasi HRIS (future)
- **📱 Responsive Design**: Interface yang mobile-friendly dan touch-optimized
- **🔐 Security First**: Implementasi security terbaik untuk data transaksi
- **🔄 Auto Sync**: Sinkronisasi data otomatis antar toko

## 🏗️ Architecture

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │   Frontend      │    │   Frontend      │
│   (Toko A)      │    │   (Toko B)      │    │   (Toko C)      │
│   SQLite        │    │   SQLite        │    │   SQLite        │
└─────────┬───────┘    └─────────┬───────┘    └─────────┬───────┘
          │                      │                      │
          └──────────────────────┼──────────────────────┘
                                 │
                    ┌─────────────┴─────────────┐
                    │      Backend Server       │
                    │        MySQL             │
                    │    (Data Warehouse)      │
                    └───────────────────────────┘
```

## 🛠️ Tech Stack

- **Backend**: Laravel 11 + MySQL + Redis
- **Frontend**: Laravel 11 + SQLite + Vue.js/Livewire  
- **Synchronization**: REST API + Queue Jobs
- **Real-time**: WebSocket/Pusher
- **Security**: JWT Authentication + Role-based Access Control

## 🚀 Quick Start

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL 8.0+
- Redis (optional, untuk caching)

### Installation

1. **Clone repository**
```bash
git clone https://github.com/yourusername/erp-pos.git
cd erp-pos
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Database setup**
```bash
# Configure database in .env file
php artisan migrate
php artisan db:seed
```

5. **Build assets**
```bash
npm run build
# or for development
npm run dev
```

6. **Start server**
```bash
php artisan serve
```

## 📖 Documentation

Dokumentasi lengkap tersedia di folder `docs/`:

- **[Product Requirement Document](docs/PRD-POS-System.md)** - Spesifikasi lengkap sistem
- **[Technical Architecture](docs/Technical-Architecture.md)** - Arsitektur teknis detail
- **[API Documentation](docs/API-Documentation.md)** - Dokumentasi REST API
- **[Installation Guide](docs/Installation-Deployment-Guide.md)** - Panduan instalasi & deployment

## 🏪 Project Structure

```
erp-pos/
├── app/
│   ├── Http/Controllers/        # API & Web Controllers
│   ├── Models/                  # Eloquent Models
│   └── Services/               # Business Logic Services
├── database/
│   ├── migrations/             # Database Migrations
│   └── seeders/               # Database Seeders
├── docs/                      # Project Documentation
├── resources/
│   ├── js/                    # Vue.js Components
│   ├── css/                   # Stylesheets
│   └── views/                 # Blade Templates
└── routes/
    ├── web.php                # Web Routes
    └── api.php                # API Routes
```

## 🔧 Development

### Running Tests
```bash
php artisan test
```

### Code Style
```bash
./vendor/bin/pint
```

### Queue Workers
```bash
php artisan queue:work
```

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👥 Team

- **Product Owner**: [Your Name]
- **Tech Lead**: [Your Name]  
- **Backend Developer**: [Your Name]
- **Frontend Developer**: [Your Name]

## 📞 Support

For support and questions:
- 📧 Email: support@yourcompany.com
- 💬 Slack: #erp-pos-support
- 📋 Issues: [GitHub Issues](https://github.com/yourusername/erp-pos/issues)

---

**Made with ❤️ for Indonesian retail businesses**
