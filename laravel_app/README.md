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

Create the app and testing databases (start MySQL in the XAMPP control panel first):

```bash
mysql -u root -e "CREATE DATABASE abdalrahim_task_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -e "CREATE DATABASE abdalrahim_task_app_testing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Then run the migrations and load the sample data:

```bash
php artisan migrate --seed
```

To rebuild from scratch at any time (drops every table first):

```bash
php artisan migrate:fresh --seed
```

## Database schema

The migrations reproduce the Week 4 schema (`schema.sql`), including its constraint names:

| Table        | Rules enforced by the database                                                                                   |
|--------------|------------------------------------------------------------------------------------------------------------------|
| `users`      | `uq_users_email`; name and email not blank                                                                       |
| `labels`     | `uq_labels_name`; name not blank                                                                                 |
| `tasks`      | title not blank; priority in `low`/`medium`/`high`; `done` requires `completed_at`; `fk_tasks_user` → `SET NULL` on delete, `CASCADE` on update; index `idx_tasks_user_done` |
| `label_task` | composite primary key (`label_id`, `task_id`); both foreign keys `CASCADE` on delete and update; index `idx_label_task_task` |

The CHECK rules are raw MySQL/MariaDB statements, so the migrations target
MySQL/MariaDB only. Every migration is reversible with `php artisan migrate:rollback`.

## Run

```bash
php artisan serve
```

The app is served at <http://localhost:8000>. The health endpoint is `/up`.

## Test

```bash
php artisan test
```

Tests run against the `abdalrahim_task_app_testing` MySQL database (configured in
`phpunit.xml`), so MySQL must be running. The schema tests in
`tests/Feature/DatabaseSchemaTest.php` rebuild that database and prove each constraint
rejects invalid data; SQLite cannot hold the CHECK rules, so it is not used.

## Conventions

- **Route files hold no business logic.** `routes/web.php` only maps URIs to
  controller actions; the work lives in `app/Http/Controllers`.
- Session, cache and queue default to file/sync drivers so a fresh clone boots
  before any migration has run. Switch them to `database` in `.env` once the
  schema is migrated.
