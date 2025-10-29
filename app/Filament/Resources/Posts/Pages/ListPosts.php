<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use App\Models\BasePrompt;
use App\Models\Post;
use App\Services\AiGenerator\AiGenerator;
use App\Services\AiGenerator\AiGeneratorEnum;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Support\Facades\Cache;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Load Titles')
                ->icon('heroicon-o-sparkles')
                ->color('danger')
                ->schema([
                    Select::make('driver')
                        ->options(AiGeneratorEnum::toSelectArray())
                        ->default(AiGeneratorEnum::TEST->value)
                        ->live(),
                    Select::make('model')
                        ->options(function (callable $get) {
                            $driver = $get('driver');
                            return $driver ? AiGenerator::driver($driver)->getModels() : [];
                        }),
                    Select::make('base_prompt')
                        ->options(BasePrompt::active()->get()->pluck('name', 'id'))
                        ->searchable(),
                    Textarea::make('title_list')
                        ->required(),
                    Textarea::make('description')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $title_list = explode(PHP_EOL, trim($data['title_list']));
                    $count = count($title_list);

                    foreach ($title_list as $title) {
                        try {
                            Post::query()->create([
                                'driver' => $data['driver'],
                                'model' => $data['model'],
                                'title' => $title,
                                'base_prompt_id' => $data['base_prompt'],
                                'description' => $data['description'],
                            ]);
                        } catch (\Exception $e) {
                            info($e->getMessage());
                        }
                    }

                    Notification::make()
                        ->success()
                        ->title('Success')
                        ->body("Load {$count} titles")
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Load titles')
                ->modalSubmitActionLabel('Load'),
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $statusCounts = Cache::remember('posts.status_counts', 180, function () {
            return Post::query()
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status');
        });

        $totalCount = $statusCounts->sum();

        $tabs = [
            'all' => Tab::make('Все')
                ->badge($totalCount),
        ];

        foreach (Post::getStatuses() as $status) {
            $tabs[$status['id']] = Tab::make($status['name'])
                ->query(fn ($query) => $query->where('status', $status['id']))
                ->badge($statusCounts->get($status['id'], 0));
        }

        return $tabs;
    }
}
