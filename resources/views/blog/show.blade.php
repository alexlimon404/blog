@extends('layouts.app')

@section('title', $post->title)

@section('content')
    <div class="row">
        <div class="col-md-8">
            <article>
                <h1>{{ $post->title }}</h1>

                <div class="text-muted mb-4">
                    By <a href="{{ route('blog.author', $post->author->id) }}">{{ $post->author->name }}</a>
                    in <a href="{{ route('blog.category', $post->category->slug) }}">{{ $post->category->name }}</a>
                    • {{ $post->published_at->format('M d, Y') }}
                </div>

                <div class="mb-3">
                    @foreach($post->tags as $tag)
                        <a href="{{ route('blog.tag', $tag->slug) }}"
                           class="badge bg-secondary text-decoration-none me-1"
                           style="background-color: {{ $tag->color }} !important;">
                            {{ $tag->name }}
                        </a>
                    @endforeach
                </div>

                <div class="content">
                    {!! nl2br(e($post->content)) !!}
                </div>
            </article>

            <div class="mt-4">
                <a href="{{ url('/') }}" class="btn btn-secondary">← Back to Posts</a>
            </div>
        </div>

        <div class="col-md-4">
            @include('blog.sidebar')
        </div>
    </div>
@endsection