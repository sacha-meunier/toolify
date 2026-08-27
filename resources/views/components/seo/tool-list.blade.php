@props(['tools'])

@php
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'ItemList',
        'itemListElement' => $tools->values()->map(fn ($tool, $index) => [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'url' => route('public.tools.show', $tool),
            'name' => $tool->name,
        ])->all(),
    ];
@endphp

<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
