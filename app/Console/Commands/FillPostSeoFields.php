<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class FillPostSeoFields extends Command
{
    protected $signature = 'posts:fill-seo {--force : Overwrite existing values}';

    protected $description = 'Fill empty excerpt and description fields for existing posts';

    public function handle(): void
    {
        $force = $this->option('force');

        $query = Post::whereNotNull('content')->where('content', '!=', '');

        if (! $force) {
            $query->where(function ($q) {
                $q->whereNull('excerpt')->orWhere('excerpt', '')
                    ->orWhereNull('description')->orWhere('description', '');
            });
        }

        $posts = $query->get();

        if ($posts->isEmpty()) {
            $this->info('No posts to update.');
            return;
        }

        $updated = 0;

        foreach ($posts as $post) {
            $cleanText = $this->cleanContent($post->content);
            $changed = false;

            if ($force || empty($post->excerpt)) {
                $post->excerpt = Str::limit($cleanText, 160);
                $changed = true;
            }

            if ($force || empty($post->description)) {
                $post->description = Str::limit($cleanText, 160);
                $changed = true;
            }

            if ($changed) {
                $post->saveQuietly();
                $updated++;
            }
        }

        $this->info("Updated {$updated} posts.");
    }

    private function cleanContent(string $content): string
    {
        $text = strip_tags($content);
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }
}
