<?php

namespace App\Filament\Resources\PushSubscriptions;

use App\Filament\Resources\PushSubscriptions\Pages\EditPushSubscription;
use App\Filament\Resources\PushSubscriptions\Pages\ListPushSubscriptions;
use App\Filament\Resources\PushSubscriptions\Schemas\PushSubscriptionForm;
use App\Filament\Resources\PushSubscriptions\Tables\PushSubscriptionsTable;
use App\Models\PushSubscription;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PushSubscriptionResource extends Resource
{
    protected static ?string $model = PushSubscription::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBell;

    protected static string|null|\UnitEnum $navigationGroup = 'System';

    protected static ?string $modelLabel = 'Push Subscription';

    protected static ?string $pluralModelLabel = 'Push Subscriptions';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return PushSubscriptionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PushSubscriptionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPushSubscriptions::route('/'),
            'edit' => EditPushSubscription::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
