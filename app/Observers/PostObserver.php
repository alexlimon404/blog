<?php

namespace App\Observers;

use App\Actions\GenerateSitemapAction;
use App\Models\Post;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PostObserver
{
    public function saved(Post $post): void
    {
        if ($post->wasChanged('published_at') || ($post->isPublished() && $post->wasChanged(['title', 'slug', 'content']))) {
            dispatch(function () use ($post) {
                app(GenerateSitemapAction::class)->handle();
                $this->pingIndexNow($post);
            })->afterResponse();
        }
    }

    public function deleted(Post $post): void
    {
        dispatch(function () {
            app(GenerateSitemapAction::class)->handle();
        })->afterResponse();
    }

    private function pingIndexNow(Post $post): void
    {
        $key = config('services.indexnow.key');
        if (empty($key)) {
            return;
        }

        try {
            Http::timeout(5)->post('https://api.indexnow.org/indexnow', [
                'host' => parse_url(config('app.url'), PHP_URL_HOST),
                'key' => $key,
                'urlList' => [
                    route('blog.post', $post->slug),
                ],
            ]);
            Log::info("IndexNow ping sent for post: {$post->slug}");
        } catch (\Throwable $e) {
            Log::warning("IndexNow ping failed: {$e->getMessage()}");
        }
    }
}
