# Full Laravel Migration Roadmap

## Overview
This document outlines the complete incremental migration from Yii3 to Laravel 12 for the remaining ~605 PHP files.

## Current Status

### ✅ Completed (Phase 0 - Peppol Foundation)
- [x] Laravel 12 core structure
- [x] Peppol entities (ClientPeppol, PaymentPeppol, UnitPeppol)
- [x] Base models (Client, Invoice, Unit)
- [x] Testing infrastructure with PHPUnit 11
- [x] Code quality improvements
- [x] Project guidelines and standards
- [x] Route protection with auth middleware

### 📝 Established
- [x] Test naming convention (it_* prefix)
- [x] Arrange, Act, Assert pattern
- [x] Architecture patterns (DTOs, Services, Repositories, Controllers)
- [x] Security best practices
- [x] Documentation standards

## Phase 1: Authentication & User Management

### Estimated Effort: 16-20 hours

### Dependencies to Add
```json
{
    "spatie/laravel-permission": "^6.0",
    "pragmarx/google2fa-laravel": "^2.0",
    "bacon/bacon-qr-code": "^3.0"
}
```

### Files to Migrate
**From src/Auth:** (~15 files)
- Identity.php → Integrated into User model
- AuthService.php → app/Services/AuthService.php
- Token.php → app/Models/PasswordResetToken.php
- Controllers (5 files) → app/Http/Controllers/Auth/
- Forms (5 files) → app/Http/Requests/Auth/ + DTOs

**From src/User:** (~8 files)
- User.php → app/Models/User.php (with Spatie traits)
- RecoveryCode.php → app/Models/RecoveryCode.php
- UserService.php → app/Services/UserService.php
- UserRepository.php → app/Repositories/UserRepository.php
- Controllers → app/Http/Controllers/

### Migrations to Create
1. `create_users_table` - with email, password, 2FA fields
2. `create_password_reset_tokens_table`
3. `create_recovery_codes_table` - for 2FA backup
4. `create_permission_tables` - Spatie package migration
5. `create_sessions_table` - Laravel session storage

### Models & Relationships
```php
User (extends Authenticatable)
  - HasMany: RecoveryCodes
  - HasMany: ClientPeppol (via clients)
  - HasMany: Invoices
  - Traits: HasRoles, HasPermissions (Spatie)
  - 2FA: totpSecret, tfa_enabled fields

RecoveryCode
  - BelongsTo: User
  - Fields: code, used_at

PasswordResetToken
  - Key: email (indexed)
  - Fields: token, created_at
```

### Controllers to Create
1. **LoginController** - handle login, 2FA verification
2. **RegisterController** - user registration
3. **ForgotPasswordController** - password reset request
4. **ResetPasswordController** - password reset execution
5. **TwoFactorController** - 2FA setup and management
6. **ProfileController** - user profile management

### Services
```php
AuthService
  - login(string $email, string $password): User|false
  - logout(User $user): void
  - verify2FA(User $user, string $code): bool
  - generateRecoveryCodes(User $user): array

UserService
  - create(UserDTO $dto): User
  - update(User $user, UserDTO $dto): User
  - delete(User $user): void
  - enable2FA(User $user): string // returns QR code
  - disable2FA(User $user): void
```

### DTOs
- UserDTO (id, email, password, login, tfa_enabled)
- LoginDTO (email, password, remember)
- RegisterDTO (email, password, password_confirmation, login)
- TwoFactorDTO (code, recovery_code)

### Views (Plain PHP)
- resources/views/auth/login.php
- resources/views/auth/register.php
- resources/views/auth/forgot-password.php
- resources/views/auth/reset-password.php
- resources/views/auth/verify-2fa.php
- resources/views/auth/setup-2fa.php
- resources/views/profile/edit.php

### Tests (~30 test methods)
```php
// Feature Tests
tests/Feature/Auth/LoginTest.php
  - it_allows_user_to_login_with_valid_credentials
  - it_prevents_login_with_invalid_credentials
  - it_requires_2fa_code_when_enabled
  - it_accepts_recovery_code_for_2fa
  - it_throttles_login_attempts

tests/Feature/Auth/RegistrationTest.php
  - it_registers_new_user_with_valid_data
  - it_validates_email_format
  - it_requires_password_confirmation
  - it_prevents_duplicate_email_registration

tests/Feature/Auth/PasswordResetTest.php
  - it_sends_password_reset_link
  - it_resets_password_with_valid_token
  - it_rejects_expired_tokens

tests/Feature/Auth/TwoFactorTest.php
  - it_enables_2fa_for_user
  - it_generates_recovery_codes
  - it_verifies_2fa_code
  - it_disables_2fa_for_user

// Unit Tests
tests/Unit/Services/AuthServiceTest.php
  - it_authenticates_user_with_correct_password
  - it_rejects_incorrect_password
  - it_validates_2fa_code

tests/Unit/Models/UserTest.php
  - it_hashes_password_on_creation
  - it_validates_password
  - it_generates_auth_key
```

