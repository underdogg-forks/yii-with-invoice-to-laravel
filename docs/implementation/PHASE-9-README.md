# Phase 9: Middleware & Utilities - Quick Reference

## 🚀 Quick Start

### Middleware Usage

```php
// Activity logging
Route::middleware(['activity.log'])->group(function () {
    Route::resource('invoices', InvoiceController::class);
});

// Tenant context
Route::middleware(['tenant'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});

// Rate limiting by user
Route::middleware(['rate.limit.user'])->group(function () {
    Route::post('/api/data', [ApiController::class, 'store']);
});
```

### Helper Services

```php
// Currency conversion
$converter = app(CurrencyConverter::class);
$usd = $converter->convert(100, 'EUR', 'USD');

// Date utilities
$dateHelper = app(DateHelper::class);
$dueDate = $dateHelper->addBusinessDays(now(), 5);

// Number formatting
$formatter = app(NumberFormatter::class);
$formatted = $formatter->formatCurrency(1234.56, 'EUR');

// Validation
$validator = app(ValidationHelper::class);
$isValid = $validator->validateVatNumber('DE123456789', 'DE');

// File operations
$fileHelper = app(FileHelper::class);
$path = $fileHelper->uploadFile($request->file('document'));

// Audit trail
$auditHelper = app(AuditHelper::class);
$auditHelper->logChange($model, 'updated');
```

### Traits

```php
// UUID primary keys
class Document extends Model {
    use HasUuid;
}

// Model caching
class Product extends Model {
    use Cacheable;
    protected int $cacheTtl = 3600;
}
$product = Product::cached($id);

// Full-text search
class Client extends Model {
    use Searchable;
    protected array $searchable = ['name', 'email'];
}
$results = Client::search('john')->get();

// Export functionality
class Invoice extends Model {
    use Exportable;
}
return $invoice->downloadCsv($data, $columns, 'export.csv');
```

### Artisan Commands

```bash
# Cleanup old data
php artisan cleanup:old-data --days=30 --dry-run

# Warm up cache
php artisan cache:warmup

# Health check
php artisan health:check

# Sync exchange rates
php artisan currency:sync --force

# Generate reports
php artisan report:generate profit --period=monthly
```

## 📦 What's Included

### Middleware (8)
1. **ActivityLogMiddleware** - User activity logging
2. **TenantMiddleware** - Multi-tenancy support
3. **ApiVersionMiddleware** - API versioning
4. **SecurityHeadersMiddleware** - Security headers
5. **RequestSanitizerMiddleware** - Input sanitization
6. **RateLimitByUserMiddleware** - User-based rate limiting
7. **LocalizationMiddleware** - Locale management
8. **PerformanceMonitoringMiddleware** - Performance tracking

### Helper Services (6)
1. **CurrencyConverter** - Currency conversion with live rates
2. **DateHelper** - Business day calculations
3. **NumberFormatter** - Locale-aware formatting
4. **ValidationHelper** - VAT, IBAN, phone validation
5. **FileHelper** - Secure file operations
6. **AuditHelper** - Model change tracking

### Traits (4)
1. **HasUuid** - UUID primary keys
2. **Cacheable** - Model caching
3. **Searchable** - Full-text search
4. **Exportable** - CSV/JSON export

### Commands (5)
1. **CleanupCommand** - Data cleanup
2. **CacheWarmupCommand** - Cache pre-population
3. **HealthCheckCommand** - System health
4. **SyncExchangeRatesCommand** - Currency sync
5. **GenerateReportCommand** - Report generation

### Configuration (6)
- `config/activity.php` - Activity logging
- `config/api.php` - API versioning
- `config/currency.php` - Currency settings
- `config/rate-limit.php` - Rate limiting
- `config/security.php` - Security settings
- `config/tenant.php` - Multi-tenancy

## 🧪 Testing

```bash
# Run all tests
vendor/bin/phpunit

# Run middleware tests
vendor/bin/phpunit tests/Unit/Middleware

# Run helper tests
vendor/bin/phpunit tests/Unit/Helpers
```

**Test Coverage**: 22 comprehensive tests

## 📚 Documentation

See [PHASE-9-IMPLEMENTATION.md](PHASE-9-IMPLEMENTATION.md) for:
- Detailed usage examples
- Configuration options
- Best practices
- Troubleshooting guide
- Integration examples

## ✅ Code Quality

- ✅ SOLID Principles
- ✅ DRY (Don't Repeat Yourself)
- ✅ Early Return Pattern
- ✅ Type Hints
- ✅ Dependency Injection
- ✅ Comprehensive Tests
- ✅ Security Hardened
- ✅ Performance Optimized

## 🔒 Security Features

- XSS protection via input sanitization
- Security headers on all responses
- Rate limiting to prevent abuse
- Activity logging for audit trails
- Secure file upload handling
- Sensitive data sanitization

## 📊 Performance Features

- Request execution time tracking
- Database query monitoring
- Memory usage tracking
- Slow request logging
- Automatic caching with invalidation
- Optimized currency rate caching

## 🌐 Localization

Supported locales: **en**, **nl**, **de**, **fr**, **es**

Automatically detects and sets locale based on:
1. User preference
2. Session
3. Accept-Language header
4. Application default

## 🎯 Key Features

### Activity Logging
- Complete audit trail
- Performance metrics
- User activity tracking
- Configurable retention

### Multi-Tenancy
- Subdomain/domain/path based
- Cached tenant data
- Database switching support
- Access control

### Currency Conversion
- Live exchange rates
- Multiple providers
- 24-hour caching
- Format with symbols

### Validation
- EU VAT numbers
- IBAN
- International phone numbers
- Disposable email detection

## 🚦 Status

**Phase 9: COMPLETE** ✅

All components tested, documented, and production-ready.

## 🤝 Contributing

Follow these principles when extending:
1. Apply SOLID principles
2. Add comprehensive tests
3. Update documentation
4. Use dependency injection
5. Follow early return pattern

## 📝 License

BSD-3-Clause (same as main project)
