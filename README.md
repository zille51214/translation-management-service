# Translation Management Service

## Overview

TLaravel-based API service develped to manage translations for multiple locales. It provides tagging, searching, and exporting translations efficiently, with main focus on performance and scalability.

---

## Features

* Store translations for multiple locales (en, fr, es, etc.)
* Tag translations (mobile, web, desktop)
* CRUD operations for translations
* Search by key, locale, and tags
* JSON export endpoint for frontend applications
* Optimized for handling 100k+ records
* Token-based authentication (Laravel Sanctum)

---

## Tech Stack

* Laravel
* MySQL
* Laravel Sanctum (Authentication)

---

## Architecture

The project follows clean architecture principles:

* **Repository Pattern** → Handles database queries
* **Service Layer** → Business logic (e.g., caching export)
* **Controller** → Thin layer for request handling

This ensures scalability, maintainability, and testability.


## ⚡ Performance Optimizations

* Indexed database columns (`key`, `locale`)
* Unique constraints to avoid duplicates
* Optimized queries using `pluck()`
* Pagination for large datasets
* Caching applied on export endpoint

---

## Authentication

API is secured using **Laravel Sanctum**.

---

## 📦 Setup Instructions

```bash
git clone https://github.com/YOUR_USERNAME/translation-management-service.git
cd translation-management-service

composer install
cp .env.example .env
php artisan key:generate
```

### Configure Database in `.env`

Then run:

```bash
php artisan migrate --seed
php artisan serve
```

---

## API Endpoints

### Authentication

* `http://127.0.0.1:8000/api/login`
{
  "email": "test@example.com",
  "password": "password"
}

### Translations

* `GET /api/translations`
* `POST /api/translations`
* `PUT /api/translations/{id}`
* `GET /api/translations/{id}`

### Search Example

```
GET /api/translations?key=welcome&locale=en&tag=mobile
```

### Export

```
GET /api/export?locale=en
```

---

## Testing

Basic feature tests are included.

Run tests:

```bash
php artisan test
```

---

## Database Seeding

* Helps test performance and scalability

---

## Design Decisions

* Used normalized schema for scalability
* Implemented caching for fast export responses (<500ms)
* Followed PSR-12 coding standards
* Applied SOLID principles


## API Documentation with Swagger

This project uses Swagger/OpenAPI documentation for testing and viewing API endpoints.

Swagger UI is available at:

```text
http://127.0.0.1:8000/api/documentation

