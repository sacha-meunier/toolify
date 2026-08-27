@props(['tool'])

@php
    $schema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'SoftwareApplication',
                'name' => $tool->name,
                'description' => $tool->tagline,
                'url' => route('public.tools.show', $tool),
                'applicationCategory' => $tool->categories->map->label()->join(', '),
                'operatingSystem' => $tool->platforms->map->label()->join(', '),
                ...($tool->logo_url ? ['image' => $tool->logo_url] : []),
                ...(in_array($tool->pricing, [\App\Enums\Pricing::Free, \App\Enums\Pricing::Freemium], true) ? [
                    'offers' => [
                        '@type' => 'Offer',
                        'price' => '0',
                        'priceCurrency' => 'USD',
                    ],
                ] : []),
            ],
            [
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => __('public/tools/show.breadcrumb_discover'),
                        'item' => route('public.discover'),
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => $tool->name,
                        'item' => route('public.tools.show', $tool),
                    ],
                ],
            ],
        ],
    ];
@endphp

<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
