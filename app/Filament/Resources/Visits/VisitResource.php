<?php

namespace App\Filament\Resources\Visits;

use App\Filament\Resources\Visits\Pages\ListVisits;
use App\Filament\Resources\Visits\Pages\ViewVisit;
use App\Filament\Resources\Visits\Tables\VisitsTable;
use App\Filament\Resources\Visits\Schemas\VisitForm;
use App\Filament\Resources\Visits\Schemas\VisitInfoList;
use App\Models\Visit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Schemas\Schema;

class VisitResource extends Resource
{
    protected static ?string $model = Visit::class;

    protected static ?string $navigationLabel = 'Посещения';

    protected static string|null|\UnitEnum $navigationGroup = 'Blog Management';

    protected static ?int $navigationSort = 50;
    
    protected static ?string $modelLabel = 'посещение';
    
    protected static ?string $pluralModelLabel = 'посещения';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-eye';

    public static function form(Schema $schema): Schema
    {
        return VisitForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VisitsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VisitInfoList::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVisits::route('/'),
            'view' => ViewVisit::route('/{record}'),
        ];
    }
}
