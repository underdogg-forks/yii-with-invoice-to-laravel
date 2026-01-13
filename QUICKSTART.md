# Quick Start Guide - Laravel Invoice Peppol

## Prerequisites Check

Before starting, ensure you have:
- ✅ PHP 8.3 or higher (`php -v`)
- ✅ Composer (`composer --version`)
- ✅ MySQL 5.7+ or MariaDB 10.3+ (or SQLite for testing)

## Installation Steps

### 1. Install Dependencies

```bash
composer install
```

**Note:** If you encounter GitHub API rate limits during `composer install`, you may need to create a GitHub personal access token. See: https://github.com/settings/tokens

### 2. Configure Environment

```bash
cp .env.example .env
```

Edit `.env` and configure your database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=invoice_peppol
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

**For SQLite (simpler, for testing):**
```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

### 3. Generate Application Key

```bash
php artisan key:generate
```

### 4. Create Database

**MySQL:**
```sql
CREATE DATABASE invoice_peppol CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**SQLite:**
```bash
touch database/database.sqlite
```

### 5. Run Migrations

```bash
php artisan migrate
```

This will create the following tables:
- `migrations` (tracking)
- `clients` (customer data)
- `units` (measurement units)
- `invoices` (invoice records)
- `client_peppol` (Peppol client configuration)
- `payment_peppol` (Peppol payment tracking)
- `unit_peppol` (Peppol unit codes)

### 6. Seed Database (Optional but Recommended)

```bash
php artisan db:seed
```

This creates sample data:
- 10 clients
- 5 units
- 20 invoices
- Peppol data for 5 clients
- Peppol data for all units
- Peppol payment data for 10 invoices

### 7. Start Development Server

```bash
php artisan serve
```

The application will be available at: **http://localhost:8000**

## Testing the Application

### Access the Application

Open your browser and navigate to:
- **Home:** http://localhost:8000
- **Client Peppol:** http://localhost:8000/clientpeppol
- **Payment Peppol:** http://localhost:8000/paymentpeppol
- **Unit Peppol:** http://localhost:8000/unitpeppol

### Run Tests

```bash
# Run all tests
vendor/bin/phpunit -c phpunit-laravel.xml

# Run only unit tests
vendor/bin/phpunit -c phpunit-laravel.xml --testsuite=Unit

# Run only feature tests
vendor/bin/phpunit -c phpunit-laravel.xml --testsuite=Feature

# Verbose output
vendor/bin/phpunit -c phpunit-laravel.xml --testdox
```

Expected output:
```
PHPUnit 11.x.x

Tests\Unit\ClientPeppolDTOTest
 ✔ Can create client peppol dto
 ✔ Can convert dto to array

Tests\Unit\ClientPeppolServiceTest
 ✔ Can get by id
 ✔ Can create
 ✔ Can delete

Tests\Feature\ClientPeppolTest
 ✔ Can create client peppol
 ✔ Client peppol belongs to client
 ✔ Can update client peppol
 ✔ Can delete client peppol

Time: XX.XXX seconds
OK (9 tests, XX assertions)
```

## Exploring Peppol Features

### Client Peppol

1. Navigate to http://localhost:8000/clientpeppol
2. Click "Add New Client Peppol"
3. Fill in the Peppol-specific fields:
   - Endpoint ID (email format)
   - Endpoint ID Scheme ID (4 characters)
   - Tax scheme information
   - Legal entity details
   - Financial institution branch
   - Buyer reference

### Payment Peppol

1. Navigate to http://localhost:8000/paymentpeppol
2. Click "Add New Payment Peppol"
3. Select provider (e.g., StoreCove, Ecosio, Peppol)
4. Auto-reference will be generated automatically

### Unit Peppol

1. Navigate to http://localhost:8000/unitpeppol
2. Click "Add New Unit Peppol"
3. Enter UN/CEFACT unit code (3 characters)
4. Provide name and description

## Common Issues and Solutions

### Issue: "Class not found" errors

**Solution:**
```bash
composer dump-autoload
```

### Issue: "No such file or directory" for storage

**Solution:**
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Issue: Database connection refused

**Solutions:**
1. Verify MySQL/MariaDB is running
2. Check credentials in `.env`
3. Ensure database exists
4. Try: `php artisan config:clear`

### Issue: CSRF token mismatch

**Solution:**
```bash
php artisan cache:clear
php artisan config:clear
```

### Issue: Migration already exists

**Solution:**
```bash
php artisan migrate:fresh
# Or to keep data:
php artisan migrate:refresh
```

## Development Workflow

### Making Changes

1. **Models:** Edit files in `app/Models/`
2. **Controllers:** Edit files in `app/Http/Controllers/`
3. **Views:** Edit files in `resources/views/`
4. **Routes:** Edit `routes/web.php`

### Creating New Migrations

```bash
php artisan make:migration create_table_name
```

### Creating New Models

```bash
php artisan make:model ModelName
```

### Creating New Controllers

```bash
php artisan make:controller ControllerName
```

## Artisan Commands Reference

```bash
# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# View routes
php artisan route:list

# Run specific migration
php artisan migrate --path=/database/migrations/filename.php

# Rollback migrations
php artisan migrate:rollback

# Fresh migration (drops all tables)
php artisan migrate:fresh

# Fresh migration with seeding
php artisan migrate:fresh --seed

# Run specific seeder
php artisan db:seed --class=ClientPeppolSeeder
```

## Project Structure Overview

```
├── app/
│   ├── DTOs/              # Data Transfer Objects
│   ├── Http/Controllers/  # HTTP Controllers
│   ├── Models/            # Eloquent Models
│   ├── Repositories/      # Data Access Layer
│   ├── Services/          # Business Logic
│   └── Providers/         # Service Providers
├── config/                # Configuration files
├── database/
│   ├── factories/         # Model Factories
│   ├── migrations/        # Database Migrations
│   └── seeders/          # Database Seeders
├── resources/views/       # Plain PHP Views
├── routes/                # Route definitions
├── storage/               # File storage
├── tests/                 # Tests
└── public/                # Public web root
```

## Next Steps

1. ✅ Application is running
2. ✅ Database is set up
3. ✅ Sample data is loaded
4. ✅ Tests are passing

### Optional Enhancements:
- Convert views to Blade templates
- Add authentication
- Implement API endpoints
- Add advanced validation
- Integrate UBL XML generation
- Add PDF export for invoices

## Getting Help

- **Laravel Documentation:** https://laravel.com/docs/12.x
- **Peppol Documentation:** https://peppol.eu/
- **Project README:** See README-LARAVEL.md
- **Migration Summary:** See MIGRATION-SUMMARY.md

## Status Check Commands

```bash
# Check PHP version
php -v

# Check Laravel version
php artisan --version

# Check database connection
php artisan db:show

# List all routes
php artisan route:list

# Check migrations status
php artisan migrate:status
```

Enjoy your Laravel Invoice Peppol application! 🚀
