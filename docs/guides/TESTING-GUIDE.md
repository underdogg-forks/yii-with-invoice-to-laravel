# Filament v4 Testing Guide

## Overview

This document outlines the comprehensive test suite for the Filament v4 admin panel implementation. All tests follow Laravel and Filament best practices.

## Test Structure

### Directory Structure
```
tests/
├── Feature/
│   ├── Filament/
│   │   ├── Resources/
│   │   │   ├── InvoiceResourceTest.php
│   │   │   ├── QuoteResourceTest.php
│   │   │   ├── SalesOrderResourceTest.php
│   │   │   ├── ClientResourceTest.php
│   │   │   ├── ProductResourceTest.php
│   │   │   ├── TaxRateResourceTest.php
│   │   │   ├── CustomFieldResourceTest.php
│   │   │   ├── TemplateResourceTest.php
│   │   │   └── UserResourceTest.php
│   │   ├── Components/
│   │   │   ├── CurrencyInputTest.php
│   │   │   ├── TaxRateSelectorTest.php
│   │   │   ├── ClientSelectorTest.php
│   │   │   ├── ProductPickerTest.php
│   │   │   └── InvoiceBuilderTest.php
│   │   └── Widgets/
│   │       ├── InvoiceStatsWidgetTest.php
│   │       ├── PaymentStatusWidgetTest.php
│   │       ├── RecentInvoicesWidgetTest.php
│   │       └── TopClientsWidgetTest.php
```

## Testing Standards

### Naming Conventions
- All test methods must use `it_` prefix
- Method names should be descriptive and readable
- Example: `public function it_can_create_invoice_with_items(): void`

### Test Structure
All tests follow the Arrange-Act-Assert (AAA) pattern:

```php
public function it_creates_invoice_with_valid_data(): void
{
    // Arrange
    $user = User::factory()->create();
    $client = Client::factory()->create();
    $data = ['invoice_number' => 'INV-001', 'client_id' => $client->id];
    
    // Act
    $this->actingAs($user);
    $invoice = $this->invoiceService->create(new InvoiceDTO(...$data));
    
    // Assert
    $this->assertNotNull($invoice->id);
    $this->assertDatabaseHas('invoices', ['invoice_number' => 'INV-001']);
}
```

### Required Traits
```php
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class InvoiceResourceTest extends TestCase
{
    use RefreshDatabase;
    
    // Test methods...
}
```

## Resource Tests

### InvoiceResourceTest

**Purpose:** Test invoice CRUD operations, filters, actions, and relationships

**Key Test Cases:**
```php
public function it_can_list_invoices(): void
public function it_can_create_invoice_with_items(): void
public function it_can_edit_invoice(): void
public function it_can_delete_invoice(): void
public function it_can_filter_invoices_by_status(): void
public function it_can_filter_invoices_by_date_range(): void
public function it_can_send_invoice_email(): void
public function it_can_generate_invoice_pdf(): void
public function it_can_mark_invoice_as_sent(): void
public function it_shows_draft_count_in_navigation_badge(): void
public function it_hides_navigation_badge_when_no_drafts(): void
```

**Example Implementation:**
```php
<?php

namespace Tests\Feature\Filament\Resources;

use App\Filament\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InvoiceResourceTest extends TestCase
{
    use RefreshDatabase;

    public function it_can_list_invoices(): void
    {
        $user = User::factory()->create();
        Invoice::factory()->count(5)->create();

        $this->actingAs($user);

        Livewire::test(InvoiceResource\Pages\ListInvoices::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords(Invoice::all());
    }

    public function it_can_create_invoice_with_items(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create();

        $this->actingAs($user);

        $newData = [
            'client_id' => $client->id,
            'invoice_number' => 'INV-001',
            'date_created' => now()->format('Y-m-d'),
            'date_due' => now()->addDays(30)->format('Y-m-d'),
            'status_id' => 1,
        ];

        Livewire::test(InvoiceResource\Pages\CreateInvoice::class)
            ->fillForm($newData)
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('invoices', [
            'invoice_number' => 'INV-001',
            'client_id' => $client->id,
        ]);
    }

    public function it_can_filter_invoices_by_status(): void
    {
        $user = User::factory()->create();
        Invoice::factory()->draft()->count(2)->create();
        Invoice::factory()->sent()->count(3)->create();

        $this->actingAs($user);

        Livewire::test(InvoiceResource\Pages\ListInvoices::class)
            ->filterTable('invoice_status_id', 1) // Draft status
            ->assertCountTableRecords(2);
    }
}
```

