<?php

namespace App\Providers;

use App\View\Composers\SettingsComposer;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Opcodes\LogViewer\Facades\LogViewer;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Paginator::defaultView('pagination::bootstrap-5');
        Paginator::defaultSimpleView('pagination::simple-bootstrap-5');

        LogViewer::auth(static function ($request) {
            return $request->user()->admin;
        });

        View::composer('layouts.app', SettingsComposer::class);

        // if (app()->isProduction()) {
        // \Illuminate\Support\Facades\URL::forceScheme('https');
        // }
    }
}
