<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\InvoiceStatsWidget;
use App\Filament\Widgets\PaymentStatusWidget;
use App\Filament\Widgets\RecentInvoicesWidget;
use App\Filament\Widgets\TopClientsWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Invoice Manager')
            ->favicon(asset('favicon.ico'))
            ->colors([
                'primary' => Color::Sky,      // Nord blue
                'secondary' => Color::Slate,  // Nord gray
                'success' => Color::Emerald,  // Nord green
                'warning' => Color::Amber,    // Nord yellow
                'danger' => Color::Rose,      // Nord red
                'info' => Color::Blue,        // Nord bright blue
            ])
            ->darkMode(true)
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                NavigationGroup::make('Sales')
                    ->icon('heroicon-o-shopping-cart')
                    ->collapsible(),
                NavigationGroup::make('Clients')
                    ->icon('heroicon-o-users')
                    ->collapsible(),
                NavigationGroup::make('Catalog')
                    ->icon('heroicon-o-cube')
                    ->collapsible(),
                NavigationGroup::make('Peppol')
                    ->icon('heroicon-o-globe-alt')
                    ->collapsible(),
                NavigationGroup::make('Configuration')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsible(),
                NavigationGroup::make('System')
                    ->icon('heroicon-o-server')
                    ->collapsible(),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                InvoiceStatsWidget::class,
                PaymentStatusWidget::class,
                RecentInvoicesWidget::class,
                TopClientsWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s');
    }
}
