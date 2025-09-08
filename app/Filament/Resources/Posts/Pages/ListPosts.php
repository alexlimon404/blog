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
}
