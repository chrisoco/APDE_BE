# 📬 Hotel Grand Pilatus – E-Mail Campaign Management System

A modern, responsive web application for managing and analyzing targeted email campaigns for Hotel Grand Pilatus. Built with Laravel, React, and MongoDB.

## 🚀 Features

- Campaign creation with custom landing pages
- Audience segmentation via ERP & Küba APIs
- Scheduled campaign sending with Mailtrap testing
- Prospect interaction tracking & analytics dashboard
- Role-based access (Admin vs. Prospect)
- RESTful API with OpenAPI (Swagger) documentation

## 🛠️ Tech Stack

- **Frontend**: React, Tailwind CSS
- **Backend**: Laravel (PHP)
- **Database**: MongoDB
- **APIs**: Mailtrap, ERP, Küba AG
- **CI/CD**: GitHub Actions, Cloudflare
- **Dev Tools**: Postman, Figma, VSCode/Cursor.ai

## 📂 Structure

- React SPA (tbd)
- `/` – Laravel API
- `/docs` – Project documentation & diagrams

## ✅ Setup (Local Dev)

```bash
# Clone repository
git clone https://github.com/your-repo/hotel-campaign-system.git

# Install Laravel dependencies
composer install
php artisan migrate

# Install frontend dependencies (tbd)
npm install
npm run dev
```

# Static Code Analysis
- `composer test`
- `composer fix`
- [PHPStan](https://phpstan.org) | [Larastan](https://github.com/larastan/larastan)
- [Pest](https://pestphp.com)
- [Pint](https://laravel.com/docs/master/pint)
- [Rector](https://getrector.com)
  - [Rector Laravel](https://github.com/driftingly/rector-laravel)
  - [Laravel Rules](https://github.com/driftingly/rector-laravel/blob/main/docs/rector_rules_overview.md)

## 👥 Roles

- **Marketing Team (Admins)**: Manage campaigns, view analytics
- **Prospects (Guests)**: Receive emails, view landing pages, submit interest

## Conventions
- https://www.conventionalcommits.org/en/v1.0.0/
- https://github.com/alexeymezenin/laravel-best-practices
- https://laravel.com/docs/12.x
- https://laravel.com/docs/12.x/pint
- https://pestphp.com/docs/installation

## MongoDB
- https://www.mongodb.com/docs/drivers/php/laravel-mongodb/current/quick-start/
- https://docs.orbstack.dev/quick-start
- https://www.mongodb.com/docs/manual/tutorial/install-mongodb-community-with-docker/#std-label-docker-mongodb-community-install

## Authentication
- https://www.mongodb.com/docs/drivers/php/laravel-mongodb/current/user-authentication/
- https://laravel.com/docs/12.x/sanctum
- (tbd) https://laravel.com/docs/12.x/sanctum#spa-authentication
- (tbd) https://www.mongodb.com/docs/drivers/php/laravel-mongodb/current/user-authentication/#create-the-user-controller