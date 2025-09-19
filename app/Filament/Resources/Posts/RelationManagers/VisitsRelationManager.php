<?php

namespace App\Filament\Resources\Posts\RelationManagers;

use App\Filament\Resources\Visits\Schemas\VisitInfoList;
use App\Filament\Resources\Visits\Tables\VisitsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class VisitsRelationManager extends RelationManager
{
    protected static string $relationship = 'visits';

    public function table(Table $table): Table
    {
        return VisitsTable::configure($table);
    }

    public function infolist(Schema $schema): Schema
    {
        return VisitInfoList::configure($schema);
    }
}
