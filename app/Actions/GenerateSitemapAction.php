<?php

namespace App\Actions;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;

class GenerateSitemapAction extends Action
{
    public function __construct()
    {
    }

    public function handle(): string
    {
        $xml = $this->generateSitemapXml();

        $publicPath = public_path('sitemap.xml');
        file_put_contents($publicPath, $xml);

        return $publicPath;
    }

    private function generateSitemapXml(): string
    {
        $baseUrl = config('app.url');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Homepage
        $xml .= $this->addUrl($baseUrl, now(), 'daily', '1.0');

        // Published posts
        $posts = Post::published()->get();
        foreach ($posts as $post) {
            $url = route('blog.post', $post->slug);
            $xml .= $this->addUrl($url, $post->updated_at, 'weekly', '0.8');
        }

        // Categories
        $categories = Category::whereHas('posts', function ($query) {
            $query->published();
        })->get();

        foreach ($categories as $category) {
            $url = route('blog.category', $category->slug);
            $xml .= $this->addUrl($url, $category->updated_at, 'weekly', '0.6');
        }

        // Tags
        $tags = Tag::whereHas('posts', function ($query) {
            $query->published();
        })->get();

        foreach ($tags as $tag) {
            $url = route('blog.tag', $tag->slug);
            $xml .= $this->addUrl($url, $tag->updated_at, 'weekly', '0.5');
        }

        $xml .= '</urlset>';

        return $xml;
    }

    private function addUrl(string $url, $lastmod = null, string $changefreq = 'weekly', string $priority = '0.5'): string
    {
        $xml = "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($url) . "</loc>\n";

        if ($lastmod) {
            $xml .= "    <lastmod>" . $lastmod->format('c') . "</lastmod>\n";
        }

        $xml .= "    <changefreq>{$changefreq}</changefreq>\n";
        $xml .= "    <priority>{$priority}</priority>\n";
        $xml .= "  </url>\n";

        return $xml;
    }
}