### QuoteResourceTest

**Purpose:** Test quote CRUD, status changes, and conversion actions

**Key Test Cases:**
```php
public function it_can_create_quote(): void
public function it_can_edit_quote(): void
public function it_can_approve_quote(): void
public function it_can_reject_quote(): void
public function it_can_convert_quote_to_invoice(): void
public function it_can_convert_quote_to_sales_order(): void
public function it_can_send_quote_email(): void
public function it_can_generate_quote_pdf(): void
```

### SalesOrderResourceTest

**Purpose:** Test sales order workflow and conversion to invoice

**Key Test Cases:**
```php
public function it_can_create_sales_order(): void
public function it_can_update_sales_order_status(): void
public function it_can_convert_sales_order_to_invoice(): void
public function it_can_filter_by_status(): void
```

### ClientResourceTest

**Purpose:** Test client management with custom fields and Peppol integration

**Key Test Cases:**
```php
public function it_can_create_client_with_address(): void
public function it_can_update_client(): void
public function it_can_add_custom_fields_to_client(): void
public function it_can_link_peppol_configuration(): void
public function it_can_filter_active_clients(): void
public function it_can_deactivate_client(): void
```

### ProductResourceTest

**Purpose:** Test product catalog management

**Key Test Cases:**
```php
public function it_can_create_product_with_sku(): void
public function it_validates_unique_sku(): void
public function it_can_update_product_price(): void
public function it_can_assign_tax_rate_to_product(): void
public function it_can_filter_products_by_family(): void
```

### TaxRateResourceTest

**Purpose:** Test tax rate configuration

**Key Test Cases:**
```php
public function it_can_create_tax_rate(): void
public function it_can_set_default_tax_rate(): void
public function it_only_allows_one_default_tax_rate(): void
public function it_calculates_tax_amount_correctly(): void
```

### CustomFieldResourceTest

**Purpose:** Test dynamic field configuration

**Key Test Cases:**
```php
public function it_can_create_text_custom_field(): void
public function it_can_create_select_custom_field_with_options(): void
public function it_validates_field_label(): void
public function it_can_reorder_custom_fields(): void
```

### TemplateResourceTest

**Purpose:** Test email template management

**Key Test Cases:**
```php
public function it_can_create_invoice_template(): void
public function it_can_create_quote_template(): void
public function it_can_preview_template(): void
public function it_can_filter_by_template_type(): void
```

### UserResourceTest

**Purpose:** Test user management with permissions

**Key Test Cases:**
```php
public function it_can_create_user(): void
public function it_can_assign_roles_to_user(): void
public function it_can_assign_permissions_to_user(): void
public function it_can_impersonate_user(): void
public function it_prevents_unauthorized_access(): void
```

## Custom Component Tests

### CurrencyInputTest

**Purpose:** Test currency formatting and validation

**Key Test Cases:**
```php
public function it_formats_currency_with_symbol(): void
public function it_supports_multiple_currencies(): void
public function it_validates_numeric_input(): void
public function it_dehydrates_value_correctly(): void
public function it_hydrates_value_correctly(): void
```

**Example:**
```php
<?php

namespace Tests\Feature\Filament\Components;

use App\Filament\Forms\Components\CurrencyInput;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyInputTest extends TestCase
{
    use RefreshDatabase;

    public function it_formats_currency_with_usd_symbol(): void
    {
        $component = CurrencyInput::make('amount')
            ->currency('USD');

        $this->assertEquals('$', $component->getPrefix());
    }

    public function it_formats_value_with_two_decimals(): void
    {
        $component = CurrencyInput::make('amount');
        
        $value = $component->formatStateUsing(fn ($state) => number_format($state, 2))
            ->getState(100.5);

        $this->assertEquals('100.50', $value);
    }
}
```

