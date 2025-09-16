<?php

namespace App\Filament\Traits;

use Filament\Actions;
use Illuminate\Database\Eloquent\Model;

trait HasPreviewNext
{
    public static function getPreviewNextActions($url = 'view'): array
    {
        return [
            Actions\ViewAction::make()->label('Previous')
                ->url(function (Model $record) use ($url) {
                    $previous = self::$resource::getModel()::where('id', '<', $record->id)
                        ->orderBy('id', 'desc')->limit(1)->first();
                    return $previous ? self::$resource::getUrl($url, [$previous]) : null;
                }),

            Actions\ViewAction::make()->label('Next')
                ->url(function (Model $record) use ($url) {
                    $next = self::$resource::getModel()::where('id', '>', $record->id)
                        ->orderBy('id')->limit(1)->first();
                    return $next ? self::$resource::getUrl($url, [$next]) : null;
                }),
        ];
    }
}
