@extends('layouts.app')

@section('title', 'Author: ' . $author->name)

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="d-flex align-items-center mb-4">
                @if($author->avatar)
                    <img src="{{ $author->avatar }}" alt="{{ $author->name }}"
                         class="rounded-circle me-3" width="80" height="80">
                @endif
                <div>
                    <h1>{{ $author->name }}</h1>
                    @if($author->bio)
                        <p class="text-muted">{{ $author->bio }}</p>
                    @endif
                </div>
            </div>

            <h2>Posts by {{ $author->name }}</h2>

            @forelse($posts as $post)
                <article class="card mb-4">
                    <div class="card-body">
                        <h3 class="card-title">
                            <a href="{{ route('blog.post', $post->slug) }}" class="text-decoration-none">
                                {{ $post->title }}
                            </a>
                        </h3>

                        <div class="text-muted mb-2">
                            In
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
                <p>No posts by this author.</p>
            @endforelse

            {{ $posts->links() }}

            <div class="mt-4">
                <a href="{{ url('/') }}" class="btn btn-secondary">← Back to Posts</a>
            </div>
        </div>

        <div class="col-md-4">
            @include('blog.sidebar')
        </div>
    </div>
@endsection