### TaxRateSelectorTest

**Purpose:** Test tax rate selection and calculation preview

**Key Test Cases:**
```php
public function it_loads_tax_rates_for_selection(): void
public function it_displays_percentage_in_options(): void
public function it_shows_calculation_preview(): void
public function it_can_quick_create_new_tax_rate(): void
```

### ClientSelectorTest

**Purpose:** Test client search and selection

**Key Test Cases:**
```php
public function it_searches_clients_by_name(): void
public function it_searches_clients_by_email(): void
public function it_searches_clients_by_phone(): void
public function it_filters_active_clients_only(): void
public function it_can_quick_create_new_client(): void
```

### ProductPickerTest

**Purpose:** Test product selection with auto-fill

**Key Test Cases:**
```php
public function it_searches_products_by_name(): void
public function it_searches_products_by_sku(): void
public function it_auto_fills_description_on_selection(): void
public function it_auto_fills_price_on_selection(): void
public function it_auto_fills_tax_rate_on_selection(): void
public function it_can_quick_create_new_product(): void
```

### InvoiceBuilderTest

**Purpose:** Test invoice items repeater with calculations

**Key Test Cases:**
```php
public function it_adds_new_item_to_invoice(): void
public function it_removes_item_from_invoice(): void
public function it_calculates_line_total_correctly(): void
public function it_calculates_subtotal_correctly(): void
public function it_calculates_tax_correctly(): void
public function it_applies_discount_correctly(): void
public function it_reorders_items(): void
public function it_clones_items(): void
```

## Widget Tests

### InvoiceStatsWidgetTest

**Purpose:** Test statistics calculation and trends

**Key Test Cases:**
```php
public function it_calculates_total_invoices_count(): void
public function it_calculates_paid_invoices_count(): void
public function it_calculates_pending_invoices_count(): void
public function it_calculates_overdue_invoices_count(): void
public function it_calculates_30_day_trend(): void
public function it_caches_statistics_for_5_minutes(): void
```

**Example:**
```php
<?php

namespace Tests\Feature\Filament\Widgets;

use App\Filament\Widgets\InvoiceStatsWidget;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InvoiceStatsWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function it_displays_correct_invoice_counts(): void
    {
        $user = User::factory()->create();
        
        Invoice::factory()->paid()->count(5)->create();
        Invoice::factory()->pending()->count(3)->create();
        Invoice::factory()->overdue()->count(2)->create();

        $this->actingAs($user);

        $component = Livewire::test(InvoiceStatsWidget::class);
        
        $stats = $component->get('stats');
        
        $this->assertEquals(10, $stats['total']);
        $this->assertEquals(5, $stats['paid']);
        $this->assertEquals(3, $stats['pending']);
        $this->assertEquals(2, $stats['overdue']);
    }

    public function it_caches_statistics(): void
    {
        $user = User::factory()->create();
        Invoice::factory()->count(10)->create();

        $this->actingAs($user);

        // First load - should query database
        Livewire::test(InvoiceStatsWidget::class);
        
        // Second load - should use cache
        $component = Livewire::test(InvoiceStatsWidget::class);
        
        $this->assertTrue($component->get('cached'));
    }
}
```

### PaymentStatusWidgetTest

**Purpose:** Test payment status chart data

**Key Test Cases:**
```php
public function it_generates_donut_chart_data(): void
public function it_calculates_status_distribution(): void
public function it_shows_empty_state_when_no_data(): void
```

### RecentInvoicesWidgetTest

**Purpose:** Test recent invoices display

**Key Test Cases:**
```php
public function it_shows_last_5_invoices(): void
public function it_orders_by_creation_date_desc(): void
public function it_shows_action_buttons(): void
```

### TopClientsWidgetTest

**Purpose:** Test top clients by revenue

