<?php

namespace App\Filament\Widgets;

use App\Actions\GenerateSitemapAction;
use Exception;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

class SitemapWidget extends Widget
{
    protected string $view = 'filament.widgets.sitemap-widget';

    public function generateSitemap()
    {
        try {
            $action = new GenerateSitemapAction();
            $action->handle();

            Notification::make()
                ->title('Sitemap Generated Successfully')
                ->body('The sitemap has been generated and saved to public/sitemap.xml')
                ->success()
                ->send();

        } catch (Exception $e) {
            Notification::make()
                ->title('Sitemap Generation Failed')
                ->body('Error: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}