### Permissions & Roles to Seed
```php
// Permissions
'manage-users'
'manage-clients'
'manage-invoices'
'manage-peppol'
'manage-products'
'manage-quotes'
'manage-settings'

// Roles
'super-admin' (all permissions)
'admin' (most permissions)
'accountant' (invoices, clients, peppol)
'user' (limited permissions)
```

### Routes
```php
// routes/web.php
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/forgot-password', [ForgotPasswordController::class, 'show'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'send'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'show'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/2fa/setup', [TwoFactorController::class, 'setup'])->name('2fa.setup');
    Route::post('/2fa/enable', [TwoFactorController::class, 'enable'])->name('2fa.enable');
    Route::post('/2fa/disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');
});
```

---

## Phase 2: Core Invoice System

### Estimated Effort: 40-50 hours

### Scope
- Migrate src/Invoice/* (40+ subdirectories)
- ~250 PHP files to migrate
- Complex relationships and business logic

### Key Modules
1. **Invoice Core**
   - Inv, InvItem, InvAmount models
   - Invoice generation and management
   - Tax calculations

2. **Invoice Items**
   - InvItem, InvItemAmount, InvItemAllowanceCharge
   - Product linking
   - Quantity and pricing

3. **Allowances & Charges**
   - Discounts and surcharges
   - Tax handling

4. **Invoice Recurring**
   - Scheduled invoice generation
   - Template management

5. **Invoice Sent Log**
   - Email tracking
   - Delivery confirmations

### Dependencies
```json
{
    "mpdf/mpdf": "^8.2",
    "sabre/xml": "^4.0",
    "league/commonmark": "^2.4"
}
```

### High-Priority Components
- InvoiceService with PDF generation
- UBL XML generator (Peppol BIS 3.0)
- Tax calculation engine
- Invoice state machine (draft, sent, paid, cancelled)

### Tests Required
- ~100+ test methods across feature and unit tests
- Test invoice calculations
- Test PDF generation
- Test UBL XML validation
- Test state transitions

---

## Phase 3: Client & Product Management

### Estimated Effort: 20-25 hours

### Client Module
**From src/Invoice/Client:**
- Client CRUD operations
- Client relationships with invoices
- Client custom fields
- Client notes

### Product Module
**From src/Invoice/Product:**
- Product catalog
- Product properties
- Product images
- Product-client relationships
- Inventory management

### Migrations
- Enhance clients table
- Create products table
- Create product_images table
- Create product_properties table
- Create client_notes table
- Create custom_fields table
- Create custom_values table

### Tests
- ~50 test methods
- Client CRUD operations
- Product management
- Custom fields functionality

---

## Phase 4: Quote & Sales Order Systems

### Estimated Effort: 25-30 hours

### Scope
**From src/Invoice/Quote & src/Invoice/SalesOrder:**
- Quote generation and management
- Conversion to Sales Orders
- Sales Order to Invoice conversion
- Approval workflows

### Models
- Quote, QuoteItem, QuoteAmount
- SalesOrder, SalesOrderItem, SalesOrderAmount
- QuoteTaxRate, SalesOrderTaxRate
- AllowanceCharges for both

### Complex Logic
- State transitions (quote → sales order → invoice)
- Approval chains
- Price negotiations
- Quantity management

### Tests
- ~60 test methods
- Quote lifecycle
- Sales order processing
- Conversion workflows

---

## Phase 5: Payment Gateways

### Estimated Effort: 30-35 hours

### Gateways to Integrate
1. **Stripe**
   - Payment intents
   - Webhooks
   - Customer management

2. **Braintree**
   - Transaction processing
   - Vault integration

3. **Amazon Pay**
   - Checkout integration
   - IPN handling

### Dependencies
```json
{
    "stripe/stripe-php": "^13.0",
    "braintree/braintree_php": "^6.0",
    "amzn/amazon-pay-api-sdk-php": "^2.7"
}
```

### Components
- PaymentService for each gateway
- Webhook controllers
- Payment recording
- Refund processing

### Tests
- ~40 test methods
- Mock gateway responses
- Test webhook handling
- Test payment states

---

## Phase 6: PDF & UBL XML Generation

### Estimated Effort: 20-25 hours

### PDF Generation
- Invoice templates
- Quote templates
- Sales order templates
- Customizable layouts
- Multi-language support

### UBL XML (Peppol BIS 3.0)
- Invoice UBL generation
- Credit note UBL
- Validation against XSD schemas
- Peppol compliance checks

### Components
- PdfGenerator service
- UblGenerator service
- Template engine
- Validation service

### Tests
- ~35 test methods
- PDF generation verification
- UBL XML validation
- Peppol compliance tests
- Template rendering

---

## Phase 7: Email Templates & Notifications

### Estimated Effort: 15-20 hours

### Scope
- Email templates management
- Invoice delivery
- Quote delivery
- Payment confirmations
- Reminder emails

### Components
- EmailTemplateService
- Notification classes
- Queue integration
- Template variables

### Tests
- ~25 test methods
- Email sending
- Template rendering
- Variable replacement

---

## Phase 8: Widgets & UI Components

### Estimated Effort: 15-20 hours

### Components to Migrate
**From src/Widget:**
- Form fields
- Buttons
- Toolbars
- Data tables
- Charts and dashboards

### Approach
- Convert to Laravel view components or stay as includes
- Maintain plain PHP initially
- Consider Blade components later

### Tests
- ~20 test methods
- Component rendering
- Data formatting

---

## Phase 9: Middleware & Utilities

### Estimated Effort: 10-15 hours

### Scope
**From src/Middleware:**
- Custom middleware
- Request/response handling
- Localization middleware

**From src/Service:**
- Utility services
- Helper classes

### Tests
- ~15 test methods
- Middleware execution
- Service utilities

---

## Total Estimated Effort

| Phase | Effort (hours) | Priority |
|-------|---------------|----------|
| Phase 1: Auth & User | 16-20 | Critical |
| Phase 2: Core Invoice | 40-50 | Critical |
| Phase 3: Client & Product | 20-25 | High |
| Phase 4: Quote & Sales Order | 25-30 | High |
| Phase 5: Payment Gateways | 30-35 | Medium |
| Phase 6: PDF & UBL XML | 20-25 | High |
| Phase 7: Email & Notifications | 15-20 | Medium |
| Phase 8: Widgets & UI | 15-20 | Low |
| Phase 9: Middleware & Utils | 10-15 | Medium |
| **Total** | **191-240 hours** | |

## Implementation Strategy

### Per Phase:
1. **Analyze** Yii3 code structure
2. **Plan** Laravel equivalents
3. **Create** migrations first
4. **Develop** models with relationships
5. **Build** factories and seeders
6. **Implement** DTOs
7. **Code** repositories
8. **Write** services with business logic
9. **Create** controllers
10. **Design** views (plain PHP)
11. **Write** comprehensive tests (it_* naming)
12. **Document** in guidelines
13. **Test** thoroughly
14. **Commit** with descriptive message

### Quality Gates
- ✅ All tests passing (100% for new code)
- ✅ No hardcoded URLs
- ✅ All routes protected with auth
- ✅ DTOs used for data transfer
- ✅ Services contain business logic
- ✅ Repositories handle data access
- ✅ Controllers stay thin
- ✅ Documentation updated

## Risk Mitigation

### Risks
1. **Complex Business Logic** - Invoice calculations, tax rules
   - Mitigation: Extensive testing, gradual migration

2. **Data Integrity** - Relationships and constraints
   - Mitigation: Careful migration planning, foreign keys

3. **Performance** - N+1 queries, slow operations
   - Mitigation: Eager loading, query optimization, caching

4. **Security** - Authentication, authorization
   - Mitigation: Spatie permissions, policies, middleware

### Testing Requirements
- Minimum 80% code coverage
- All critical paths tested
- Integration tests for workflows
- Performance tests for heavy operations

## Documentation Updates

After each phase:
- [ ] Update .junie/guidelines.md
- [ ] Update .github/copilot-instructions.md
- [ ] Update README-LARAVEL.md
- [ ] Update MIGRATION-SUMMARY.md
- [ ] Create phase-specific docs

## Next Steps

1. **Immediate**: Install Spatie Laravel Permission
   ```bash
   composer require spatie/laravel-permission
   php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
   ```

2. **Phase 1 Start**: Begin Auth & User Management migration following this roadmap

3. **Continuous**: Update guidelines and documentation as patterns emerge

## Conclusion

This migration is substantial but achievable with the incremental approach. The foundation laid in Phase 0 (Peppol) establishes the patterns and standards that will be followed throughout.

Each phase builds on the previous, with Auth & User being the critical foundation for all subsequent work.
