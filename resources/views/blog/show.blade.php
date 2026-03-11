@extends('layouts.app')

@section('title', $post->title . ' | ' . setting('default_title', 'Blog'))
@section('description', $post->description ?: $post->excerpt ?: Str::limit(strip_tags($post->content), 160))
@section('og_type', 'article')
@section('canonical', route('blog.post', $post->slug))
@section('published_at', $post->published_at?->toIso8601String() ?? '')
@section('updated_at', $post->updated_at?->toIso8601String() ?? '')

@section('content')
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "BlogPosting",
        "headline": @json($post->title),
        "description": @json($post->description ?: $post->excerpt ?: Str::limit(strip_tags($post->content), 160)),
        "url": "{{ route('blog.post', $post->slug) }}",
        "datePublished": "{{ $post->published_at?->toIso8601String() }}",
        "dateModified": "{{ $post->updated_at->toIso8601String() }}",
        @if($post->author)
        "author": {
            "@@type": "Person",
            "name": @json($post->author->name)
        },
        @endif
        @if($post->category)
        "articleSection": @json($post->category->name),
        @endif
        "keywords": @json($post->tags->pluck('name')->implode(', ')),
        "publisher": {
            "@@type": "Organization",
            "name": @json(setting('default_title', 'Blog'))
        }
    }
    </script>

    @include('blog.breadcrumbs', ['breadcrumbs' => array_filter([
        $post->category ? ['title' => $post->category->name, 'url' => route('blog.category', $post->category->slug)] : null,
        ['title' => $post->title],
    ])])

    <div class="row">
        <div class="col-md-8">
            <article>
                <h1>{{ $post->title }}</h1>

                <div class="text-muted mb-4">
                    @if($post->author_id)
                        By <a href="{{ route('blog.author', $post->author->id) }}">{{ $post->author->name }}</a>
                    @endif
                    @if($post->category_id)
                        {{ $post->author ? 'in' : '' }}
                        <a href="{{ route('blog.category', $post->category->slug) }}">{{ $post->category->name }}</a>
                    @endif
                    • {{ $post->published_at ? $post->published_at->format('M d, Y') : 'not_published' }}
                    @if($post->updated_at && $post->published_at && $post->updated_at->gt($post->published_at->addDay()))
                        • Updated {{ $post->updated_at->format('M d, Y') }}
                    @endif
                    • {{ ceil(str_word_count(strip_tags($post->content)) / 200) }} min read
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

                @php
                    $content = $post->content;

                    // Remove H1 from AI content (page already has H1)
                    $content = preg_replace('/<h1[^>]*>.*?<\/h1>/is', '', $content);

                    // Add nofollow/noopener to external links
                    $siteHost = parse_url(config('app.url'), PHP_URL_HOST);
                    $content = preg_replace_callback(
                        '/<a\s([^>]*href=["\']https?:\/\/([^"\']+)["\'][^>]*)>/is',
                        function ($match) use ($siteHost) {
                            $host = parse_url('http://' . $match[2], PHP_URL_HOST);
                            if ($host && $host !== $siteHost) {
                                $tag = $match[1];
                                if (strpos($tag, 'rel=') === false) {
                                    $tag .= ' rel="noopener nofollow"';
                                }
                                if (strpos($tag, 'target=') === false) {
                                    $tag .= ' target="_blank"';
                                }
                                return "<a {$tag}>";
                            }
                            return $match[0];
                        },
                        $content
                    );

                    // Build TOC from h2/h3
                    $tocItems = [];
                    $content = preg_replace_callback(
                        '/<(h[23])([^>]*)>(.*?)<\/\1>/is',
                        function ($match) use (&$tocItems) {
                            $tag = $match[1];
                            $attrs = $match[2];
                            $text = strip_tags($match[3]);
                            $id = Str::slug($text);
                            $tocItems[] = ['tag' => $tag, 'id' => $id, 'text' => $text];
                            return "<{$tag}{$attrs} id=\"{$id}\">{$match[3]}</{$tag}>";
                        },
                        $content
                    );
                @endphp

                @if(count($tocItems) >= 3)
                    <nav class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-2">Table of Contents</h5>
                            <ul class="list-unstyled mb-0">
                                @foreach($tocItems as $item)
                                    <li class="{{ $item['tag'] === 'h3' ? 'ms-3' : '' }}">
                                        <a href="#{{ $item['id'] }}" class="text-decoration-none">{{ $item['text'] }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </nav>
                @endif

                <div class="content">
                    {!! $content !!}
                </div>
            </article>

            @if($relatedPosts->isNotEmpty())
                <div class="mt-5">
                    <h3>Related Posts</h3>
                    <div class="row">
                        @foreach($relatedPosts as $related)
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <a href="{{ route('blog.post', $related->slug) }}" class="text-decoration-none">
                                                {{ $related->title }}
                                            </a>
                                        </h5>
                                        <p class="card-text small text-muted">{{ Str::limit($related->excerpt, 80) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mt-4">
                <a href="{{ url('/') }}" class="btn btn-secondary">← Back to Posts</a>
            </div>
        </div>

        <div class="col-md-4">
            @include('blog.sidebar')
        </div>
    </div>
@endsection