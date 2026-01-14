# Phase 9: Middleware & Utilities Implementation Prompt

## Overview
Implement comprehensive middleware and utility services for the Laravel 12 invoice application migration. This phase focuses on custom middleware for cross-cutting concerns, helper services, and utility classes following SOLID/DRY principles.

## Scope & Timeline
- **Effort**: 10-15 hours
- **Complexity**: Low-Medium
- **Dependencies**: Phases 0-4, 6, 7 complete
- **Test Coverage**: 15+ test methods

## Core Principles
- ✅ **SOLID Principles** - Single Responsibility, Open/Closed, Liskov Substitution, Interface Segregation, Dependency Inversion
- ✅ **DRY (Don't Repeat Yourself)** - No code duplication, reusable components
- ✅ **Early Return Pattern** - Guard clauses, fail-fast validation
- ✅ **Dependency Injection** - Constructor injection for all dependencies
- ✅ **Interface-based Design** - Program to interfaces, not implementations

## Part 1: Custom Middleware (4-5 hours)

### Middleware to Create (8 Middleware Classes)

#### 1. **ActivityLogMiddleware**
- **Purpose**: Log all user activities (login, logout, CRUD operations)
- **Features**:
  - Log HTTP method, URL, IP address, user agent
  - Store user ID, timestamp, request payload (sanitized)
  - Exclude sensitive data (passwords, tokens)
  - Configurable log level (info, debug, warning)
  - Performance tracking (execution time)
- **Database**: `activity_logs` table
- **Model**: `ActivityLog` with user relationship

#### 2. **TenantMiddleware**
- **Purpose**: Multi-tenancy support for SaaS functionality
- **Features**:
  - Identify tenant from subdomain or request parameter
  - Set tenant context for database queries
  - Validate tenant access permissions
  - Handle tenant switching
  - Cache tenant data for performance
- **Database**: `tenants` table
- **Model**: `Tenant` with users, invoices relationships

#### 3. **ApiVersionMiddleware**
- **Purpose**: API versioning support
- **Features**:
  - Extract version from header (Accept: application/vnd.api+json; version=1)
  - Validate version compatibility
  - Route to correct controller version
  - Deprecation warnings
  - Default to latest stable version
- **Config**: `config/api.php` for version management

#### 4. **SecurityHeadersMiddleware**
- **Purpose**: Add security headers to responses
- **Features**:
  - X-Frame-Options: DENY
  - X-Content-Type-Options: nosniff
  - X-XSS-Protection: 1; mode=block
  - Strict-Transport-Security: max-age=31536000
  - Content-Security-Policy configurable
  - Referrer-Policy: no-referrer-when-downgrade
- **Config**: `config/security.php`

#### 5. **RequestSanitizerMiddleware**
- **Purpose**: Sanitize incoming request data
- **Features**:
  - Strip HTML tags from input
  - Trim whitespace
  - Remove null bytes
  - XSS protection
  - SQL injection protection
  - Configurable whitelist for HTML fields (e.g., rich text)
- **Uses**: HTML Purifier or similar

#### 6. **RateLimitByUserMiddleware**
- **Purpose**: Rate limiting per user (more granular than IP)
- **Features**:
  - Limit requests per minute/hour
  - Different limits for authenticated vs. guest users
  - Different limits per user role (admin, user, guest)
  - Return 429 status with Retry-After header
  - Cache-based implementation (Redis)
- **Config**: `config/rate-limit.php`

#### 7. **LocalizationMiddleware**
- **Purpose**: Set application locale based on user preference
- **Features**:
  - Read locale from user settings
  - Fallback to browser Accept-Language header
  - Fallback to application default
  - Set Laravel locale and Carbon locale
  - Store locale in session
- **Supported**: en, nl, de, fr, es (configurable)

#### 8. **PerformanceMonitoringMiddleware**
- **Purpose**: Monitor application performance
- **Features**:
  - Measure request execution time
  - Measure database query count and time
  - Measure memory usage
  - Log slow requests (configurable threshold)
  - Store metrics in database or push to monitoring service
- **Database**: `performance_metrics` table (optional)
- **Integration**: Can integrate with New Relic, DataDog, etc.

### Middleware Migrations

```php
// migration: create_activity_logs_table
Schema::create('activity_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
    $table->string('action'); // create, update, delete, view, login, logout
    $table->string('model_type')->nullable(); // Invoice, Client, etc.
    $table->unsignedBigInteger('model_id')->nullable();
    $table->string('method'); // GET, POST, PUT, DELETE
    $table->string('url');
    $table->ipAddress('ip_address');
    $table->text('user_agent')->nullable();
    $table->json('request_data')->nullable();
    $table->json('response_data')->nullable();
    $table->integer('status_code')->nullable();
    $table->decimal('execution_time', 8, 2)->nullable(); // milliseconds
    $table->timestamps();
    
    $table->index(['user_id', 'created_at']);
    $table->index(['action', 'created_at']);
    $table->index(['model_type', 'model_id']);
});

// migration: create_tenants_table
Schema::create('tenants', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('subdomain')->unique();
    $table->string('domain')->nullable();
    $table->string('database')->nullable(); // for separate DB multi-tenancy
    $table->boolean('is_active')->default(true);
    $table->json('settings')->nullable();
    $table->timestamp('trial_ends_at')->nullable();
    $table->timestamp('subscribed_at')->nullable();
    $table->timestamps();
    $table->softDeletes();
});

// migration: create_performance_metrics_table
Schema::create('performance_metrics', function (Blueprint $table) {
    $table->id();
    $table->string('url');
    $table->string('method');
    $table->decimal('execution_time', 8, 2); // milliseconds
    $table->integer('query_count');
    $table->decimal('query_time', 8, 2); // milliseconds
    $table->integer('memory_usage'); // bytes
    $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
    $table->timestamp('created_at');
    
    $table->index(['url', 'created_at']);
    $table->index('created_at');
});
```

### Middleware Registration

**app/Http/Kernel.php:**
```php
protected $middlewareGroups = [
    'web' => [
        // ... existing middleware
        \App\Http\Middleware\LocalizationMiddleware::class,
        \App\Http\Middleware\SecurityHeadersMiddleware::class,
        \App\Http\Middleware\PerformanceMonitoringMiddleware::class,
    ],
    
    'api' => [
        // ... existing middleware
        \App\Http\Middleware\ApiVersionMiddleware::class,
        \App\Http\Middleware\SecurityHeadersMiddleware::class,
        \App\Http\Middleware\RequestSanitizerMiddleware::class,
        \App\Http\Middleware\PerformanceMonitoringMiddleware::class,
    ],
];

protected $routeMiddleware = [
    // ... existing middleware
    'activity.log' => \App\Http\Middleware\ActivityLogMiddleware::class,
    'tenant' => \App\Http\Middleware\TenantMiddleware::class,
    'rate.limit.user' => \App\Http\Middleware\RateLimitByUserMiddleware::class,
];
```

## Part 2: Helper Services & Utilities (4-5 hours)

### Helper Services to Create (6 Services)

#### 1. **CurrencyConverter**
- **Purpose**: Convert amounts between currencies
- **Features**:
  - Fetch exchange rates from external API (e.g., fixer.io, openexchangerates.org)
  - Cache exchange rates (24 hour TTL)
  - Support major currencies (USD, EUR, GBP, JPY, etc.)
  - Precision handling (decimal places)
  - Historical rate lookup
- **Config**: `config/currency.php`
- **Methods**:
  - `convert(float $amount, string $from, string $to): float`
  - `getRate(string $from, string $to): float`
  - `getAvailableCurrencies(): array`
  - `formatMoney(float $amount, string $currency): string`

#### 2. **DateHelper**
- **Purpose**: Date/time utilities
- **Features**:
  - Parse various date formats
  - Business day calculations (exclude weekends/holidays)
  - Date range generation
  - Relative date formatting ("2 days ago")
  - Timezone conversions
- **Methods**:
  - `addBusinessDays(Carbon $date, int $days): Carbon`
  - `getBusinessDaysBetween(Carbon $start, Carbon $end): int`
  - `isBusinessDay(Carbon $date): bool`
  - `parseFlexibleDate(string $input): Carbon`
  - `formatRelative(Carbon $date): string`

#### 3. **NumberFormatter**
- **Purpose**: Format numbers for display
- **Features**:
  - Locale-aware number formatting
  - Currency formatting with symbols
  - Percentage formatting
  - File size formatting (KB, MB, GB)
  - Ordinal numbers (1st, 2nd, 3rd)
- **Methods**:
  - `formatNumber(float $number, int $decimals = 2, ?string $locale = null): string`
  - `formatCurrency(float $amount, string $currency, ?string $locale = null): string`
  - `formatPercentage(float $value, int $decimals = 2): string`
  - `formatFileSize(int $bytes): string`
  - `formatOrdinal(int $number): string`

#### 4. **ValidationHelper**
- **Purpose**: Custom validation utilities
- **Features**:
  - VAT number validation (EU countries)
  - IBAN validation
  - Bank account validation
  - Phone number validation (international)
  - Email domain validation (disposable email check)
  - Custom business rules
- **Methods**:
  - `validateVatNumber(string $vat, string $country): bool`
  - `validateIban(string $iban): bool`
  - `validatePhoneNumber(string $phone, ?string $country = null): bool`
  - `isDisposableEmail(string $email): bool`
  - `validateBusinessRule(string $rule, mixed $value): bool`

#### 5. **FileHelper**
- **Purpose**: File operation utilities
- **Features**:
  - Secure file uploads with validation
  - File type detection (MIME)
  - Image resizing and optimization
  - PDF manipulation (merge, split)
  - Virus scanning integration (ClamAV)
  - Generate file hashes (MD5, SHA256)
- **Methods**:
  - `uploadFile(UploadedFile $file, string $disk = 'local'): string`
  - `deleteFile(string $path, string $disk = 'local'): bool`
  - `resizeImage(string $path, int $width, int $height): string`
  - `mergePdfs(array $paths): string`
  - `getFileHash(string $path, string $algo = 'sha256'): string`
  - `scanForVirus(string $path): bool`

#### 6. **AuditHelper**
- **Purpose**: Audit trail utilities
- **Features**:
  - Track model changes (created, updated, deleted)
  - Store old and new values
  - Identify user who made changes
  - JSON diff for complex fields
  - Restore previous versions
- **Database**: `audits` table
- **Methods**:
  - `logChange(Model $model, string $action): void`
  - `getAuditTrail(Model $model): Collection`
  - `restoreVersion(Model $model, int $versionId): Model`
  - `compareVersions(int $versionA, int $versionB): array`

### Helper Traits (4 Traits)

#### 1. **HasUuid**
- **Purpose**: Add UUID primary key to models
- **Features**:
  - Auto-generate UUID on create
  - Override default incrementing ID
  - Use UUID v4 by default

#### 2. **Cacheable**
- **Purpose**: Easy model caching
- **Features**:
  - Cache model queries
  - Cache relationships
  - Configurable TTL
  - Cache invalidation on update/delete

#### 3. **Searchable**
- **Purpose**: Add full-text search capabilities
- **Features**:
  - Search across multiple fields
  - Weighted search results
  - Highlight search terms
  - Integration with Laravel Scout (optional)

#### 4. **Exportable**
- **Purpose**: Export model data to various formats
- **Features**:
  - Export to CSV, Excel, PDF
  - Configurable columns
  - Filtering and sorting
  - Batch export for large datasets

## Part 3: Utility Commands & Configuration (2-3 hours)

### Artisan Commands to Create (5 Commands)

#### 1. **CleanupCommand**
- **Purpose**: Cleanup old data
- **Features**:
  - Delete old activity logs (configurable retention period)
  - Delete soft-deleted records permanently
  - Cleanup temp files
  - Prune old notifications
  - Schedule: daily

#### 2. **GenerateReportCommand**
- **Purpose**: Generate scheduled reports
- **Features**:
  - Generate daily/weekly/monthly reports
  - Email reports to recipients
  - Store in database
  - Support all report types (profit, sales, inventory)
  - Schedule: configurable

#### 3. **CacheWarmupCommand**
- **Purpose**: Pre-populate cache with frequently accessed data
- **Features**:
  - Cache common queries
  - Cache configuration
  - Cache routes
  - Cache views
  - Run on deployment

#### 4. **HealthCheckCommand**
- **Purpose**: System health check
- **Features**:
  - Check database connectivity
  - Check cache connectivity (Redis)
  - Check queue workers
  - Check disk space
  - Check external API connectivity
  - Return status code for monitoring

#### 5. **SyncExchangeRatesCommand**
- **Purpose**: Sync currency exchange rates
- **Features**:
  - Fetch latest rates from API
  - Store in database/cache
  - Handle API failures gracefully
  - Schedule: daily

### Configuration Files (5 Config Files)

#### 1. **config/security.php**
```php
return [
    'headers' => [
        'X-Frame-Options' => 'DENY',
        'X-Content-Type-Options' => 'nosniff',
        'X-XSS-Protection' => '1; mode=block',
        'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
        'Content-Security-Policy' => "default-src 'self'",
        'Referrer-Policy' => 'no-referrer-when-downgrade',
    ],
    
    'sanitize' => [
        'enabled' => true,
        'whitelist_fields' => ['description', 'notes', 'terms'],
        'allowed_tags' => '<p><br><strong><em><ul><ol><li>',
    ],
];
```

#### 2. **config/currency.php**
```php
return [
    'api_provider' => env('CURRENCY_API_PROVIDER', 'fixer'),
    'api_key' => env('CURRENCY_API_KEY'),
    'base_currency' => env('CURRENCY_BASE', 'EUR'),
    'cache_ttl' => 86400, // 24 hours
    
    'supported_currencies' => [
        'EUR', 'USD', 'GBP', 'JPY', 'CHF', 'CAD', 'AUD', 'NZD',
    ],
    
    'format' => [
        'EUR' => ['symbol' => '€', 'decimals' => 2],
        'USD' => ['symbol' => '$', 'decimals' => 2],
        'GBP' => ['symbol' => '£', 'decimals' => 2],
        'JPY' => ['symbol' => '¥', 'decimals' => 0],
    ],
];
```

#### 3. **config/activity.php**
```php
return [
    'enabled' => env('ACTIVITY_LOGGING_ENABLED', true),
    'retention_days' => env('ACTIVITY_RETENTION_DAYS', 90),
    'log_level' => env('ACTIVITY_LOG_LEVEL', 'info'),
    
    'exclude_urls' => [
        '/health',
        '/metrics',
        '/_debugbar',
    ],
    
    'exclude_actions' => [
        'view', // Don't log read operations
    ],
    
    'sanitize_request' => true,
    'sanitize_fields' => ['password', 'password_confirmation', 'token', 'api_key'],
];
```

#### 4. **config/rate-limit.php**
```php
return [
    'default' => [
        'requests' => 60,
        'period' => 60, // seconds
    ],
    
    'guest' => [
        'requests' => 30,
        'period' => 60,
    ],
    
    'authenticated' => [
        'requests' => 120,
        'period' => 60,
    ],
    
    'admin' => [
        'requests' => 300,
        'period' => 60,
    ],
    
    'api' => [
        'requests' => 1000,
        'period' => 3600, // per hour
    ],
];
```

#### 5. **config/tenant.php**
```php
return [
    'enabled' => env('TENANT_ENABLED', false),
    'mode' => env('TENANT_MODE', 'subdomain'), // subdomain, domain, path
    'central_domain' => env('TENANT_CENTRAL_DOMAIN', 'app.test'),
    
    'database' => [
        'separate' => false, // Use separate databases per tenant
        'prefix' => '', // Table prefix for tenant tables
    ],
    
    'features' => [
        'auto_create_tenant' => false,
        'trial_days' => 14,
        'require_subscription' => true,
    ],
];
```

## Part 4: Testing (2-3 hours)

### Test Coverage (15+ Tests)

#### Middleware Tests (8 tests)
- `tests/Unit/Middleware/ActivityLogMiddlewareTest.php`
  - `it_logs_user_activity`
  - `it_excludes_sensitive_data`
  
- `tests/Unit/Middleware/SecurityHeadersMiddlewareTest.php`
  - `it_adds_security_headers_to_response`
  
- `tests/Unit/Middleware/RequestSanitizerMiddlewareTest.php`
  - `it_sanitizes_input_data`
  - `it_preserves_whitelisted_html`
  
- `tests/Unit/Middleware/RateLimitByUserMiddlewareTest.php`
  - `it_limits_requests_per_user`
  - `it_returns_429_when_limit_exceeded`
  
- `tests/Unit/Middleware/LocalizationMiddlewareTest.php`
  - `it_sets_locale_from_user_preference`

#### Helper Service Tests (7+ tests)
- `tests/Unit/Helpers/CurrencyConverterTest.php`
  - `it_converts_currency`
  - `it_caches_exchange_rates`
  
- `tests/Unit/Helpers/DateHelperTest.php`
  - `it_adds_business_days`
  - `it_calculates_business_days_between_dates`
  
- `tests/Unit/Helpers/NumberFormatterTest.php`
  - `it_formats_currency`
  - `it_formats_file_size`
  
- `tests/Unit/Helpers/ValidationHelperTest.php`
  - `it_validates_vat_number`

## Implementation Guidelines

### SOLID Principles Application

**Single Responsibility:**
```php
// Each middleware has ONE responsibility
class ActivityLogMiddleware
{
    // Only logs activity, doesn't handle authorization or validation
}
```

**Open/Closed:**
```php
// Use interfaces for extensibility
interface CurrencyProviderInterface
{
    public function getRate(string $from, string $to): float;
}

class FixerCurrencyProvider implements CurrencyProviderInterface { }
class OpenExchangeRateProvider implements CurrencyProviderInterface { }
```

**Dependency Inversion:**
```php
class CurrencyConverter
{
    public function __construct(
        private CurrencyProviderInterface $provider
    ) {}
}
```

### DRY Principles Application

**Avoid Duplication:**
```php
// Extract common middleware logic to trait
trait LogsActivity
{
    protected function logActivity(Request $request): void
    {
        // Common logging logic
    }
}
```

### Early Return Pattern

```php
public function handle(Request $request, Closure $next)
{
    // Guard clauses
    if (!$this->shouldLog($request)) {
        return $next($request);
    }
    
    if (!$this->isValidUser($request)) {
        return $next($request);
    }
    
    // Main logic
    $this->logActivity($request);
    return $next($request);
}
```

## File Structure

```
app/
├── Http/
│   └── Middleware/
│       ├── ActivityLogMiddleware.php
│       ├── TenantMiddleware.php
│       ├── ApiVersionMiddleware.php
│       ├── SecurityHeadersMiddleware.php
│       ├── RequestSanitizerMiddleware.php
│       ├── RateLimitByUserMiddleware.php
│       ├── LocalizationMiddleware.php
│       └── PerformanceMonitoringMiddleware.php
│
├── Services/
│   └── Helpers/
│       ├── CurrencyConverter.php
│       ├── DateHelper.php
│       ├── NumberFormatter.php
│       ├── ValidationHelper.php
│       ├── FileHelper.php
│       └── AuditHelper.php
│
├── Traits/
│   ├── HasUuid.php
│   ├── Cacheable.php
│   ├── Searchable.php
│   └── Exportable.php
│
├── Console/
│   └── Commands/
│       ├── CleanupCommand.php
│       ├── GenerateReportCommand.php
│       ├── CacheWarmupCommand.php
│       ├── HealthCheckCommand.php
│       └── SyncExchangeRatesCommand.php
│
└── Models/
    ├── ActivityLog.php
    ├── Tenant.php
    └── PerformanceMetric.php

database/
└── migrations/
    ├── xxxx_create_activity_logs_table.php
    ├── xxxx_create_tenants_table.php
    └── xxxx_create_performance_metrics_table.php

config/
├── security.php
├── currency.php
├── activity.php
├── rate-limit.php
└── tenant.php

tests/
└── Unit/
    ├── Middleware/
    │   ├── ActivityLogMiddlewareTest.php
    │   ├── SecurityHeadersMiddlewareTest.php
    │   ├── RequestSanitizerMiddlewareTest.php
    │   ├── RateLimitByUserMiddlewareTest.php
    │   └── LocalizationMiddlewareTest.php
    │
    └── Helpers/
        ├── CurrencyConverterTest.php
        ├── DateHelperTest.php
        ├── NumberFormatterTest.php
        └── ValidationHelperTest.php
```

## Dependencies to Install

```bash
composer require geoip2/geoip2:~2.0    # For IP geolocation
composer require myclabs/php-enum       # For enums (if not using PHP 8.1+)
composer require spatie/laravel-activitylog  # Alternative for activity logging
composer require spatie/laravel-permission   # Already installed
composer require league/csv             # For CSV export
composer require maatwebsite/excel      # For Excel export
composer require intervention/image     # For image manipulation
composer require predis/predis          # For Redis
```

## Success Criteria

- ✅ All 8 middleware classes implemented with proper error handling
- ✅ All 6 helper services implemented with SOLID/DRY principles
- ✅ All 4 traits implemented and tested
- ✅ All 5 Artisan commands functional
- ✅ All 5 configuration files created
- ✅ 15+ comprehensive tests with it_* naming
- ✅ All middleware registered in Kernel
- ✅ All services registered in AppServiceProvider
- ✅ Documentation updated (README, guidelines)
- ✅ Code follows early return pattern
- ✅ No code duplication (DRY)

## Integration Points

- Integrates with existing authentication system (Phase 1)
- Works with invoice/quote/SO models (Phases 2-4)
- Supports Peppol compliance requirements (Phase 0)
- Enhances email and notification systems (Phase 7)
- Provides utilities for future payment gateway integration (Phase 5)

## Timeline

1. **Day 1 (4-5h)**: Implement middleware classes and migrations
2. **Day 2 (4-5h)**: Implement helper services and traits
3. **Day 3 (2-3h)**: Implement Artisan commands and configuration
4. **Day 4 (2-3h)**: Write comprehensive tests and documentation

## Notes

- Use dependency injection throughout
- Apply SOLID principles to all classes
- Follow Laravel conventions and best practices
- Write descriptive comments for complex logic
- Use type hints for all parameters and return types
- Handle errors gracefully with proper logging
- Consider performance implications (caching, lazy loading)
- Ensure backward compatibility with existing code
- Update .junie/guidelines.md and .github/copilot-instructions.md after completion

---

**Start Phase 9 implementation with:** `@copilot continue`