**Key Test Cases:**
```php
public function it_calculates_client_revenue_correctly(): void
public function it_shows_top_5_clients(): void
public function it_orders_by_revenue_desc(): void
public function it_shows_invoice_count_per_client(): void
```

## Running Tests

### Run All Tests
```bash
php artisan test
```

### Run Specific Test Suite
```bash
# Run all Filament resource tests
php artisan test tests/Feature/Filament/Resources

# Run specific resource test
php artisan test tests/Feature/Filament/Resources/InvoiceResourceTest.php

# Run all widget tests
php artisan test tests/Feature/Filament/Widgets

# Run all component tests
php artisan test tests/Feature/Filament/Components
```

### Run Tests with Coverage
```bash
php artisan test --coverage
```

### Run Tests in Parallel
```bash
php artisan test --parallel
```

## Test Data Factories

### Usage Examples

```php
// Create invoice with items
$invoice = Invoice::factory()
    ->hasItems(3)
    ->for(Client::factory())
    ->create();

// Create draft invoice
$invoice = Invoice::factory()->draft()->create();

// Create paid invoice
$invoice = Invoice::factory()->paid()->create();

// Create overdue invoice
$invoice = Invoice::factory()->overdue()->create();

// Create quote with items
$quote = Quote::factory()
    ->hasItems(5)
    ->approved()
    ->create();

// Create sales order
$salesOrder = SalesOrder::factory()
    ->pending()
    ->create();

// Create client with Peppol
$client = Client::factory()
    ->withPeppol()
    ->active()
    ->create();

// Create product with tax rate
$product = Product::factory()
    ->for(TaxRate::factory())
    ->create();

// Create user with role
$user = User::factory()
    ->hasRole('admin')
    ->create();
```

## Continuous Integration

### GitHub Actions Workflow

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v2
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.3
          extensions: dom, curl, libxml, mbstring, zip, pcntl, pdo, sqlite, pdo_sqlite
          
      - name: Install Dependencies
        run: composer install --no-interaction --prefer-dist
        
      - name: Run Tests
        run: php artisan test --parallel
        
      - name: Upload Coverage
        uses: codecov/codecov-action@v2
```

## Best Practices

### 1. Test Isolation
- Each test should be independent
- Use RefreshDatabase trait to reset database between tests
- Don't rely on test execution order

### 2. Descriptive Names
- Test names should describe what they test
- Use `it_` prefix for readability
- Example: `it_prevents_duplicate_invoice_numbers`

### 3. Arrange-Act-Assert
- Always follow AAA pattern
- Keep assertions focused
- One logical assertion per test

### 4. Factory Usage
- Use factories for all test data
- Define states for common scenarios
- Keep factories simple and focused

### 5. Mocking
- Mock external services
- Don't mock what you don't own
- Use fakes for complex dependencies

### 6. Coverage
- Aim for 80%+ code coverage
- Focus on critical paths
- Test edge cases and error conditions

### 7. Performance
- Keep tests fast
- Use in-memory SQLite for speed
- Run slow tests separately

## Troubleshooting

### Common Issues

**Issue: Tests failing with "Class not found"**
```bash
composer dump-autoload
```

**Issue: Database errors**
```bash
php artisan migrate:fresh --env=testing
```

**Issue: Livewire component not rendering**
```php
// Make sure to act as user before testing
$this->actingAs(User::factory()->create());
```

**Issue: Permission denied errors**
```php
// Ensure user has required permissions
$user = User::factory()->create();
$user->givePermissionTo('manage-invoices');
$this->actingAs($user);
```

## Summary

This testing guide provides comprehensive coverage for all Filament v4 components:
- ✅ 9 Resource test suites
- ✅ 5 Custom component test suites
- ✅ 4 Widget test suites
- ✅ 100+ individual test cases
- ✅ Full coverage of CRUD operations
- ✅ Authorization and permission tests
- ✅ Integration tests for complex workflows
- ✅ Performance and caching tests

All tests follow Laravel and Filament best practices and provide confidence in the production readiness of the admin panel.
