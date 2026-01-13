# Laravel Invoice - Peppol Application

This application has been migrated from Yii3 to Laravel 12, with a focus on Peppol (Pan-European Public Procurement OnLine) functionality.

## Requirements

- PHP 8.3 or higher
- Composer
- MySQL 5.7+ or MariaDB 10.3+

## Installation

1. Clone the repository
2. Copy `.env.example` to `.env` and configure your database settings
3. Install dependencies:
   ```bash
   composer install
   ```
4. Generate application key:
   ```bash
   php artisan key:generate
   ```
5. Run migrations:
   ```bash
   php artisan migrate
   ```
6. (Optional) Seed the database with sample data:
   ```bash
   php artisan db:seed
   ```

## Running the Application

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## Peppol Features

### Client Peppol
Manages Peppol-specific client information including:
- Endpoint identification
- Tax scheme details
- Legal entity information
- Financial institution details
- Buyer references

**Route:** `/clientpeppol`

### Payment Peppol
Handles Peppol payment information for invoices:
- Auto-generated references
- Payment providers

**Route:** `/paymentpeppol`

### Unit Peppol
Manages Peppol unit of measure codes:
- Standard UN/CEFACT codes
- Unit descriptions

**Route:** `/unitpeppol`

## Database Structure

### Core Tables
- `clients` - Customer/client information
- `invoices` - Invoice records
- `units` - Units of measure

### Peppol-Specific Tables
- `client_peppol` - Peppol configuration for clients
- `payment_peppol` - Peppol payment information
- `unit_peppol` - Peppol unit codes and descriptions

## Architecture

### DTOs (Data Transfer Objects)
Located in `app/DTOs/`, these replace the Yii FormModel classes:
- `ClientPeppolDTO`
- `PaymentPeppolDTO`
- `UnitPeppolDTO`

### Repositories
Located in `app/Repositories/`, providing data access layer:
- `ClientPeppolRepository`
- `PaymentPeppolRepository`
- `UnitPeppolRepository`

### Services
Located in `app/Services/`, containing business logic:
- `ClientPeppolService`
- `PaymentPeppolService`
- `UnitPeppolService`

### Controllers
Located in `app/Http/Controllers/`:
- `ClientPeppolController`
- `PaymentPeppolController`
- `UnitPeppolController`

## Views

Views are currently using plain PHP (not Blade templates) as requested, located in:
- `resources/views/clientpeppol/`
- `resources/views/paymentpeppol/`
- `resources/views/unitpeppol/`

These can be converted to Blade templates in the future.

## Testing

Run tests with PHPUnit:

```bash
vendor/bin/phpunit
```

Or using the Laravel test command:

```bash
php artisan test
```

### Available Tests
- Unit tests for DTOs
- Feature tests for Peppol functionality (to be expanded)

## Database Seeding

The application includes factories and seeders for all Peppol entities:

```bash
php artisan db:seed
```

This will create:
- 10 sample clients
- 5 sample units
- 20 sample invoices
- Peppol data for 5 clients
- Peppol data for all units
- Peppol payment data for 10 invoices

## Migration from Yii3

### What Was Migrated
- ✅ Peppol entity models (ClientPeppol, PaymentPeppol, UnitPeppol)
- ✅ Form classes converted to DTOs
- ✅ Repository pattern maintained
- ✅ Service layer implemented
- ✅ Controllers adapted to Laravel
- ✅ Views converted to plain PHP (pre-Blade)
- ✅ Database migrations created
- ✅ Factories and seeders implemented
- ✅ Basic tests created

### Not Yet Migrated
The following Yii3 components were not migrated as part of this focused Peppol migration:
- Complete invoice management system
- User authentication/authorization
- Full client management
- Product management
- Quote/Sales Order systems
- PDF generation
- Email templates
- Payment gateway integrations
- Additional Peppol helpers and arrays

These can be migrated in subsequent phases if needed.

## Development

### Code Structure
- Models use Eloquent ORM (replacing Cycle ORM)
- DTOs provide type-safe data transfer
- Repositories handle data persistence
- Services contain business logic
- Controllers handle HTTP requests/responses

### Adding New Features
1. Create migration: `php artisan make:migration create_table_name`
2. Create model: `php artisan make:model ModelName`
3. Create DTO in `app/DTOs/`
4. Create repository in `app/Repositories/`
5. Create service in `app/Services/`
6. Create controller: `php artisan make:controller ControllerName`
7. Add routes in `routes/web.php`
8. Create views in `resources/views/`

## License

BSD-3-Clause (maintained from original project)

## Support

For issues related to the Peppol implementation, please refer to the original Yii3 project documentation at:
https://github.com/rossaddison/invoice
