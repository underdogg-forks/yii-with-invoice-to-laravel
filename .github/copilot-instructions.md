# Copilot Instructions - Laravel Invoice Application

## Project Context

This is a Laravel 12 application migrated from Yii3, focused on invoice management with Peppol (Pan-European Public Procurement OnLine) support. The migration is incremental, preserving business logic while adapting to Laravel conventions.

## Core Development Principles

### SOLID Principles

**Always apply SOLID principles when generating code:**

1. **Single Responsibility**: Each class has one clear purpose
2. **Open/Closed**: Use interfaces and abstract classes for extensibility
3. **Liskov Substitution**: Ensure proper inheritance hierarchies
4. **Interface Segregation**: Create focused, client-specific interfaces
5. **Dependency Inversion**: Inject abstractions (interfaces), not concrete classes

### DRY (Don't Repeat Yourself)

- Extract common logic to traits, base classes, or services
- Use helper functions for repeated operations
- Leverage template inheritance and composition
- Avoid duplicating validation rules, calculations, or business logic

### Early Return Pattern

**Always use guard clauses and fail-fast:**

```php
// ✅ Preferred
public function process($data): bool
{
    if (!$data) {
        return false;
    }
    
    if (!$this->validate($data)) {
        return false;
    }
    
    // Main logic here
    return $this->finalize($data);
}

// ❌ Avoid
public function process($data): bool
{
    if ($data) {
        if ($this->validate($data)) {
            return $this->finalize($data);
        }
    }
    return false;
}
```

### Dynamic Programming

Use memoization and caching for expensive operations:

```php
// Cache database queries
Cache::remember('key', 3600, fn() => Model::expensive()->get());

// Memoize within request
private array $cache = [];
if (isset($this->cache[$key])) {
    return $this->cache[$key];
}
```

## Code Generation Guidelines

### Testing
When generating tests:
- ALWAYS use `it_` prefix for test method names
- Make names grammatically correct and descriptive
- Follow Arrange, Act, Assert pattern
- Use factories for test data
- Mock external dependencies in unit tests
- Use RefreshDatabase trait in feature tests

Example:
```php
public function it_creates_invoice_with_peppol_data(): void
{
    // Arrange
    $client = Client::factory()->withPeppol()->create();
    $data = ['invoice_number' => 'INV-001', ...];
    
    // Act
    $invoice = $this->invoiceService->create($data);
    
    // Assert
    $this->assertNotNull($invoice->id);
    $this->assertDatabaseHas('invoices', ['invoice_number' => 'INV-001']);
}
```

### Architecture Patterns
When creating new features:
1. **Start with Model and Migration**: Define database structure first
2. **Create Factory**: For testing and seeding
3. **Create DTO**: For type-safe data transfer
4. **Create Repository Interface**: For data access abstraction
5. **Create Repository**: Implement the interface
6. **Create Service**: For business logic (inject repository interface)
7. **Create Controller**: For HTTP handling only (inject service)
8. **Create Views**: Plain PHP initially (not Blade)
9. **Write Tests**: Comprehensive coverage with it_* methods
10. **Add Routes**: With appropriate middleware (auth, throttle)

### Naming Conventions

#### Invoice Numbering
- **Model**: `InvoiceNumbering` (NOT InvoiceGroup)
- **Table**: `invoice_numbering` (NOT invoice_groups)
- **Foreign Key**: `numbering_id` (NOT group_id)
- **Relationship**: `$invoice->numbering()` (NOT $invoice->group())

**Why:** "Numbering" better describes the purpose - managing invoice number generation schemes. "Group" is too generic and doesn't convey the specific functionality.

**Example:**
```php
// Correct naming
class InvoiceNumbering extends Model {
    protected $table = 'invoice_numbering';
    
    public function invoices(): HasMany {
        return $this->hasMany(Invoice::class, 'numbering_id');
    }
    
    public function generateNextNumber(): string { /* ... */ }
}

// Usage in Invoice model
class Invoice extends Model {
    public function numbering(): BelongsTo {
        return $this->belongsTo(InvoiceNumbering::class, 'numbering_id');
    }
}

// Controller usage
$numbering = InvoiceNumbering::find($numberingId);
$invoiceNumber = $numbering->generateNextNumber();
```

