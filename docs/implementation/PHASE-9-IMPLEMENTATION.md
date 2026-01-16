# Phase 9: Middleware & Utilities - Implementation Guide

## Overview

This document provides comprehensive documentation for Phase 9 implementation: Middleware & Utilities. This phase adds cross-cutting concerns, helper services, and utility functionality to the Laravel 12 invoice application.

## Table of Contents

1. [Middleware](#middleware)
2. [Helper Services](#helper-services)
3. [Traits](#traits)
4. [Artisan Commands](#artisan-commands)
5. [Configuration](#configuration)
6. [Usage Examples](#usage-examples)
7. [Testing](#testing)

---

## Middleware

### 1. ActivityLogMiddleware

**Purpose**: Logs all user activities including HTTP method, URL, IP address, and execution time.

**Location**: `app/Http/Middleware/ActivityLogMiddleware.php`

**Usage**:
```php
// Apply to specific routes
Route::middleware(['activity.log'])->group(function () {
    Route::get('/invoices', [InvoiceController::class, 'index']);
});
```

**Configuration**: `config/activity.php`

**Features**:
- Logs HTTP method, URL, IP address, user agent
- Stores request/response data (sanitized)
- Tracks execution time
- Configurable exclusion patterns
- Automatic sensitive data sanitization

---

### 2. TenantMiddleware

**Purpose**: Multi-tenancy support for SaaS functionality.

**Location**: `app/Http/Middleware/TenantMiddleware.php`

**Usage**:
```php
// Apply to tenant-specific routes
Route::middleware(['tenant'])->group(function () {
    Route::resource('invoices', InvoiceController::class);
});
```

**Configuration**: `config/tenant.php`

**Features**:
- Subdomain-based tenant identification
- Domain-based tenant identification  
- Path-based tenant identification
- Tenant context management
- Cached tenant data for performance
- Database switching support

---

### 3. ApiVersionMiddleware

**Purpose**: API versioning support with deprecation warnings.

**Location**: `app/Http/Middleware/ApiVersionMiddleware.php`

**Usage**:
```php
// Automatically applied to API routes in bootstrap/app.php
```

**Configuration**: `config/api.php`

**Features**:
- Version extraction from Accept header
- Version validation
- Deprecation warnings
- Default version fallback
- Version headers in response

**Example Header**:
```
Accept: application/vnd.api+json; version=1
```

---

### 4. SecurityHeadersMiddleware

**Purpose**: Adds security headers to all responses.

**Location**: `app/Http/Middleware/SecurityHeadersMiddleware.php`

**Usage**: Automatically applied to web and API routes.

**Configuration**: `config/security.php`

**Headers Applied**:
- `X-Frame-Options: DENY`
- `X-Content-Type-Options: nosniff`
- `X-XSS-Protection: 1; mode=block`
- `Strict-Transport-Security: max-age=31536000`
- `Content-Security-Policy: default-src 'self'`
- `Referrer-Policy: no-referrer-when-downgrade`

---

### 5. RequestSanitizerMiddleware

**Purpose**: Sanitizes incoming request data to prevent XSS attacks.

**Location**: `app/Http/Middleware/RequestSanitizerMiddleware.php`

**Usage**: Automatically applied to API routes.

**Configuration**: `config/security.php`

**Features**:
- Strips HTML tags from input
- Trims whitespace
- Removes null bytes
- Whitelisted fields for rich text
- Configurable allowed tags

---

### 6. RateLimitByUserMiddleware

**Purpose**: Rate limiting per user (more granular than IP-based).

**Location**: `app/Http/Middleware/RateLimitByUserMiddleware.php`

**Usage**:
```php
Route::middleware(['rate.limit.user'])->group(function () {
    Route::post('/api/data', [DataController::class, 'store']);
});
```

**Configuration**: `config/rate-limit.php`

**Features**:
- Different limits per user role
- Cache-based implementation
- Returns 429 with Retry-After header
- Rate limit headers in response

---

### 7. LocalizationMiddleware

**Purpose**: Sets application locale based on user preference.

**Location**: `app/Http/Middleware/LocalizationMiddleware.php`

**Usage**: Automatically applied to web routes.

**Features**:
- User preference priority
- Accept-Language header fallback
- Session storage
- Carbon locale synchronization
- Supported: en, nl, de, fr, es

---

### 8. PerformanceMonitoringMiddleware

**Purpose**: Monitors application performance metrics.

**Location**: `app/Http/Middleware/PerformanceMonitoringMiddleware.php`

**Usage**: Automatically applied to web and API routes.

**Features**:
- Request execution time tracking
- Database query count and time
- Memory usage measurement
- Slow request logging
- Performance headers in response

---

## Helper Services

### 1. CurrencyConverter

**Purpose**: Convert amounts between currencies with live exchange rates.

**Location**: `app/Services/Helpers/CurrencyConverter.php`

**Usage**:
```php
use App\Services\Helpers\CurrencyConverter;

$converter = app(CurrencyConverter::class);

// Convert amount
$usdAmount = $converter->convert(100, 'EUR', 'USD');

// Get exchange rate
$rate = $converter->getRate('EUR', 'USD');

// Format money
$formatted = $converter->formatMoney(100.50, 'EUR'); // "€100.50"
```

**Configuration**: `config/currency.php`

**Features**:
- Fixer.io API integration
- OpenExchangeRates API support
- 24-hour rate caching
- Multiple currency support
- Locale-aware formatting

---

### 2. DateHelper

**Purpose**: Date and time utilities including business day calculations.

**Location**: `app/Services/Helpers/DateHelper.php`

**Usage**:
```php
use App\Services\Helpers\DateHelper;
use Carbon\Carbon;

$helper = app(DateHelper::class);

// Add business days (excluding weekends)
$dueDate = $helper->addBusinessDays(Carbon::today(), 5);

// Calculate business days between dates
$days = $helper->getBusinessDaysBetween($start, $end);

// Check if business day
$isBusinessDay = $helper->isBusinessDay(Carbon::today());

// Parse flexible dates
$date = $helper->parseFlexibleDate('tomorrow');

// Format relative
$relative = $helper->formatRelative(Carbon::yesterday()); // "1 day ago"
```

---

### 3. NumberFormatter

**Purpose**: Locale-aware number formatting.

**Location**: `app/Services/Helpers/NumberFormatter.php`

**Usage**:
```php
use App\Services\Helpers\NumberFormatter;

$formatter = app(NumberFormatter::class);

// Format number
$formatted = $formatter->formatNumber(1234.56, 2); // "1,234.56"

// Format currency
$money = $formatter->formatCurrency(1234.56, 'EUR'); // "€1,234.56"

// Format percentage
$percent = $formatter->formatPercentage(25.5); // "25.50%"

// Format file size
$size = $formatter->formatFileSize(1024 * 1024); // "1.00 MB"

// Format ordinal
$ordinal = $formatter->formatOrdinal(21); // "21st"

// Parse formatted number
$number = $formatter->parseNumber('1,234.56'); // 1234.56
```

---

### 4. ValidationHelper

**Purpose**: Custom validation utilities for VAT, IBAN, phone numbers.

**Location**: `app/Services/Helpers/ValidationHelper.php`

**Usage**:
```php
use App\Services\Helpers\ValidationHelper;

$helper = app(ValidationHelper::class);

// Validate VAT number (EU)
$isValid = $helper->validateVatNumber('DE123456789', 'DE');

// Validate IBAN
$isValid = $helper->validateIban('DE89370400440532013000');

// Validate phone number
$isValid = $helper->validatePhoneNumber('+31612345678');

// Check disposable email
$isDisposable = $helper->isDisposableEmail('test@tempmail.com');

// Validate business rule
$isValid = $helper->validateBusinessRule('positive', 10);
```

---

### 5. FileHelper

**Purpose**: Secure file operations and utilities.

**Location**: `app/Services/Helpers/FileHelper.php`

**Usage**:
```php
use App\Services\Helpers\FileHelper;

$helper = app(FileHelper::class);

// Upload file securely
$path = $helper->uploadFile($request->file('upload'));

// Delete file
$helper->deleteFile($path);

// Resize image
$resizedPath = $helper->resizeImage($path, 800, 600);

// Get file hash
$hash = $helper->getFileHash($path, 'sha256');

// Get MIME type
$mimeType = $helper->getMimeType($path);
```

---

### 6. AuditHelper

**Purpose**: Model change tracking and audit trail.

**Location**: `app/Services/Helpers/AuditHelper.php`

**Usage**:
```php
use App\Services\Helpers\AuditHelper;

$helper = app(AuditHelper::class);

// Log model change
$helper->logChange($invoice, 'updated');

// Get audit trail
$audits = $helper->getAuditTrail($invoice);

// Restore previous version
$helper->restoreVersion($invoice, $auditId);

// Compare versions
$diff = $helper->compareVersions($auditId1, $auditId2);
```

---

## Traits

### 1. HasUuid

**Purpose**: Use UUID as primary key instead of auto-incrementing integers.

**Usage**:
```php
use App\Traits\HasUuid;

class Document extends Model
{
    use HasUuid;
}
```

---

### 2. Cacheable

**Purpose**: Easy model caching with auto-invalidation.

**Usage**:
```php
use App\Traits\Cacheable;

class Product extends Model
{
    use Cacheable;
    
    protected int $cacheTtl = 3600; // 1 hour
}

// Use cached model
$product = Product::cached($id);
```

---

### 3. Searchable

**Purpose**: Full-text search capabilities with weighting.

**Usage**:
```php
use App\Traits\Searchable;

class Client extends Model
{
    use Searchable;
    
    protected array $searchable = ['name', 'email', 'address'];
}

// Search
$results = Client::search('john')->get();

// Weighted search
$results = Client::weightedSearch('john')->get();
```

---

### 4. Exportable

**Purpose**: Export model data to CSV or JSON.

**Usage**:
```php
use App\Traits\Exportable;

class Invoice extends Model
{
    use Exportable;
}

// Export to CSV
$csv = $invoice->exportToCsv($invoices, [
    'id' => 'ID',
    'invoice_number' => 'Invoice Number',
    'total' => 'Total',
]);

// Download CSV
return $invoice->downloadCsv($invoices, $columns, 'invoices.csv');
```

---

## Artisan Commands

### 1. cleanup:old-data

**Purpose**: Cleanup old data from the system.

**Usage**:
```bash
# Cleanup with default retention period
php artisan cleanup:old-data

# Specify retention days
php artisan cleanup:old-data --days=30

# Dry run (no actual deletion)
php artisan cleanup:old-data --dry-run
```

**Features**:
- Deletes old activity logs
- Cleans temp files
- Removes old soft-deleted records
- Configurable retention period

---

### 2. cache:warmup

**Purpose**: Pre-populate cache with frequently accessed data.

**Usage**:
```bash
php artisan cache:warmup
```

**Caches**:
- Configuration
- Routes
- Views
- Common queries

---

### 3. health:check

**Purpose**: System health monitoring.

**Usage**:
```bash
php artisan health:check
```

**Checks**:
- Database connectivity
- Cache connectivity
- Storage access
- Disk space
- External API availability

**Exit Codes**:
- 0: All checks passed
- 1: Some checks failed

---

### 4. currency:sync

**Purpose**: Sync currency exchange rates from API.

**Usage**:
```bash
# Sync rates
php artisan currency:sync

# Force refresh cached rates
php artisan currency:sync --force
```

---

### 5. report:generate

**Purpose**: Generate scheduled reports.

**Usage**:
```bash
# Generate profit report
php artisan report:generate profit --period=monthly

# Generate and email report
php artisan report:generate sales --period=weekly --email=admin@example.com
```

**Report Types**:
- profit
- sales
- inventory

---

## Configuration

### Activity Logging (`config/activity.php`)

```php
return [
    'enabled' => true,
    'retention_days' => 90,
    'exclude_urls' => ['/health', '/_debugbar'],
    'sanitize_fields' => ['password', 'token'],
];
```

### Currency (`config/currency.php`)

```php
return [
    'api_provider' => 'fixer',
    'api_key' => env('CURRENCY_API_KEY'),
    'base_currency' => 'EUR',
    'cache_ttl' => 86400,
];
```

### Security (`config/security.php`)

```php
return [
    'headers' => [
        'X-Frame-Options' => 'DENY',
        'X-Content-Type-Options' => 'nosniff',
    ],
    'sanitize' => [
        'enabled' => true,
        'whitelist_fields' => ['description', 'notes'],
    ],
];
```

---

## Usage Examples

### Example 1: Activity Logging

```php
// The middleware automatically logs activities
// Access logs via model:
$logs = ActivityLog::where('user_id', $userId)
    ->where('action', 'create')
    ->orderBy('created_at', 'desc')
    ->get();
```

### Example 2: Currency Conversion in Invoice

```php
use App\Services\Helpers\CurrencyConverter;

class InvoiceController extends Controller
{
    public function __construct(
        private CurrencyConverter $converter
    ) {}
    
    public function show(Invoice $invoice)
    {
        $currencies = ['USD', 'GBP', 'EUR'];
        $conversions = [];
        
        foreach ($currencies as $currency) {
            $conversions[$currency] = $this->converter->convert(
                $invoice->total,
                $invoice->currency,
                $currency
            );
        }
        
        return view('invoices.show', compact('invoice', 'conversions'));
    }
}
```

### Example 3: Model Auditing

```php
use App\Traits\Auditable; // Would need to create this trait
use App\Services\Helpers\AuditHelper;

class Invoice extends Model
{
    protected static function boot()
    {
        parent::boot();
        
        static::updated(function (Invoice $invoice) {
            app(AuditHelper::class)->logChange($invoice, 'updated');
        });
    }
}
```

### Example 4: Exportable Invoices

```php
use App\Traits\Exportable;

class Invoice extends Model
{
    use Exportable;
}

// In controller
public function export()
{
    $invoices = Invoice::all();
    
    $columns = [
        'invoice_number' => 'Invoice #',
        'client.name' => 'Client',
        'total' => 'Total',
        'status' => 'Status',
        'created_at' => 'Date',
    ];
    
    return (new Invoice())->downloadCsv($invoices, $columns, 'invoices.csv');
}
```

---

## Testing

### Running Tests

```bash
# Run all tests
vendor/bin/phpunit

# Run middleware tests only
vendor/bin/phpunit tests/Unit/Middleware

# Run helper tests only
vendor/bin/phpunit tests/Unit/Helpers

# Run specific test
vendor/bin/phpunit tests/Unit/Middleware/SecurityHeadersMiddlewareTest.php
```

### Test Coverage

- **Middleware Tests**: 8 test methods
- **Helper Tests**: 14 test methods
- **Total**: 22 comprehensive tests

All tests follow the `it_*` naming convention for clarity.

---

## Best Practices

1. **Use Helper Services via Dependency Injection**:
   ```php
   public function __construct(
       private CurrencyConverter $converter,
       private DateHelper $dateHelper
   ) {}
   ```

2. **Cache Expensive Operations**:
   ```php
   $rate = Cache::remember('rate_eur_usd', 3600, function () {
       return $this->converter->getRate('EUR', 'USD');
   });
   ```

3. **Apply Middleware Selectively**:
   - Use route-level middleware for specific needs
   - Global middleware should be lightweight

4. **Monitor Performance**:
   - Check performance metrics regularly
   - Identify slow requests
   - Optimize database queries

5. **Security First**:
   - Always sanitize user input
   - Use security headers
   - Validate all external data

---

## Troubleshooting

### Issue: Currency API not working

**Solution**: Check API key in `.env`:
```
CURRENCY_API_KEY=your_api_key_here
CURRENCY_API_PROVIDER=fixer
```

### Issue: Activity logs growing too large

**Solution**: Run cleanup command regularly:
```bash
php artisan cleanup:old-data --days=30
```

### Issue: Cache not working

**Solution**: Check cache configuration and run health check:
```bash
php artisan health:check
```

---

## Future Enhancements

Potential improvements for future releases:

1. **Activity Logs**: Add filtering and search UI
2. **Multi-tenancy**: Implement separate database support
3. **Performance**: Add APM integration (New Relic, DataDog)
4. **Currency**: Support more providers
5. **Audit**: Add UI for viewing audit trails
6. **Reports**: Add more report types

---

## Contributing

When adding new middleware or helpers:

1. Follow SOLID principles
2. Use early return pattern
3. Add comprehensive tests
4. Update this documentation
5. Register in appropriate provider/config

---

## License

This implementation is part of the Laravel Invoice Application and follows the same license as the main project (BSD-3-Clause).
