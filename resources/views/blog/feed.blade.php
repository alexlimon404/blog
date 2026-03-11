{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ setting('default_title', 'Blog') }}</title>
        <link>{{ url('/') }}</link>
        <description>{{ setting('default_description', '') }}</description>
        <language>{{ app()->getLocale() }}</language>
        <lastBuildDate>{{ $posts->first()?->published_at?->toRssString() }}</lastBuildDate>
        <atom:link href="{{ route('blog.feed') }}" rel="self" type="application/rss+xml"/>
        @foreach($posts as $post)
        <item>
            <title>{{ htmlspecialchars($post->title, ENT_XML1) }}</title>
            <link>{{ route('blog.post', $post->slug) }}</link>
            <guid isPermaLink="true">{{ route('blog.post', $post->slug) }}</guid>
            <description>{{ htmlspecialchars($post->excerpt ?: Str::limit(strip_tags($post->content), 300), ENT_XML1) }}</description>
            <pubDate>{{ $post->published_at->toRssString() }}</pubDate>
            @if($post->author)
            <author>{{ $post->author->name }}</author>
            @endif
            @if($post->category)
            <category>{{ htmlspecialchars($post->category->name, ENT_XML1) }}</category>
            @endif
        </item>
        @endforeach
    </channel>
</rss>