#### General Naming Guidelines
- Use explicit, descriptive names that reflect purpose
- Avoid generic terms like "group", "type", "category" without context
- Prefer domain-specific terminology (e.g., "numbering", "taxation", "shipping")
- Keep naming consistent across models, migrations, and relationships

### Authentication & Authorization
- Use Spatie Laravel-Permission for RBAC
- Create policies for all models
- Apply middleware to routes:
  - `auth` for authenticated routes
  - `permission:name` or `can:ability` for specific permissions
- Always check authorization in controllers using policies

Example:
```php
Route::middleware(['auth', 'permission:manage-invoices'])
    ->group(function () {
        Route::resource('invoices', InvoiceController::class);
    });
```

### Service Layer Pattern
Services should:
- Accept DTOs as parameters
- Return models or collections
- Handle business logic and validation
- Delegate data access to repositories
- Throw descriptive exceptions on errors

```php
class InvoiceService
{
    public function __construct(
        private InvoiceRepository $repository,
        private PeppolService $peppolService
    ) {}
    
    public function create(InvoiceDTO $dto): Invoice
    {
        // Business logic here
        $invoice = $this->repository->create($dto->toArray());
        
        if ($dto->generatePeppol) {
            $this->peppolService->generateXml($invoice);
        }
        
        return $invoice;
    }
}
```

### Repository Pattern
Repositories should:
- Provide CRUD operations
- Return Eloquent models or collections
- Use eager loading to prevent N+1
- Not contain business logic

```php
class InvoiceRepository
{
    public function findWithRelations(int $id): ?Invoice
    {
        return Invoice::with(['client', 'items', 'peppolPayments'])
            ->find($id);
    }
    
    public function create(array $data): Invoice
    {
        return Invoice::create($data);
    }
}
```

### DTOs (Data Transfer Objects)
- Use PHP 8.3 constructor property promotion
- Provide `fromModel()` and `toArray()` methods
- Validate data structure, not business rules
- Located in `app/DTOs/`

```php
class InvoiceDTO
{
    public function __construct(
        public ?int $id = null,
        public int $client_id,
        public string $invoice_number,
        public float $total_amount,
        public bool $generatePeppol = false,
    ) {}
    
    public static function fromModel(Invoice $invoice): self
    {
        return new self(
            id: $invoice->id,
            client_id: $invoice->client_id,
            invoice_number: $invoice->invoice_number,
            total_amount: $invoice->total_amount,
        );
    }
    
    public function toArray(): array
    {
        return [
            'client_id' => $this->client_id,
            'invoice_number' => $this->invoice_number,
            'total_amount' => $this->total_amount,
        ];
    }
}
```

### Views
- Use plain PHP templates (not Blade) initially
- Always use route() helper for URLs
- Always escape output with htmlspecialchars()
- Include CSRF token in all forms
- Structure: layout includes content from pages

```php
<?php ob_start(); ?>
<h1>Page Title</h1>
<form method="POST" action="<?= route('resource.store') ?>">
    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
    <input name="field" value="<?= htmlspecialchars($value ?? '') ?>">
</form>
<?php
$content = ob_get_clean();
$title = 'Page Title';
include __DIR__ . '/../layout.php';
?>
```

### Controllers
- Keep thin - delegate to services
- Validate using form requests or inline rules
- Return views or JSON responses
- Handle HTTP concerns only

```php
class InvoiceController extends Controller
{
    public function __construct(
        private InvoiceService $service
    ) {}
    
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([...]);
        
        $dto = new InvoiceDTO(...$validated);
        $invoice = $this->service->create($dto);
        
        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Invoice created');
    }
}
```

### Migrations
- Use descriptive names
- Include foreign key constraints
- Add indexes for frequently queried columns
- Make reversible (down() method)

```php
Schema::create('invoices', function (Blueprint $table) {
    $table->id();
    $table->foreignId('client_id')->constrained()->onDelete('cascade');
    $table->string('invoice_number')->unique();
    $table->decimal('total_amount', 10, 2);
    $table->timestamps();
    
    $table->index('invoice_number');
});
```

## Peppol-Specific Requirements

