<?php

namespace App\Filament\Resources\Posts;

use App\Filament\Resources\BasePrompts\BasePromptResource;
use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Filament\Resources\Posts\Pages\ViewPost;
use App\Filament\Resources\Posts\Schemas\PostForm;
use App\Filament\Resources\Posts\Tables\PostsTable;
use App\Models\Post;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Document;

    protected static string|null|\UnitEnum $navigationGroup = 'Blog Management';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return PostForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PostsTable::configure($table)
            ->modifyQueryUsing(fn ($query) => $query
                ->withCount([
                    'visits',
                    'visits as today_visits_count' => fn ($query) => $query->whereDate('created_at', today())
                ])
            );
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema(static::getInfoList($schema));
    }

    public static function getInfoList(Schema $schema): array
    {
        return [
            Section::make('Post Information')->inlineLabel()
                ->columns(1)
                ->schema([
                    TextEntry::make('id'),
                    TextEntry::make('created_at')
                        ->dateTime(),
                    TextEntry::make('updated_at')
                        ->dateTime(),
                    TextEntry::make('published_at')
                        ->color(function (Post $record) {
                            return $record->isPublished() ? 'success' : 'danger';
                        })
                        ->dateTime(),
                    TextEntry::make('uuid')
                        ->copyable(),
                    TextEntry::make('title'),
                    TextEntry::make('slug')
                        ->color('warning')
                        ->url(fn (Post $record): string => route('blog.post', $record->slug))
                        ->openUrlInNewTab(),

                    TextEntry::make('excerpt'),
                    TextEntry::make('description'),

                    TextEntry::make('tags.name')
                        ->label('Tags')
                        ->badge(),
                ]),
            Section::make('System')->inlineLabel()
                ->schema([
                    TextEntry::make('category.name')
                        ->label('Category'),
                    TextEntry::make('author.name')
                        ->label('Author'),
                    TextEntry::make('basePrompt.name')
                        ->color('warning')
                        ->url(fn (Post $record): string => BasePromptResource::getUrl('edit', [$record->base_prompt_id]))
                        ->label('Prompt'),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state): string => Post::getStatuses()->firstWhere('id', $state)['color'] ?? 'gray')
                        ->formatStateUsing(fn (string $state): string => Post::getStatuses()->firstWhere('id', $state)['name'] ?? $state),
                    TextEntry::make('status_at')
                        ->dateTime(),
                    TextEntry::make('driver')
                        ->badge(),
                    TextEntry::make('model')
                        ->badge(),
                    TextEntry::make('today_visits_count')
                        ->label('Today Visits')
                        ->state(fn (Post $record): int => $record->visits()->whereDate('created_at', today())->count())
                        ->badge()
                        ->color('info'),
                    TextEntry::make('visits_count')
                        ->label('Total Visits')
                        ->state(fn (Post $record): int => $record->visits()->count())
                        ->badge()
                        ->color('success'),
                ]),
            Section::make('Content')->columnSpanFull()
                ->schema([
                    TextEntry::make('content')
                        ->markdown()
                        ->columnSpanFull(),
                ]),
        ];
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\VisitsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
            'view' => ViewPost::route('/{record}'),
        ];
    }
}
