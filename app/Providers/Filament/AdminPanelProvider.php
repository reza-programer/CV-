<?php

namespace App\Providers\Filament;

use App\Http\Middleware\TrackLastSeen;
use Filament\Actions\Action;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/**
 * Admin console laid out like an operations (WMS) back office: all white,
 * dense ruled tables and monochrome greys. The look lives in the panel theme,
 * resources/css/filament/admin/theme.css.
 */
class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('CV by kezottt_ Admin')
            ->brandLogo(fn () => view('filament.admin.brand'))
            ->brandLogoHeight('2rem')
            ->favicon(asset('favicon.svg'))
            ->colors([
                'primary' => Color::Zinc,
                'gray' => Color::Zinc,
            ])
            // Filament's own Inter is served locally; no external font CDN.
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->darkMode(false)
            ->maxContentWidth(Width::Full)
            ->sidebarWidth('15rem')
            ->sidebarCollapsibleOnDesktop()
            // Console-style tables: striped, more rows per page.
            ->bootUsing(fn () => Table::configureUsing(fn (Table $table) => $table
                ->striped()
                ->paginationPageOptions([25, 50, 100])
                ->defaultPaginationPageOption(25)))
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->navigationGroups([
                NavigationGroup::make('Pemantauan'),
                NavigationGroup::make('Kelola'),
            ])
            ->userMenuItems([
                Action::make('site')
                    ->label('Buka situs')
                    ->url('/')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                TrackLastSeen::class,
            ]);
    }
}
