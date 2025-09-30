<?php

namespace App\Http\Middleware;

use App\Models\Post;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TrackVisits
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Записываем только GET запросы и только если это не AJAX
        if ($request->isMethod('GET')
            && ! $request->ajax()
            && $response->getStatusCode() === 200
            && ! $this->shouldExclude($request)) {
            $this->recordVisit($request);
        }

        return $response;
    }

    private function shouldExclude(Request $request): bool
    {
        $excludedPaths = [
            'telescope',
            'admin',
            'log-viewer',
        ];

        $path = $request->path();

        foreach ($excludedPaths as $excluded) {
            if (str_starts_with($path, $excluded)) {
                return true;
            }
        }

        return false;
    }

    private function recordVisit(Request $request): void
    {
        $slug = $request->route()->parameter('slug');
        $slug && $post = Post::where('slug', $slug)->first();

        try {
            \App\Models\Visit::create([
                'created_at' => now(),
                'url' => $request->fullUrl(),
                'page_title' => $this->extractPageTitle($request),
                'referrer' => $request->header('Referer'),
                'user_agent' => $request->header('User-Agent', ''),
                'ip_address' => $request->ip(),
                'session_id' => $request->session()->getId(),
                'post_id' => $post->id ?? null,
            ]);
        } catch (\Exception $e) {
            // Логируем ошибку, но не прерываем выполнение
            logger()->error('Failed to record visit', [
                'error' => $e->getMessage(),
                'url' => $request->fullUrl(),
            ]);
        }
    }

    private function extractPageTitle(Request $request): ?string
    {
        // Попытаемся определить заголовок страницы из роута
        $routeName = $request->route()?->getName();

        switch ($routeName) {
            case 'blog.post':
                return 'Blog Post';
            case 'blog.category':
                return 'Category';
            case 'blog.tag':
                return 'Tag';
            case 'blog.author':
                return 'Author';
            default:
                return $request->path() === '/' ? 'Home' : ucfirst($request->path());
        }
    }
}
