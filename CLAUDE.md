# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel-based email campaign management system for Hotel Grand Pilatus, built with MongoDB and React frontend. The system handles prospect management, campaign creation, landing pages, and campaign tracking with external API integrations (ERP, Küba AG).

## Development Commands

### Setup & Development
```bash
# Install dependencies
composer install
npm install

# Start development server (with queue, logs, and Vite)
composer dev

# Run database migrations
php artisan migrate

# Import prospects from external APIs
php artisan import:prospects
```

### Code Quality & Testing
```bash
# Run all tests and checks
composer test

# Fix code style and refactor
composer fix

# Individual quality commands
composer lint                    # Format code with Pint
composer test:lint               # Test formatting without changes
composer refactor                # Apply Rector refactoring
composer test:refactor          # Test refactoring without changes
composer test:types             # Run PHPStan static analysis
composer test:unit              # Run Pest unit tests with coverage
composer test:type-coverage     # Ensure minimum 50% type coverage
composer test:unit-coverage     # Require exactly 100% test coverage
```

### Frontend Development
```bash
npm run dev                     # Start Vite development server
npm run build                   # Build for production
```

## Architecture

### Core Models & Relationships
- **Campaign**: Main campaign entity with status enum, filters, and soft deletes
- **Landingpage**: One-to-one with Campaign, contains dynamic content
- **Prospect**: Customer data from ERP/Küba APIs with extensive filtering capabilities
- **CampaignTracking**: UTM tracking, device detection, and analytics
- **User**: Authentication with Sanctum and role-based access

### Key Features
- **Generic Filtering System**: `HasFilterable` trait with automatic filter generation for enum/range attributes
- **Data Transfer Objects**: Spatie Laravel Data for ERP and Küba API integration
- **Campaign Tracking**: Comprehensive UTM parameter tracking with device/browser detection
- **MongoDB Integration**: Using Laravel MongoDB for flexible document storage
- **API Resources**: Structured JSON responses for frontend consumption

### Important Traits & Services
- `HasFilterable`: Provides dynamic filtering capabilities with automatic search criteria generation
- `CampaignTrackingService`: Handles landing page visit tracking and analytics
- `HasEnumHelpers`: Utility methods for enum handling

### Authentication & Authorization
- Laravel Sanctum for API authentication
- Policy-based authorization for all models
- Role-based access control (Admin vs Prospect)

### External Integrations
- ERP API for customer data import
- Küba AG API for additional prospect sources
- Mailtrap for email campaign testing and delivery

### Development Standards
- Strict typing enabled (`declare(strict_types=1)`)
- Comprehensive PHPDoc annotations for model properties
- 100% test coverage requirement
- Laravel best practices and conventions
- Rector for automated refactoring
- Pint for code formatting