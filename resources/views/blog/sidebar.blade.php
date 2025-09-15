<div class="card mb-4">
    <div class="card-header">
        <h5>Categories</h5>
    </div>
    <div class="card-body">
        @php
            $categories = \App\Models\Category::withCount('posts')->get();
        @endphp

        @foreach($categories as $category)
            <a href="{{ route('blog.category', $category->slug) }}"
               class="d-block text-decoration-none mb-1">
                {{ $category->name }} ({{ $category->posts_count }})
            </a>
        @endforeach
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5>Popular Tags</h5>
    </div>
    <div class="card-body">
        @php
            $tags = \App\Models\Tag::withCount('posts')->orderBy('posts_count', 'desc')->take(10)->get();
        @endphp

        @foreach($tags as $tag)
            <a href="{{ route('blog.tag', $tag->slug) }}"
               class="badge bg-secondary text-decoration-none me-1 mb-1"
               style="background-color: {{ $tag->color }} !important;">
                {{ $tag->name }} ({{ $tag->posts_count }})
            </a>
        @endforeach
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5>Recent Posts</h5>
    </div>
    <div class="card-body">
        @php
            $recentPosts = \App\Models\Post::published()->latest('published_at')->take(5)->get();
        @endphp

        @foreach($recentPosts as $recentPost)
            <a href="{{ route('blog.post', $recentPost->slug) }}"
               class="d-block text-decoration-none mb-2">
                <small class="text-muted">{{ $recentPost->published_at->format('M d') }}</small><br>
                {{ Str::limit($recentPost->title, 50) }}
            </a>
        @endforeach
    </div>
</div>