### Validation Rules
- Endpoint IDs: valid email format
- Scheme IDs: exactly 4 characters
- UN/CEFACT codes: exactly 3 characters
- Tax scheme IDs: max 7 characters
- Follow EN 16931 standard

### UBL XML Generation
- Use Sabre/XML library
- Validate against Peppol BIS 3.0 specification
- Include all required elements
- Test against validator (Ecosio, OpenPeppol)

### Provider Integration
- Support StoreCove API
- Support direct Peppol network
- Handle async responses
- Log all API interactions

## Performance Optimization

### Query Optimization
```php
// Always use eager loading
Invoice::with(['client', 'items.product', 'peppolPayments'])->get();

// Use select() to limit columns
Invoice::select(['id', 'invoice_number', 'total_amount'])->get();

// Use chunk() for large datasets
Invoice::chunk(100, function ($invoices) {
    // Process batch
});
```

### Caching
```php
// Cache frequently accessed data
$settings = Cache::remember('app_settings', 3600, function () {
    return Setting::all();
});
```

## Security Requirements

### Input Validation
- Validate all input using form requests
- Sanitize HTML output
- Use parameterized queries (Eloquent does this)

### Authentication
- Use Laravel Sanctum for API tokens
- Implement 2FA for sensitive operations
- Log authentication attempts

### Authorization
- Define policies for all models
- Check permissions before actions
- Log authorization failures

## Error Handling

### Exceptions
```php
class InvoiceNotFoundException extends Exception {}
class PeppolValidationException extends Exception {}

// Use try-catch in services
try {
    $invoice = $this->service->create($dto);
} catch (PeppolValidationException $e) {
    return back()->withErrors(['peppol' => $e->getMessage()]);
}
```

### Logging
```php
Log::info('Invoice created', ['invoice_id' => $invoice->id]);
Log::error('Peppol validation failed', ['errors' => $errors]);
```

## Documentation

### Code Comments
- Document complex business logic
- Explain Peppol-specific requirements
- Add PHPDoc for public methods
- Avoid obvious comments

### Update After Changes
- .junie/guidelines.md
- .github/copilot-instructions.md
- README-LARAVEL.md

## Migration Progress Tracking

### Completed Modules
- ✅ Peppol entities (ClientPeppol, PaymentPeppol, UnitPeppol)
- ✅ Base models (Client, Invoice, Unit)
- ✅ Testing infrastructure
- ✅ Code quality improvements

### In Progress
- 🔄 Authentication & User Management
- ⏳ Full Invoice System
- ⏳ Client/Product Management

### Pending
- ⏳ Quote & Sales Order systems
- ⏳ Payment gateways
- ⏳ PDF/UBL XML generation
- ⏳ Email templates
- ⏳ Multi-language support

## Quick Reference

### Common Commands
```bash
# Run tests
vendor/bin/phpunit

# Run specific test
vendor/bin/phpunit --filter it_creates_invoice

# Generate key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Create migration
php artisan make:migration create_table_name

# Create model with factory
php artisan make:model ModelName -mf

# Clear cache
php artisan cache:clear
php artisan config:clear
```

### Useful Packages
- spatie/laravel-permission: RBAC
- barryvdh/laravel-debugbar: Development debugging
- spatie/laravel-query-builder: API filtering/sorting
- league/fractal: API transformations

## Remember
- Test everything
- Follow Laravel conventions
- Keep controllers thin
- Use DTOs for type safety
- Write descriptive test names with it_*
- Update documentation as you go
- Security first, always

## UI Development Guidelines (Phase 8+)

### Laravel Filament v4

**When creating admin interfaces, always use Filament v4:**

#### Resource Creation
- Create Filament Resources for all database models
- Keep resources thin - delegate business logic to Services
- Use Filament's built-in features (filters, search, bulk actions)
- Follow Filament naming conventions

Example:
```php
class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Sales';
    
    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('client_id')
                ->relationship('client', 'name')
                ->searchable()
                ->required(),
            TextInput::make('invoice_number')->required(),
            DatePicker::make('issue_date')->required(),
            Repeater::make('items')
                ->relationship()
                ->schema([
                    Select::make('product_id')->relationship('product', 'name'),
                    TextInput::make('quantity')->numeric(),
                    TextInput::make('price')->numeric()->prefix('$'),
                ]),
        ]);
    }
}
```

