<?php

namespace App\Console\Commands;

use App\Actions\GenerateSitemapAction;
use Illuminate\Console\Command;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    public function handle(): void
    {
        $this->info('Generating sitemap...');

        $publicPath = GenerateSitemapAction::run();

        $this->info('Sitemap generated successfully at: ' . $publicPath);
    }
}
