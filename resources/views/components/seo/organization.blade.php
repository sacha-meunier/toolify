@php
    $schema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Organization',
                '@id' => url('/').'#organization',
                'name' => config('app.name'),
                'url' => url('/'),
                'logo' => asset('favicon.svg'),
            ],
            [
                '@type' => 'WebSite',
                '@id' => url('/').'#website',
                'name' => config('app.name'),
                'url' => url('/'),
                'publisher' => ['@id' => url('/').'#organization'],
            ],
        ],
    ];
@endphp

<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