#### Widget Creation
- Use widgets for dashboard statistics
- Cache expensive queries
- Keep widgets focused on presentation

Example:
```php
class RevenueChartWidget extends ChartWidget
{
    protected function getData(): array
    {
        return Cache::remember('revenue-chart', 3600, function () {
            // Query and format data
        });
    }
}
```

#### Custom Actions
- Use Filament actions for operations
- Delegate to services for business logic
- Provide user feedback with notifications

Example:
```php
Action::make('sendEmail')
    ->icon('heroicon-o-envelope')
    ->action(function (Invoice $record) {
        app(EmailService::class)->sendInvoice($record);
        Notification::make()
            ->success()
            ->title('Email sent')
            ->send();
    });
```

### Blade Templates

**Always use Blade instead of plain PHP for views:**

#### Blade Directives
```blade
{{-- ✅ Good - Use Blade syntax --}}
@if ($invoice->isPaid())
    <span class="badge-success">Paid</span>
@else
    <span class="badge-warning">Pending</span>
@endif

@foreach ($invoices as $invoice)
    <x-invoice.card :invoice="$invoice" />
@endforeach

{{-- ❌ Bad - Don't use PHP tags --}}
<?php foreach ($invoices as $invoice): ?>
    ...
<?php endforeach; ?>
```

#### Blade Components
Create reusable components:
```blade
{{-- Define: resources/views/components/invoice/header.blade.php --}}
<div class="invoice-header">
    <h2>{{ $invoice->invoice_number }}</h2>
    <p>{{ $invoice->client->name }}</p>
</div>

{{-- Use: --}}
<x-invoice.header :invoice="$invoice" />
```

#### Data Escaping
```blade
{{-- Automatic escaping --}}
{{ $user->name }}

{{-- Raw HTML (only for trusted content) --}}
{!! $content !!}

{{-- JSON for JavaScript --}}
<script>
    const data = @json($data);
</script>
```

### View Structure

#### Layouts
```blade
{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>@yield('title', 'Invoice App')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <nav>@include('partials.navigation')</nav>
    
    <main>
        @yield('content')
    </main>
    
    <footer>@include('partials.footer')</footer>
</body>
</html>

{{-- Use in pages --}}
@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
    <h1>Invoices</h1>
    {{-- content here --}}
@endsection
```

### Component Best Practices

1. **Anonymous Components** - For simple, single-use components
2. **Class-based Components** - For complex logic
3. **Slot Usage** - For flexible content
4. **Props Typing** - Use type hints

Example class-based component:
```php
<?php

namespace App\View\Components\Invoice;

use App\Models\Invoice;
use Illuminate\View\Component;
use Illuminate\View\View;

class Header extends Component
{
    public function __construct(
        public Invoice $invoice
    ) {}
    
    public function render(): View
    {
        return view('components.invoice.header');
    }
}
```

### Filament & Blade Integration

1. **Use Filament for Admin Panel** - All CRUD operations
2. **Use Blade for Public Pages** - Customer-facing pages
3. **Share Components** - Reuse Blade components in both
4. **Consistent Styling** - Tailwind CSS throughout

### Testing

```php
// Filament resource testing
public function it_can_render_invoice_list(): void
{
    $this->actingAs(User::factory()->create());
    
    Livewire::test(InvoiceResource\Pages\ListInvoices::class)
        ->assertSuccessful();
}

// Blade component testing
public function it_renders_invoice_header(): void
{
    $invoice = Invoice::factory()->create();
    
    $view = $this->blade(
        '<x-invoice.header :invoice="$invoice" />',
        ['invoice' => $invoice]
    );
    
    $view->assertSee($invoice->invoice_number);
}
```

### Migration from Plain PHP

When converting existing PHP views to Blade:

1. **Replace `<?= ?>` with `{{ }}`**
2. **Replace `<?php if: ?>` with `@if`**
3. **Replace `<?php foreach: ?>` with `@foreach`**
4. **Extract repeated HTML to components**
5. **Use layouts instead of includes**
6. **Add proper CSRF protection** - `@csrf`
7. **Use route helpers** - `route('name')`

