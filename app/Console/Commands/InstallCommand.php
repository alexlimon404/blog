<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'app:install';

    protected $description = 'Install application settings';

    public function handle()
    {
        $this->info('Installing application settings...');

        $this->installSeoSettings();

        $this->info('Application settings installed successfully!');
    }

    protected function installSeoSettings(): void
    {
        $this->info('Installing SEO settings...');

        $settings = [
            [
                'key' => 'default_description',
                'value' => '',
                'type' => Setting::FIELD_STRING,
                'description' => 'Default meta description',
                'active' => true,
            ],
            [
                'key' => 'default_keywords',
                'value' => '',
                'type' => Setting::FIELD_STRING,
                'description' => 'Default meta keywords',
                'active' => true,
            ],
            [
                'key' => 'default_title',
                'value' => 'Blog',
                'type' => Setting::FIELD_STRING,
                'description' => 'Default page title',
                'active' => true,
            ],
        ];

        foreach ($settings as $setting) {
            $model = Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );

            if ($model->wasRecentlyCreated) {
                $this->components->info("  - {$setting['key']}");
            } else {
                $this->components->error("  - {$setting['key']}");
            }
        }

        $this->info('SEO settings installed.');
    }
}
