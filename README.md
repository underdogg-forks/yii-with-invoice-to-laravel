# Laravel Invoice Management System

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-v4-F59E0B?style=flat&logo=filament)](https://filamentphp.com)
[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=flat&logo=php)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)
[![Build](https://img.shields.io/static/v1?label=Build&message=Passing&color=66ff00)]()
[![Psalm Level](https://img.shields.io/static/v1?label=Psalm%20Level&message=1&color=66ff00)]()
[![PHP-CS-Fixer](https://img.shields.io/badge/php--cs--fixer-enabled-blue?logo=php)](https://github.com/FriendsOfPHP/PHP-CS-Fixer)

A comprehensive invoice management system built with Laravel 12 and Filament v4, featuring full CRUD operations for invoices, quotes, sales orders, clients, and more. Includes Peppol e-invoicing support, multi-currency handling, and advanced reporting capabilities.

> **Migration Note:** This project was migrated from Yii3 to Laravel 12, preserving all business logic while modernizing the tech stack. See [migration documentation](docs/migration/) for details.

## ✨ Features

### Core Functionality
- **📄 Invoice Management** - Complete invoice lifecycle from creation to payment
- **📋 Quote System** - Professional quote generation with conversion to sales orders/invoices
- **📦 Sales Orders** - Advanced order management with conversion workflows
- **👥 Client Management** - Comprehensive client profiles with custom fields
- **📦 Product Catalog** - Product management with SKU, pricing, and tax rates
- **🔢 Smart Numbering** - Flexible numbering schemes for invoices, quotes, clients, projects, and tasks
- **📊 Reporting** - Built-in reports for profit, sales, inventory, tax, clients, and products

### Peppol E-Invoicing
- **🌍 Peppol Integration** - Full Peppol BIS 3.0 compliant e-invoicing
- **🔄 UBL XML Generation** - Generate UBL 2.1 Invoice 3.0.15 XML invoices
- **✅ Validation** - Built-in validation against Peppol specifications
- **📮 StoreCove API** - Direct integration with StoreCove for Peppol delivery
- **🔐 Secure Configuration** - Comprehensive Peppol settings per client

### Advanced Features
- **💰 Multi-Currency Support** - Handle invoices in USD, EUR, GBP, JPY, and more
- **💳 Payment Tracking** - Track payments with multiple payment methods
- **📧 Email Templates** - Professional, customizable email templates for invoices and quotes
- **🎨 Template System** - Flexible template engine for customized invoice layouts
- **📱 Responsive Design** - Fully responsive admin panel built with Filament v4
- **🌙 Dark Mode** - Beautiful dark mode support throughout the application
- **🔒 Role-Based Access** - Comprehensive permission system using Spatie Laravel-Permission
- **📈 Dashboard Widgets** - Real-time statistics and insights

## 🚀 Quick Start

### Requirements

- PHP 8.3 or higher
- Composer 2.x
- Node.js 20.x & NPM
- MySQL 8.0+ or PostgreSQL 13+
- Redis (optional, for caching)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/underdogg-forks/yii-with-invoice-to-laravel.git
   cd yii-with-invoice-to-laravel
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure your database**
   Edit `.env` file with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Run migrations**
   ```bash
   php artisan migrate --seed
   ```

7. **Build assets**
   ```bash
   npm run build
   ```

8. **Create admin user**
   ```bash
   php artisan make:filament-user
   ```

9. **Start the development server**
   ```bash
   php artisan serve
   ```

10. **Access the admin panel**
    Navigate to `http://localhost:8000/admin` and login with your admin credentials.

## 📚 Documentation

Comprehensive documentation is available in the `docs/` folder:

### Setup Guides
- [Filament Setup Guide](docs/guides/FILAMENT-SETUP-GUIDE.md) - Complete setup instructions for Filament
- [Testing Guide](docs/guides/TESTING-GUIDE.md) - Comprehensive testing documentation
- [Quick Start Guide](docs/guides/QUICKSTART.md) - Get started quickly

### Migration Documentation
- [Migration Summary](docs/migration/MIGRATION-SUMMARY.md) - Overview of Yii3 to Laravel migration
- [Migration Verification](docs/migration/MIGRATION-VERIFICATION.md) - Verification steps
- [Project Status](docs/migration/PROJECT-STATUS.md) - Current migration status

### Implementation Details
- [Phase 8 Implementation](docs/implementation/PHASE-8-IMPLEMENTATION-SUMMARY.md) - Filament resources implementation
- [Phase 9 Plans](docs/implementation/PHASE-9-IMPLEMENTATION.md) - Future enhancements

## 🎯 Admin Panel Features

### Resources (14 Total)
1. **Clients** - Complete client management with custom fields and Peppol configuration
2. **Invoices** - Full invoice lifecycle with PDF generation and email sending
3. **Quotes** - Professional quote generation with conversion workflows
4. **Sales Orders** - Order management with conversion to invoices
5. **Products** - Product catalog with SKU, pricing, and tax configuration
6. **Tax Rates** - Tax rate management with automatic calculations
7. **Client Peppol** - Peppol-specific configuration for e-invoicing
8. **Payment Methods** - Payment method configuration
9. **Unit of Measure (Peppol)** - UN/CEFACT unit codes for Peppol
10. **Invoice Numbering** - Smart numbering schemes for multiple entity types
11. **Custom Fields** - Flexible custom field system with 6 field types
12. **Templates** - Email and document template management
13. **Users** - User management with role-based permissions
14. **Reports** - Generate various business reports

### Dashboard Widgets
- **Invoice Statistics** - 4 stat cards with 30-day trends and mini charts
- **Payment Status** - Donut chart showing invoice status distribution
- **Recent Invoices** - Quick access to the last 5 invoices
- **Top Clients** - Revenue ranking of top 5 clients

### Custom Form Components
1. **Currency Input** - Multi-currency formatted input (USD, EUR, GBP, JPY)
2. **Tax Rate Selector** - Enhanced selector with live calculation preview
3. **Client Selector** - Advanced search with quick create functionality
4. **Product Picker** - Smart selection with auto-fill capabilities
5. **Invoice Builder** - Advanced repeater with live total calculations

## 🏗️ Architecture

### Technology Stack
- **Backend Framework:** Laravel 12
- **Admin Panel:** Filament v4
- **Database:** MySQL 8.0+ / PostgreSQL 13+
- **Frontend:** Tailwind CSS v4, Alpine.js
- **Build Tool:** Vite
- **Authentication:** Laravel Sanctum
- **Permissions:** Spatie Laravel-Permission
- **PDF Generation:** mPDF
- **E-Invoicing:** Peppol BIS 3.0, UBL 2.1

### Design Patterns
- **Repository Pattern** - Clean separation of data access logic
- **Service Layer** - Business logic abstraction
- **Data Transfer Objects (DTOs)** - Type-safe data transfer
- **Enums** - Type-safe status and configuration values
- **Policy-Based Authorization** - Fine-grained access control
- **Event-Driven Architecture** - Decoupled business logic with events

### Code Quality
- **SOLID Principles** - Applied throughout the codebase
- **DRY (Don't Repeat Yourself)** - Minimal code duplication
- **Early Return Pattern** - Clean, readable conditional logic
- **Type Hints** - Full PHP 8.3 type coverage
- **PHPDoc** - Comprehensive code documentation

## 🧪 Testing

The application includes comprehensive test coverage:

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

Test suites include:
- **Resource Tests** - CRUD operations, filters, actions (9 test files)
- **Component Tests** - Custom form components (5 test files)
- **Widget Tests** - Dashboard widgets (4 test files)

See [Testing Guide](docs/guides/TESTING-GUIDE.md) for detailed testing documentation.

## 🔒 Security

This application follows Laravel security best practices:

- ✅ **CSRF Protection** - All forms protected with CSRF tokens
- ✅ **SQL Injection Prevention** - Eloquent ORM with parameter binding
- ✅ **XSS Prevention** - Blade automatic escaping and content sanitization
- ✅ **Password Hashing** - Bcrypt hashing with appropriate cost factor
- ✅ **Role-Based Access Control** - Spatie Permission package
- ✅ **Rate Limiting** - API and login attempt rate limiting
- ✅ **Input Validation** - Server-side validation for all inputs
- ✅ **Content Sanitization** - HTML content sanitized before rendering

## 🌐 Internationalization

The application is designed for multi-language support:

- Translation files in `lang/` directory
- Blade directive support: `@lang()`, `__()` helper
- Easy addition of new languages
- RTL support ready

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

Please ensure:
- Code follows PSR-12 coding standards
- All tests pass
- New features include tests
- Documentation is updated

## 📝 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## 🙏 Acknowledgments

- **Laravel** - The amazing PHP framework
- **Filament** - Beautiful admin panel for Laravel
- **Spatie** - Excellent Laravel packages
- **Tailwind CSS** - Utility-first CSS framework
- **Alpine.js** - Lightweight JavaScript framework
- **Peppol** - Pan-European Public Procurement OnLine network

## 📧 Support

For support, please:
- Check the [documentation](docs/)
- Open an issue on GitHub
- Contact the development team

## 🗺️ Roadmap

### Current Version (Phase 8) ✅
- ✅ 14 Filament resources with full CRUD
- ✅ 4 dashboard widgets
- ✅ 5 custom form components
- ✅ Blade component system
- ✅ Peppol e-invoicing support
- ✅ Multi-currency handling

### Phase 9 (Planned)
- [ ] Advanced reporting with charts
- [ ] Recurring invoices automation
- [ ] Client portal with payment integration
- [ ] Mobile app (Flutter/React Native)
- [ ] API v2 with GraphQL
- [ ] Advanced analytics dashboard
- [ ] Webhook system for integrations
- [ ] Multi-company support

## 📊 Stats

- **14 Resources** - Complete CRUD operations
- **4 Dashboard Widgets** - Real-time insights
- **5 Custom Components** - Enhanced UX
- **7 Blade Components** - Reusable UI elements
- **18 Test Suites** - Comprehensive coverage
- **100% Filament v4 Compliant** - Latest standards

---

**Built with ❤️ using Laravel and Filament**

## 📖 Legacy Documentation

The following sections contain documentation from the original Yii3 implementation and are preserved for reference:

**Recent Implementations (Yii3)**

[VitePress Integration.](https://vitepress.dev/guide/getting-started) (Dec 2025)

[Prometheus Integration.](docs/PROMETHEUS_INTEGRATION.md) (Dec 2025)

[Prometheus Menu Integration.](docs/PROMETHEUS_MENU_INTEGRATION.md) (Dec 2025)

[Sonar Cloud Setup.](docs/SONARCLOUD_SETUP.md) (Nov 2025)

[Netbeans ↔️ Vs Code: Sync Guide.](docs/NETBEANS_SYNC_GUIDE.md) (Dec 2025)
 
[Php Product Selection Workflow.](docs/PHP_PRODUCT_SELECTION_WORKFLOW.md) (Dec 2025)

[Security Commands.](docs/SECURITY_COMMANDS.md) (Dec 2025)

[Typescript Build Process.](docs/TYPESCRIPT_BUILD_PROCESS.md) (Dec 2025)

[Typescript ES2023 Modernization.](docs/TYPESCRIPT_ES2023_MODERNIZATION.md) (Dec 2025)

[Typescript ES2024 Modernization.](docs/TYPESCRIPT_ES2024_MODERNIZATION.md) (Dec 2025)

[Typescript Go V7 Compatability Testing Guide.](docs/TYPESCRIPT_GO_V7_COMPATIBILITY_TESTING_GUIDE.md) (Dec 2025)

[Invoice Amount Magnifier using Angular.](docs/INVOICE_AMOUNT_MAGNIFIER.md) (Dec 2025)

[Family Commalist Picker using Angular.](docs/FAMILY_COMMALIST_PICKER.md) (Dec 2025)

[Cycle ORM HasOne and outerKey Issue.](docs/CYCLE_ORM_HASONE_OUTERKEY_ISSUE.md) (Jan 2026)

[Cycle ORM Join Optimization.](docs/CYCLE_ORM_JOIN_OPTIMIZATION.md) (Jan 2026)

[Cycle ORM Foreign Key Constraint Issue.](docs/CYCLE_ORM_FOREIGN_KEY_CONSTRAINT_ISSUE.md) (Jan 2026)

[Netbeans IDE 25 Guide.](docs/NETBEANS_IDE25_GUIDE.md) (Dec 2025)

[Tooltip Styles Configuration.](docs/TOOLTIP_STYLES_CONFIGURATION.md) (Jan 2026)

**Features**

* Cycle ORM Interface using Invoiceplane type database schema. 
* Generate VAT invoices using mPDF. 
* Code Generator - Controller to views. 
* PCI-compliant payment gateway interfaces – Braintree Sandbox, Stripe Sandbox, and Amazon Pay integration tested. 
* Generate OpenPeppol UBL 2.1 Invoice 3.0.15 XML invoices – validated with Ecosio. 
* StoreCove API connector with JSON invoice. 
* Invoice cycle – Quote to Sales Order (with client's purchase order details) to Invoice.     
* Multiple language compliant – steps to generate new language files included. 
* Separate Client Console and Company Console. 
* Install with Composer.
* SonarQubeCloud / SonarCloud Code Analysis
* NetBeans 28 && Vs Code IDE Integration
* Eclipse IDE Integration
* SonarLint4NetBeans Plugin - Tools ... Options ... Miscellaneous ... php ... Rules

**Installing with Composer in Windows**
*````composer update````*

## 🚀 Quick Setup with Interactive Installer

For new installations, use one of these interactive installers:

### Option 1: Standalone Installer (Recommended for first-time setup)
```bash
php install.php
```
This works without any dependencies and guides you through the complete setup.

### Option 2: Full-Featured Installer (After dependencies are installed)
```bash
# Using the convenience script
php install_writable.php

# Or using the yii console directly  
./yii install
```

Both installers will:
- ✅ Perform preflight checks (PHP version, extensions, Composer)
- 📦 Install dependencies with `composer install` (with your confirmation)
- 🗄️ Parse database configuration and create the database if needed
- 📋 Provide a checklist for final manual steps

After running either installer, you'll need to manually:
1. Set `BUILD_DATABASE=true` in your `.env` file
2. Start the application to trigger table creation
3. Reset `BUILD_DATABASE=false` for better performance

## Manual Installation

If you prefer manual setup or encounter issues with the installer:

**Installing npm_modules folder containing bootstrap as mentioned in package.json**
* Step 1: Download node.js at https://nodejs.org/en/download
* Step 2: Ensure C:\ProgramFiles\nodejs is in environment variable path. Search ... edit the system environment variables
* Step 3: Run ````npm i```` in ````c:\wamp64\invoice```` folder. This will install @popperjs, Bootstrap 5, and TypeScript 
          into a new node_modules folder.
* Step 4: Keep your npm up to date by running, for example, ````npm install -g npm@10.8.1```` or just ````npm install -g````.

**Recommended php.ini settings**
* Step 1: Wampserver ... Php {version} ... Php Settings ... xdebug.mode = off
* Step 2:                                               ... Maximum Execution Time = 360

Installing the database in mySql
1. Create a database in mySql called yii3_i.
2. The BUILD_DATABASE=true setting in the config/common/params.php file will ensure a firstrun setup of tables.
3. After the setup of tables, ensure that this setting is changed back to false otherwise you will get performance issues.

The c:\wamp64\yii3-i\config\common\params.php file line approx. 193 will automatically build up the tables under database yii3-i. 

````'mode' => $_ENV['BUILD_DATABASE'] ? PhpFileSchemaProvider::MODE_WRITE_ONLY : PhpFileSchemaProvider::MODE_READ_AND_WRITE,````

** If you adjust any Entity file you will have to always make two adjustments to**
** ensure the database is updated with the new changes and relevant fields: **
* 1. Change the BUILD_DATABASE=false in the .env file at the root to BUILD_DATABASE=true
* 2. Once the changes have been reflected and you have checked them via e.g. phpMyAdmin revert back to the original settings

Signup your first user using **+ Person icon**. This user will automatically be assigned the admin role. If you do not have an internet connection you will receive an email failed message
but you will still be able to login. 

You or your customer, signup the second user as your Client/Customer. They will automatically be assigned the observer role. 
If you do not have an internet connection you will get a failed message but if your admin makes the 'Invoice User Account' status active the user
will be able to log in.

If a user signs up by email, they will automatically be assigned as a client, and automatically be made active. 

**If your user has not signed up by email verification, to enable your signed-up Client to make payments:** 
* Step 1: Make sure you have created a client ie. Client ... View ... New
* Step 2: Create a Settings...Invoice User Account
* Step 3: Use the Assigned Client ... Burger Button ... and assign the New User Account to an existing Client.
* Step 4: Make sure they are active.
* Step 5: Make sure the relevant invoice has the status 'sent' either by manually editing the status of the invoice under Invoice ... View ... Options or by actually sending the invoice to the client by email under Invoice ... View ... Options.

**To install at least a service and a product, and a foreign and a non-foreign client automatically, please follow these steps:**

* Step 1: Settings ... View ... General ... Install Test Data ... Yes  AND   Use Test Date ... Yes
* Step 2: In the settings menu, you will now see 'Test data can now be installed'. Click on it.

**The package by default will not use VAT and will use the traditional Invoiceplane type installation providing both line-item tax and invoice tax** 

**If you require VAT based invoices, ensure VAT is setup by going to  Settings ... Views ... Value Added Tax and use a separate database for this purpose. Only line-item tax will be available.**

**Steps to translate into another language:** 

GeneratorController includes a function google_translate_lang ...          
This function takes the English app_lang.php array auto generated in 

````src/Invoice/Language/English```` 

and translates it into the chosen locale (Settings...View...Google Translate) 

outputting it to ````resources/views/generator/output_overwrite.```` 

* Step 1: Download https://curl.haxx.se/ca/cacert.pem into active c:\wamp64\bin\php\php8.1.12 folder.
* Step 2: Select your project that you created under https://console.cloud.google.com/projectselector2/iam-admin/serviceaccounts?pportedpurview=project
* Step 3: Click on Actions icon and select Manage Keys. 
* Step 4: Add Key.
* Step 5: Choose the JSON File option and download the file to src/Invoice/Google_translate_unique_folder.
* Step 6: You will have to enable the Cloud Translation API and provide your billing details. You will be charged 0 currency.
* Step 7: Move the file from views/generator/output_overwrite to eg. src/Invoice/Language/{your language}

**Xml electronic invoices - Can be output if the following sequence is followed:**

* a: A logged in Client sets up their Peppol details on their side via Client...View...Options...Edit Peppol Details for e-invoicing.

* b: A quote is created and sent by the Administrator to the Client.

* c: A logged in Client creates a sales order from the quote with their purchase order number, purchase order line number, and their contact person in the modal.

* d: A logged in Client, on each of the sales order line items, inputs their line item purchase order reference number, and their purchase order line number. (Mandatory or else exception will be raised).

* e: A logged in Administrator, requests that terms and conditions be accepted.

* f: A logged in Client accepts the terms and conditions.

* g: A logged in Administrator, updates the status of the sales order from assembled, approved, confirmed, to generate.

* h: A logged in Administrator can generate an invoice if the sales order status is on 'generate'

* i: A logged in Administrator can now generate a Peppol XML Invoice using today's exchange rates set up in Settings...View...Peppol Electronic Invoicing...One of From Currency and one of To Currency.

* j: Peppol exceptions will be raised.

