# Contributing to ERP-POS System

Thank you for your interest in contributing to our POS system! This document provides guidelines and information for contributors.

## 🚀 Development Workflow

### Branch Structure
- **`main`**: Production-ready code only
- **`develop`**: Integration branch for features  
- **`feature/*`**: New features (branch from `develop`)
- **`bugfix/*`**: Bug fixes (branch from `develop`)
- **`hotfix/*`**: Critical fixes (branch from `main`)

### Getting Started
1. Fork the repository
2. Clone your fork: `git clone https://github.com/your-username/erp-pos.git`
3. Create feature branch: `git checkout -b feature/your-feature-name develop`
4. Make your changes
5. Test thoroughly
6. Submit a pull request

## 📋 Development Guidelines

### Code Standards
- Follow PSR-12 coding standards
- Use Laravel best practices
- Write meaningful commit messages
- Add appropriate comments for complex logic

### Testing Requirements
- Write unit tests for new features
- Ensure all existing tests pass
- Test both online and offline scenarios
- Verify multi-store synchronization

### Database Guidelines
- Always create migrations for schema changes
- Include both up() and down() methods
- Add database seeders for test data
- Consider SQLite compatibility for frontend

## 🔧 Local Development Setup

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL 8.0+
- SQLite (for frontend testing)

### Installation
```bash
# Clone and setup
composer install
npm install
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Build assets
npm run dev
```

### Running Tests
```bash
# All tests
php artisan test

# Specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Code style check
./vendor/bin/pint --test
```

## 📝 Pull Request Process

1. **Branch Naming**:
   - Features: `feature/add-barcode-scanner`
   - Bugfixes: `bugfix/fix-transaction-sync`
   - Hotfixes: `hotfix/critical-payment-issue`

2. **Commit Messages**:
   ```
   type(scope): description
   
   Examples:
   feat(pos): add barcode scanner integration
   fix(sync): resolve transaction duplicate issue
   docs(api): update authentication endpoints
   ```

3. **PR Requirements**:
   - Clear description of changes
   - Link to related issues
   - Screenshots for UI changes
   - Test results confirmation

4. **Review Process**:
   - Code review by at least 1 team member
   - All CI checks must pass
   - No merge conflicts
   - Documentation updated if needed

## 🏗️ Architecture Guidelines

### Frontend (Store Application)
- Use SQLite for local storage
- Implement offline-first approach
- Ensure responsive design for POS terminals
- Optimize for touch interfaces

### Backend (Central Server)
- RESTful API design
- Proper error handling and logging
- Database optimization for multi-store queries
- Secure authentication and authorization

### Synchronization
- Handle offline scenarios gracefully
- Implement conflict resolution strategies
- Ensure data consistency across stores
- Monitor sync performance

## 🐛 Bug Reports

When reporting bugs, please include:
- Clear reproduction steps
- Expected vs actual behavior
- Environment details (OS, browser, PHP version)
- Error logs and screenshots
- Store type (frontend/backend) and configuration

## ✨ Feature Requests

For new features, please provide:
- Clear use case and business value
- Detailed functional requirements
- Impact on existing functionality
- Implementation suggestions (if any)

## 📚 Documentation

- Update relevant documentation for new features
- Include code examples in API documentation
- Update installation guides if needed
- Maintain changelog for releases

## 🔒 Security

- Report security vulnerabilities privately
- Follow secure coding practices
- Validate all user inputs
- Implement proper access controls
- Regular dependency updates

## 💬 Communication

- **Issues**: Use GitHub Issues for bugs and feature requests
- **Discussions**: Use GitHub Discussions for general questions
- **Email**: security@yourcompany.com for security issues

## 📄 License

By contributing, you agree that your contributions will be licensed under the same license as the project (MIT License).

---

**Thank you for contributing to ERP-POS! 🙏**
