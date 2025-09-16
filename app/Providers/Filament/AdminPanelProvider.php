<?php

namespace App\Providers\Filament;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Tables\Table;
use Filament\Widgets\AccountWidget;
use Illuminate\Support\HtmlString;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function boot(): void
    {
        Table::configureUsing(static function (Table $table): void {
            $table->defaultPaginationPageOption(10);
            $table->defaultSort('id', 'desc');
            $table->deferColumnManager(false);
        });

        BulkAction::configureUsing(static function (BulkAction $action): void {
            $action->badge();
        });

        EditAction::configureUsing(static function (EditAction $action): void {
            $action->label('E');
        });
        ViewAction::configureUsing(static function (ViewAction $action): void {
            $action->label('V')->color('success');
        });
        DeleteAction::configureUsing(static function (DeleteAction $action): void {
            $action->label('D');
        });
        Action::configureUsing(static function (Action $action): void {
            $livewire = app('livewire')->current();
            if ($livewire instanceof \Filament\Resources\Pages\ViewRecord) {
                $action->badge();
            }
        });
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->brandName(new HtmlString('<a href="' . config('app.url') . '" target="_blank">Blog</a>'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->navigationGroups([
                NavigationGroup::make('User Management')
                    ->collapsed(false),
                NavigationGroup::make('Blog Management')
                    ->collapsed(false),
                NavigationGroup::make('System')
                    ->collapsed(false)
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
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
            ])->navigationItems([
                NavigationItem::make('Telescope')
                    ->url('/telescope', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-magnifying-glass')
                    ->group('System')
                    ->sort(7),
                NavigationItem::make('LogViewer')
                    ->url('/log-viewer', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-document-text')
                    ->group('System')
                    ->sort(9),
            ]);
    }
}
