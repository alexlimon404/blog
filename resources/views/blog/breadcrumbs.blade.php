@php
    $breadcrumbs = $breadcrumbs ?? [];
@endphp

@if(count($breadcrumbs) > 0)
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            @foreach($breadcrumbs as $crumb)
                @if($loop->last)
                    <li class="breadcrumb-item active" aria-current="page">{{ $crumb['title'] }}</li>
                @else
                    <li class="breadcrumb-item"><a href="{{ $crumb['url'] }}">{{ $crumb['title'] }}</a></li>
                @endif
            @endforeach
        </ol>
    </nav>

    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@@type": "ListItem",
                "position": 1,
                "name": "Home",
                "item": "{{ url('/') }}"
            }@foreach($breadcrumbs as $crumb),
            {
                "@@type": "ListItem",
                "position": {{ $loop->iteration + 1 }},
                "name": @json($crumb['title'])@if(!$loop->last),
                "item": "{{ $crumb['url'] }}"@endif

            }@endforeach

        ]
    }
    </script>
@endif
