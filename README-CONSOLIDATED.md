# Laravel Invoice - Peppol-Compliant Invoice Management System

[![License](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)
[![Laravel](https://img.shields.io/badge/Laravel-12-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3+-purple.svg)](https://php.net)
[![Tests](https://img.shields.io/badge/Tests-135%2B-green.svg)](tests/)
[![Peppol](https://img.shields.io/badge/Peppol-BIS%203.0-blue.svg)](https://peppol.org)

> Professional invoice management system with full Peppol (Pan-European Public Procurement OnLine) compliance, migrated from Yii3 to Laravel 12.

## 🚀 Quick Start

### Requirements

- PHP 8.3 or higher
- Composer 2.6+
- MySQL 5.7+ / MariaDB 10.3+ / PostgreSQL 13+
- Node.js 18+ (for frontend assets)

### Installation

```bash
# Clone the repository
git clone https://github.com/underdogg-forks/yii-with-invoice-to-laravel.git
cd yii-with-invoice-to-laravel

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Configure environment
cp .env.example .env
php artisan key:generate

# Configure database in .env file, then run migrations
php artisan migrate

# (Optional) Seed with sample data
php artisan db:seed

# Start development server
php artisan serve
```

Visit http://localhost:8000

## 📋 Features

### Core Functionality
- ✅ **Invoice Management** - Create, edit, and manage invoices with comprehensive item tracking
- ✅ **Quote System** - Generate quotes and convert to sales orders/invoices
- ✅ **Sales Orders** - Full sales order workflow with status tracking
- ✅ **Client Management** - Comprehensive client database with custom fields
- ✅ **Product Catalog** - Product management with inventory tracking
- ✅ **Multi-Currency** - Support for multiple currencies
- ✅ **Tax Calculations** - Automatic tax calculations with configurable rates

### Peppol Compliance (UBL 2.1 / EN 16931)
- ✅ **UBL XML Generation** - Peppol BIS 3.0 compliant XML invoices
- ✅ **Peppol Network Integration** - Direct transmission via StoreCove
- ✅ **Endpoint Management** - Client endpoint ID management
- ✅ **Tax Scheme Support** - VAT, GST, and other tax schemes
- ✅ **Legal Entity Data** - Comprehensive supplier/customer information
- ✅ **Payment Terms** - Structured payment information
- ✅ **Webhook Processing** - Real-time status updates

### Document Generation
- ✅ **PDF Generation** - Professional invoice, quote, and sales order PDFs
- ✅ **Template System** - Customizable document templates
- ✅ **Visual Template Builder** - Drag-and-drop template creation
- ✅ **Multi-Language** - Template support for multiple languages
- ✅ **Variable Replacement** - Dynamic content with {{variable}} syntax

### Email & Notifications
- ✅ **Gmail-like Inbox** - Email tracking with conversation threading
- ✅ **Email Templates** - Customizable transactional email templates
- ✅ **Multi-Channel Notifications** - Email, database, and push notifications
- ✅ **User Preferences** - Per-user notification channel preferences
- ✅ **Queue Integration** - Async email and notification processing

### Reporting
- ✅ **Profit Analysis** - Revenue, costs, and profit margin reports
- ✅ **Sales Summary** - Top products, clients, and revenue trends
- ✅ **Inventory Reports** - Stock levels and low stock alerts
- ✅ **Custom Reports** - Flexible report generation with parameters
- ✅ **Report Scheduling** - Automated report generation

### Security & Authentication
- ✅ **Two-Factor Authentication** - TOTP-based 2FA with recovery codes
- ✅ **Role-Based Access Control** - Comprehensive permissions system (Spatie)
- ✅ **Password Reset** - Secure password reset workflow
- ✅ **Session Management** - Secure session handling
- ✅ **CSRF Protection** - Built-in Laravel CSRF protection

## 🏗️ Architecture

### Design Patterns

#### SOLID Principles
- **Single Responsibility**: Each class has one clear purpose
- **Open/Closed**: Extensible through interfaces and abstract classes
- **Liskov Substitution**: Proper inheritance hierarchies
- **Interface Segregation**: Focused, client-specific interfaces
- **Dependency Inversion**: Depend on abstractions, not concretions

#### DRY (Don't Repeat Yourself)
- Shared logic extracted to services
- Reusable components and traits
- Helper functions for common operations
- Template inheritance and composition

#### Early Return Pattern
- Guard clauses at method start
- Fail-fast validation
- Reduced nesting and complexity

### Layer Architecture

```
┌─────────────────────────────────────┐
│         Controllers (HTTP)          │ ← Thin, handle requests only
├─────────────────────────────────────┤
│      DTOs (Data Transfer)           │ ← Type-safe data transfer
├─────────────────────────────────────┤
│    Services (Business Logic)        │ ← Core business rules
├─────────────────────────────────────┤
│   Repositories (Data Access)        │ ← Database abstraction
├─────────────────────────────────────┤
│      Models (Eloquent ORM)          │ ← Database entities
└─────────────────────────────────────┘
```

### Key Components

#### DTOs (Data Transfer Objects)
Located in `app/DTOs/` - Type-safe data containers

```php
class InvoiceDTO {
    public function __construct(
        public readonly ?int $id,
        public readonly int $client_id,
        public readonly string $invoice_number,
        public readonly float $total_amount,
        // ...
    ) {}
    
    public static function fromArray(array $data): self { /* ... */ }
    public static function fromModel(Invoice $invoice): self { /* ... */ }
    public function toArray(): array { /* ... */ }
}
```

#### Services (Business Logic)
Located in `app/Services/` - Contains all business rules

```php
class InvoiceService {
    public function __construct(
        private InvoiceRepository $repository,
        private PdfService $pdfService
    ) {}
    
    public function create(InvoiceDTO $dto): Invoice { /* ... */ }
    public function generatePdf(Invoice $invoice): string { /* ... */ }
}
```

#### Repositories (Data Access)
Located in `app/Repositories/` - Database operations

```php
class InvoiceRepository {
    public function findWithRelations(int $id): ?Invoice {
        return Invoice::with(['client', 'items', 'amounts'])->find($id);
    }
    
    public function search(array $criteria, int $perPage = 15) { /* ... */ }
}
```

#### Controllers (HTTP Layer)
Located in `app/Http/Controllers/` - Thin HTTP handlers

```php
class InvoiceController extends Controller {
    public function __construct(private InvoiceService $service) {}
    
    public function store(Request $request): RedirectResponse {
        $validated = $request->validate([...]);
        $dto = InvoiceDTO::fromArray($validated);
        $invoice = $this->service->create($dto);
        return redirect()->route('invoices.show', $invoice);
    }
}
```

## 🧪 Testing

### Test Standards

- **Naming Convention**: All test methods use `it_*` prefix
- **Pattern**: Arrange-Act-Assert structure
- **Coverage**: 135+ comprehensive tests
- **Types**: Unit, Feature, and Integration tests

### Running Tests

```bash
# Run all tests
vendor/bin/phpunit

# Run specific test suite
vendor/bin/phpunit tests/Unit
vendor/bin/phpunit tests/Feature

# Run with coverage
vendor/bin/phpunit --coverage-html coverage

# Run specific test
vendor/bin/phpunit --filter it_creates_invoice
```

### Test Example

```php
public function it_creates_invoice_with_peppol_data(): void
{
    // Arrange
    $client = Client::factory()->withPeppol()->create();
    $data = ['invoice_number' => 'INV-001', ...];
    
    // Act
    $invoice = $this->invoiceService->create(InvoiceDTO::fromArray($data));
    
    // Assert
    $this->assertNotNull($invoice->id);
    $this->assertDatabaseHas('invoices', ['invoice_number' => 'INV-001']);
}
```

## 📚 API Documentation

### Peppol Endpoints

#### Generate UBL XML
```http
GET /invoices/{id}/xml
Authorization: Bearer {token}
```

#### Send via Peppol Network
```http
POST /invoices/{id}/send-peppol
Authorization: Bearer {token}
Content-Type: application/json

{
    "recipient_endpoint_id": "0088:1234567890123",
    "recipient_scheme_id": "0088"
}
```

### Email Endpoints

#### Get Inbox
```http
GET /emails?page=1&per_page=15&unread=true
Authorization: Bearer {token}
```

#### Send Email
```http
POST /emails/send
Authorization: Bearer {token}
Content-Type: application/json

{
    "to": "client@example.com",
    "subject": "Invoice INV-001",
    "body": "<html>...</html>",
    "attachments": []
}
```

### Report Endpoints

#### Generate Profit Report
```http
POST /reports/generate
Authorization: Bearer {token}
Content-Type: application/json

{
    "type": "profit",
    "start_date": "2024-01-01",
    "end_date": "2024-12-31",
    "period": "month"
}
```

## 🔧 Configuration

### Peppol Configuration

Edit `config/peppol.php`:

```php
return [
    'supplier' => [
        'endpoint_id' => env('PEPPOL_ENDPOINT_ID', '0088:1234567890123'),
        'scheme_id' => env('PEPPOL_SCHEME_ID', '0088'),
        'vat_number' => env('PEPPOL_VAT_NUMBER', 'NL123456789B01'),
        // ...
    ],
    'service_provider' => env('PEPPOL_SERVICE_PROVIDER', 'storecove'),
    // ...
];
```

### StoreCove Configuration

Edit `config/storecove.php`:

```php
return [
    'api_key' => env('STORECOVE_API_KEY'),
    'api_url' => env('STORECOVE_API_URL', 'https://api.storecove.com/v2'),
    'webhook_url' => env('STORECOVE_WEBHOOK_URL'),
    'webhook_secret' => env('STORECOVE_WEBHOOK_SECRET'),
];
```

## 📖 Migration Status

### ✅ Completed Phases

- **Phase 0**: Peppol Foundation (Peppol entities, base models)
- **Phase 1**: Authentication & User Management (2FA, RBAC)
- **Phase 2**: Core Invoice System (Invoices, items, calculations)
- **Phase 3**: Client & Product Management (Clients, products, custom fields)
- **Phase 4**: Quote & Sales Order Systems (Quote→SO→Invoice workflow)
- **Phase 6**: PDF & UBL XML Generation (Templates, Peppol compliance)
- **Phase 7**: Email & Notifications (Templates, inbox, notifications)

### 🔄 In Progress

- **Phase 5**: Payment Gateways (Stripe, Braintree, Amazon Pay)

### 📅 Upcoming

- **Phase 9**: Middleware & Utilities (Custom middleware, helpers)

## 🤝 Contributing

Please see [CONTRIBUTING.md](.github/CONTRIBUTING.md) for details.

### Development Guidelines

1. Follow PSR-12 coding standards
2. Write tests for all new features (it_* naming)
3. Use DTOs for data transfer
4. Keep controllers thin
5. Put business logic in services
6. Use repositories for data access
7. Apply SOLID principles
8. Follow DRY principles
9. Use early returns for guards

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Original Yii3 Invoice application
- Laravel Framework
- Spatie packages (laravel-permission)
- StoreCove for Peppol network integration
- mPDF for PDF generation
- Sabre/XML for UBL generation

## 📞 Support

For issues and questions:
- GitHub Issues: [Create an issue](https://github.com/underdogg-forks/yii-with-invoice-to-laravel/issues)
- Documentation: [Wiki](https://github.com/underdogg-forks/yii-with-invoice-to-laravel/wiki)

## 🗺️ Roadmap

See [FULL-MIGRATION-ROADMAP.md](FULL-MIGRATION-ROADMAP.md) for detailed migration progress and future plans.
