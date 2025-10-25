<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Author;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::published()
            ->with(['author', 'category', 'tags'])
            ->latest('published_at')
            ->paginate(10);

        return view('blog.index', compact('posts'));
    }

    public function show(string $slug)
    {
        $query = Post::query()
            ->with(['author', 'category', 'tags'])
            ->where('slug', $slug);

        // Если пользователь не админ, показывать только опубликованные посты
        if (! auth()->check() || ! auth()->user()->admin) {
            $query->published();
        }

        $post = $query->firstOrFail();

        return view('blog.show', compact('post'));
    }

    public function category(string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $posts = $category->posts()
            ->published()
            ->with(['author', 'category', 'tags'])
            ->latest('published_at')
            ->paginate(10);

        return view('blog.category', compact('category', 'posts'));
    }

    public function tag(string $slug)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();
        $posts = $tag->posts()
            ->published()
            ->with(['author', 'category', 'tags'])
            ->latest('published_at')
            ->paginate(10);

        return view('blog.tag', compact('tag', 'posts'));
    }

    public function author(int $id)
    {
        $author = Author::findOrFail($id);
        $posts = $author->posts()
            ->published()
            ->with(['author', 'category', 'tags'])
            ->latest('published_at')
            ->paginate(10);

        return view('blog.author', compact('author', 'posts'));
    }
}
