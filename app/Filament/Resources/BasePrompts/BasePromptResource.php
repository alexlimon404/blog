<?php

namespace App\Filament\Resources\BasePrompts;

use App\Filament\Resources\BasePrompts\Pages\CreateBasePrompts;
use App\Filament\Resources\BasePrompts\Pages\EditBasePrompt;
use App\Filament\Resources\BasePrompts\Pages\ListBasePrompts;
use App\Filament\Resources\BasePrompts\Schemas\BasePromptForm;
use App\Filament\Resources\BasePrompts\Tables\BasePromptsTable;
use App\Models\BasePrompt;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BasePromptResource extends Resource
{
    protected static ?string $model = BasePrompt::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::NumberedList;

    protected static string|null|\UnitEnum $navigationGroup = 'Blog Management';

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return BasePromptForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BasePromptsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBasePrompts::route('/'),
            'create' => CreateBasePrompts::route('/create'),
            'edit' => EditBasePrompt::route('/{record}/edit'),
        ];
    }
}
