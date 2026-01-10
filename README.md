# BPKAD Pajak DTHRTH API

Backend API untuk sistem BPKAD Pajak DTHRTH.

## Tech Stack

- PHP 8.5
- Laravel 12
- PostgreSQL / MySQL

## Requirements

- PHP >= 8.2
- Composer
- Database (PostgreSQL/MySQL)

## Installation

```bash
# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate

# Run seeders (optional)
php artisan db:seed
```

## Development

```bash
# Run local server
php artisan serve

# Run tests
php artisan test
```

## API Documentation

API documentation available at `/docs/api` (if enabled).
