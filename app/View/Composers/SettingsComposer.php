<?php

namespace App\View\Composers;

use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class SettingsComposer
{
    protected static ?Collection $settings = null;

    public function compose(View $view): void
    {
        if (is_null(self::$settings)) {
            self::$settings = Setting::getCached()
                ->keyBy('key')
                ->map(fn ($setting) => $setting->value);
        }

        $view->with('settings', self::$settings);
    }
}
