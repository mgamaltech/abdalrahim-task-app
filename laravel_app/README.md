# Abdalrahim Task App

Laravel 12 application for the task manager, built on the database design from Week 4
(see [`Design the ERD/`](../Design%20the%20ERD) and
[`Write SQL to create the schema, with the constraints included/`](../Write%20SQL%20to%20create%20the%20schema,%20with%20the%20constraints%20included)
in the repository root).

## Requirements

- PHP 8.2+ with the `zip`, `pdo_mysql`, `mbstring`, `openssl` and `curl` extensions
- Composer 2
- MySQL 8 (XAMPP)

## Setup

```bash
cd laravel_app
composer install
cp .env.example .env
php artisan key:generate
```

Create the database (start MySQL in the XAMPP control panel first):

```bash
mysql -u root -e "CREATE DATABASE abdalrahim_task_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Then run the migrations:

```bash
php artisan migrate
```

## Run

```bash
php artisan serve
```

The app is served at <http://localhost:8000>. The health endpoint is `/up`.

## Test

```bash
php artisan test
```

Tests run against an in-memory SQLite database (configured in `phpunit.xml`), so
MySQL does not need to be running.

## Conventions

- **Route files hold no business logic.** `routes/web.php` only maps URIs to
  controller actions; the work lives in `app/Http/Controllers`.
- Session, cache and queue default to file/sync drivers so a fresh clone boots
  before any migration has run. Switch them to `database` in `.env` once the
  schema is migrated.
