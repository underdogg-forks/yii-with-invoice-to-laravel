# Project Guidelines - Laravel Invoice Application

## Testing Standards

### Test Method Naming
- All test methods start with `it_`
- Names should make grammatical sense and read like documentation
- Follow the "Arrange, Act, Assert" pattern

**Examples:**
```php
public function it_creates_a_client_peppol_with_valid_data(): void
public function it_prevents_unauthorized_access_to_peppol_routes(): void
public function it_validates_required_fields_on_client_creation(): void
```

### Test Structure
```php
public function it_does_something(): void
{
    // Arrange - Set up test data and dependencies
    $client = Client::factory()->create();
    
    // Act - Perform the action being tested
    $response = $this->post(route('clientpeppol.store', $client->id), $data);
    
    // Assert - Verify the expected outcome
    $response->assertStatus(201);
    $this->assertDatabaseHas('client_peppol', ['client_id' => $client->id]);
}
```

## Architecture Patterns

### DTOs (Data Transfer Objects)
- Use for type-safe data transfer between layers
- Replace Yii FormModel classes
- Located in `app/DTOs/`

### Service Layer
- Contains business logic
- Located in `app/Services/`
- Injected via constructor

### Repository Pattern
- Handles data access
- Located in `app/Repositories/`
- Provides abstraction over Eloquent models

### Controllers
- Handle HTTP requests/responses only
- Delegate business logic to services
- Return views or JSON responses

## Naming Conventions

### Invoice Numbering
- **Model**: `InvoiceNumbering` (NOT InvoiceGroup)
- **Table**: `invoice_numbering` (NOT invoice_groups)
- **Foreign Key**: `numbering_id` (NOT group_id)
- **Relationship**: `$invoice->numbering()` (NOT $invoice->group())

This naming better reflects the purpose: managing invoice number generation schemes.

**Example:**
```php
// Model
class InvoiceNumbering extends Model {
    public function generateNextNumber(): string { /* ... */ }
}

// Usage
$numbering = InvoiceNumbering::find($numberingId);
$invoiceNumber = $numbering->generateNextNumber();

// Relationship
$invoice->numbering; // Access the numbering scheme
```

## Authentication & Authorization

### Using Spatie/Laravel-Permission + Policies
- Define permissions in database
- Use policies for model-level authorization
- Implement role-based access control (RBAC)

**Example Policy:**
```php
class ClientPeppolPolicy
{
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create-client-peppol');
    }
    
    public function update(User $user, ClientPeppol $clientPeppol): bool
    {
        return $user->hasPermissionTo('update-client-peppol');
    }
}
```

## Code Quality

### Always:
- Write comprehensive PHPUnit tests
- Use type hints (PHP 8.3 features)
- Follow PSR-12 coding standards
- Document complex business logic
- Use Laravel's built-in helpers and facades
- Use semantic naming that reflects purpose (e.g., "numbering" not "group")

### Avoid:
- Hardcoded URLs (use route() helper)
- Business logic in controllers
- Direct database queries in controllers
- Magic numbers and strings
- Ambiguous naming (prefer explicit names like "numbering" over generic ones like "group")

## Migration Strategy

### Incremental Approach:
1. **Phase 1**: Authentication & User Management
2. **Phase 2**: Core Invoice System
3. **Phase 3**: Client/Product Management
4. **Phase 4**: Quote & Sales Order systems
5. **Phase 5**: Payment gateways
6. **Phase 6**: PDF/UBL XML generation
7. **Phase 7**: Email templates & notifications
8. **Phase 8**: Widgets & UI components

### For Each Module:
1. Create migrations
2. Create models with relationships
3. Create factories and seeders
4. Create DTOs
5. Create repositories
6. Create services
7. Create controllers
8. Create views (plain PHP initially)
9. Write comprehensive tests
10. Update routes with auth middleware

## Documentation

### Update After Each Phase:
- .junie/guidelines.md (this file)
- .github/copilot-instructions.md
- README-LARAVEL.md
- MIGRATION-SUMMARY.md

## Dependencies

### Core Laravel 12 Packages:
- laravel/framework: ^12.0
- spatie/laravel-permission: for RBAC
- mpdf/mpdf: for PDF generation
- sabre/xml: for UBL XML

### Development:
- phpunit/phpunit: ^11.0
- fakerphp/faker: for factories
- mockery/mockery: for unit tests

## Git Workflow

### Commit Messages:
- Be descriptive and concise
- Reference phase/module being worked on
- Example: "Phase 1: Add User authentication with Spatie permissions"

### Branch Strategy:
- Main branch: `copilot/refactor-yii-to-laravel`
- One commit per logical change
- Test before committing

## Performance Considerations

- Use eager loading to prevent N+1 queries
- Cache frequently accessed data
- Use database indexes appropriately
- Optimize file uploads and storage

## Security Best Practices

- Always validate user input
- Use Laravel's built-in CSRF protection
- Sanitize output (htmlspecialchars in views)
- Use parameterized queries (Eloquent does this)
- Implement proper authentication and authorization
- Never store sensitive data in plain text
- Use environment variables for secrets

## Peppol-Specific Guidelines

### Validation:
- Endpoint IDs must be valid email addresses
- Scheme IDs have specific length requirements
- UN/CEFACT codes must be 3 characters
- All Peppol fields follow EN 16931 standard

### Testing:
- Test against Peppol specification
- Validate UBL XML structure
- Test integration with Peppol providers (StoreCove, Ecosio)

## Multi-Language Support

### Implementation:
- Use Laravel's localization features
- Store translations in `resources/lang/`
- Use trans() or __() helpers in views
- Support at minimum: English, Dutch, French, German

## Continuous Improvement

- Refactor as patterns emerge
- Optimize queries based on profiling
- Update tests as requirements change
- Document lessons learned
- Use clear, semantic naming for better maintainability
