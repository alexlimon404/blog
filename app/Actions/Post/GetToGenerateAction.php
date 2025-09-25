<?php

namespace App\Actions\Post;

use App\Actions\Action;
use App\Models\Post;
use App\Services\AiGenerator\AiGenerator;

class GetToGenerateAction extends Action
{
    public function __construct(private Post $post)
    {
    }

    public function handle(): void
    {
        $data = AiGenerator::driver($this->post->driver)->getText([
            'uuid' => $this->post->uuid,
        ]);

        if ($data['uuid'] === $this->post->uuid) {
            $this->post->update([
                'content' => $content = $data['text_response'],
            ]);

            $content && $this->post->updateStatus(Post::STATUS_COMPLETED);
        }
    }
}
