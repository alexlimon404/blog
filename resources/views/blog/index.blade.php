@extends('layouts.app')

@section('title', 'Blog Posts')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <h1>Latest Posts</h1>

            @forelse($posts as $post)
                <article class="card mb-4">
                    <div class="card-body">
                        <h2 class="card-title">
                            <a href="{{ route('blog.post', $post->slug) }}" class="text-decoration-none">
                                {{ $post->title }}
                            </a>
                        </h2>

                        <div class="text-muted mb-2">
                            By <a href="{{ route('blog.author', $post->author->id) }}">{{ $post->author->name }}</a>
                            in
                            <a href="{{ route('blog.category', $post->category->slug) }}">{{ $post->category->name }}</a>
                            • {{ $post->published_at->format('M d, Y') }}
                        </div>

                        <p class="card-text">{{ $post->excerpt }}</p>

                        <div class="mb-2">
                            @foreach($post->tags as $tag)
                                <a href="{{ route('blog.tag', $tag->slug) }}"
                                   class="badge bg-secondary text-decoration-none me-1"
                                   style="background-color: {{ $tag->color }} !important;">
                                    {{ $tag->name }}
                                </a>
                            @endforeach
                        </div>

                        <a href="{{ route('blog.post', $post->slug) }}" class="btn btn-primary">Read More</a>
                    </div>
                </article>
            @empty
                <p>No posts available.</p>
            @endforelse

            {{ $posts->links() }}
        </div>

        <div class="col-md-4">
            @include('blog.sidebar')
    </div>
</div>
@endsection