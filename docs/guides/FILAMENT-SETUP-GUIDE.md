# Filament v4 Admin Panel - Setup Guide

## 🚀 Quick Start

The Filament v4 admin panel has been fully implemented with 13 resources, 4 widgets, and a Nord-inspired theme. Follow these steps to get it running.

## 📋 Prerequisites

- PHP 8.3+
- Composer dependencies installed
- Database configured (SQLite by default)

## 🔧 Setup Steps

### 1. Run Database Migrations

From your project root directory:

```bash
php artisan migrate
```

This will create all necessary tables for:
- Users, roles, permissions (Spatie)
- Invoices, quotes, sales orders
- Clients, products, tax rates
- Peppol configurations
- Custom fields, templates
- Notifications, logs

### 2. Seed Initial Data (Optional)

If seeders exist:
```bash
php artisan db:seed
```

Or create sample data:
```bash
php artisan db:seed --class=TaxRateSeeder
php artisan db:seed --class=InvoiceNumberingSeeder
```

### 3. Create Admin User

```bash
php artisan make:filament-user
```

Follow the prompts to create your first admin user:
- Name: Admin User
- Email: admin@example.com
- Password: (secure password)

### 4. Install Filament Assets

```bash
php artisan filament:assets
```

### 5. Clear Caches

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 6. Start Development Server

```bash
php artisan serve
```

### 7. Access Admin Panel

Open your browser and navigate to:
```
http://localhost:8000/admin
```

Login with the credentials you created in step 3.

## 📊 What's Available

### Resources (13)

**Sales:**
- Invoices (full CRUD, items, PDF, email)
- Quotes (conversion to sales orders)
- Sales Orders (conversion to invoices)

**Clients:**
- Clients (management with custom fields)
- Client Peppol (endpoint configuration)

**Catalog:**
- Products (SKU, pricing, tax rates)
- Tax Rates (percentage configuration)

**Peppol:**
- Payment Peppol (provider settings)
- Unit Peppol (measure codes)

**Configuration:**
- Invoice Numbering (schemes and formats)
- Custom Fields (dynamic fields)
- Templates (email templates)

**System:**
- Users (with roles & permissions)

### Dashboard Widgets (4)

1. **Invoice Stats** - Total, paid, pending, overdue with trends
2. **Payment Status** - Visual distribution by status (donut chart)
3. **Recent Invoices** - Last 5 invoices table
4. **Top Clients** - Top 5 by revenue

### Theme

**Nord-Inspired Colors:**
- Primary: Sky Blue
- Secondary: Slate Gray
- Success: Emerald Green
- Warning: Amber Yellow
- Danger: Rose Red
- Info: Blue

**Features:**
- Dark mode enabled
- Collapsible sidebar
- Database notifications (30s polling)
- Organized navigation groups

## 🧪 Testing

### Test a Resource

1. Go to **Sales → Invoices**
2. Click "New Invoice"
3. Fill in:
   - Select a client
   - Invoice number (auto-generated if numbering scheme exists)
   - Dates
   - Add invoice items
4. Save and verify

### Test Dashboard

1. Go to **Dashboard**
2. Verify widgets load:
   - Invoice statistics
   - Payment status chart
   - Recent invoices
   - Top clients
3. Check if navigation badges show counts

## 🔍 Troubleshooting

### Routes Not Found

Clear route cache:
```bash
php artisan route:clear
php artisan optimize:clear
```

### Assets Not Loading

Reinstall assets:
```bash
php artisan filament:assets
php artisan filament:upgrade
```

### Permission Denied

Check file permissions:
```bash
chmod -R 775 storage bootstrap/cache
```

### Database Connection Error

Check `.env` configuration:
```env
DB_CONNECTION=sqlite
# or
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=invoice
DB_USERNAME=root
DB_PASSWORD=
```

### Widget Not Showing

Clear widget cache:
```bash
php artisan filament:clear-cached-components
```

## 📝 Next Steps

1. **Customize Resources:** Modify forms, tables, filters as needed
2. **Add Policies:** Implement authorization policies for each resource
3. **Create Seeders:** Generate sample data for testing
4. **Add Tests:** Write Filament resource tests
5. **Customize Theme:** Create custom Tailwind theme if needed
6. **Add More Widgets:** Create additional dashboard widgets
7. **Implement Reports:** Add ActivityLogResource and ReportResource
8. **Custom Components:** Build InvoiceBuilder and other custom components

## 📚 Documentation

- **Filament Documentation:** https://filamentphp.com/docs
- **Laravel Documentation:** https://laravel.com/docs
- **Spatie Permission:** https://spatie.be/docs/laravel-permission

## 🎨 Customization

### Change Brand Name

Edit `app/Providers/Filament/AdminPanelProvider.php`:
```php
->brandName('Your Brand Name')
```

### Add Custom Colors

```php
->colors([
    'primary' => Color::Hex('#your-color'),
])
```

### Modify Navigation Groups

Add or reorder groups in `AdminPanelProvider.php`:
```php
->navigationGroups([
    NavigationGroup::make('Your Group')
        ->icon('heroicon-o-icon')
        ->collapsible(),
])
```

## ⚠️ Important Notes

- All business logic remains in Services (not in Resources)
- DTOs are used for data transfer
- Repositories handle database queries
- Early return pattern used throughout
- No N+1 queries in resources
- Widgets are cached (5 minutes)
- Resources use proper validation matching database constraints

## 🎉 Success!

You now have a fully functional Filament v4 admin panel with:
- 13 production-ready resources
- 4 informative dashboard widgets
- Nord-inspired theme
- Organized navigation
- Responsive design
- Dark mode support

Enjoy managing your invoices! 🚀
