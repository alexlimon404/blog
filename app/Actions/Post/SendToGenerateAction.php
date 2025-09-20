<?php

namespace App\Actions\Post;

use App\Actions\Action;
use App\Models\Post;
use App\Services\AiGenerator\AiGenerator;

class SendToGenerateAction extends Action
{
    public function __construct(private Post $post)
    {
    }

    public function handle(): void
    {
        $data = AiGenerator::driver($this->post->driver)->createText([
            'uuid' => $this->post->uuid,
            'model_name' => $this->post->model,
            'text_request' => "{$this->post->basePrompt?->prompt} {$this->post->title}",
        ]);

        $data['status'] === Post::STATUS_GENERATE && $this->post->updateStatus($data['status']);
    }
}
