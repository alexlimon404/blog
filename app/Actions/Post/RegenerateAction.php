<?php

namespace App\Actions\Post;

use App\Actions\Action;
use App\Models\Post;
use App\Services\AiGenerator\AiGenerator;

class RegenerateAction extends Action
{
    public function __construct(private Post $post)
    {
    }

    public function handle(): void
    {
        AiGenerator::driver($this->post->driver)->regenerateText($this->post->uuid, [
            'model_name' => $this->post->model,
            'text_request' => "{$this->post->basePrompt?->prompt} {$this->post->title}",
        ]);
    }
}
