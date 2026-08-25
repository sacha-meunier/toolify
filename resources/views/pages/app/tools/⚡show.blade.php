<?php

use App\Models\Tool;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

new class extends Component
{
    public Tool $tool;

    public function mount(Tool $tool): void
    {
        Gate::authorize('view', [$tool, request()->query('share')]);

        $this->tool = $tool;
    }
};
?>

<div class="flex flex-col">
    <x-domain.app.topbar>
        <p class="truncate text-sm text-muted-foreground">
            <a href="{{ route('search') }}" wire:navigate class="hover:text-foreground">{{ __('app/tools/show.breadcrumb_search') }}</a>
            <span class="px-1">/</span>
            <span>{{ $tool->categories->first()?->label() }}</span>
            <span class="px-1">/</span>
            <span class="text-foreground">{{ $tool->name }}</span>
        </p>

        <x-slot:actions>
            <livewire:domain.app.dropdown.add-to-stack-menu :tool="$tool" :key="'add-to-stack-'.$tool->id"/>
        </x-slot:actions>
    </x-domain.app.topbar>

    <div class="flex flex-col gap-16 px-8 py-8">
        {{-- Header --}}
        <section class="flex flex-col gap-6">
            <div class="flex items-start gap-3">
                <div class="flex size-12 shrink-0 items-center justify-center rounded-lg text-sidebar-primary-foreground">
                    @if ($tool->logo_url)
                        <img src="{{ $tool->logo_url }}" alt="" class="size-full rounded-2xl object-cover">
                    @else
                        <x-ui.icon.command class="size-7"/>
                    @endif
                </div>

                <div class="flex min-w-0 flex-1 flex-col">
                    <p class="text-lg font-semibold text-foreground">{{ $tool->name }}</p>
                    <p class="text-base text-foreground">{{ $tool->tagline }}</p>
                </div>
            </div>

            <div class="flex gap-4">
                <div class="flex flex-1 flex-col gap-6 rounded-xl border border-border bg-gradient-to-b from-primary/0 to-primary/5 p-6 shadow-xs">
                    <div class="flex flex-col gap-1.5">
                        <p class="text-sm text-muted-foreground">{{ __('app/tools/show.stat_rating') }}</p>
                        <p class="text-3xl font-semibold text-card-foreground">4.7</p>
                    </div>
                </div>

                <div class="flex flex-1 flex-col gap-6 rounded-xl border border-border bg-gradient-to-b from-primary/0 to-primary/5 p-6 shadow-xs">
                    <div class="flex flex-col gap-1.5">
                        <p class="text-sm text-muted-foreground">{{ __('app/tools/show.stat_collections') }}</p>
                        <p class="text-3xl font-semibold text-card-foreground">175</p>
                    </div>
                </div>

                <div class="flex flex-1 flex-col gap-6 rounded-xl border border-border bg-gradient-to-b from-primary/0 to-primary/5 p-6 shadow-xs">
                    <div class="flex flex-col gap-1.5">
                        <p class="text-sm text-muted-foreground">{{ __('app/tools/show.stat_followers') }}</p>
                        <p class="text-3xl font-semibold text-card-foreground">1 290</p>
                    </div>
                </div>

                <div class="flex flex-1 flex-col gap-6 rounded-xl border border-border bg-gradient-to-b from-primary/0 to-primary/5 p-6 shadow-xs">
                    <div class="flex flex-col gap-1.5">
                        <p class="text-sm text-muted-foreground">{{ __('app/tools/show.stat_views') }}</p>
                        <p class="text-3xl font-semibold text-card-foreground">12 290</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- About --}}
        <section class="flex flex-col gap-4">
            <div class="flex flex-col gap-2">
                <p class="text-sm font-medium text-foreground">{{ __('app/tools/show.about_heading') }}</p>
                <p class="text-base whitespace-pre-line text-foreground">{{ $tool->description }}</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @foreach ($tool->categories as $category)
                    <x-ui.badge>{{ $category->label() }}</x-ui.badge>
                @endforeach
                @foreach ($tool->platforms as $platform)
                    <x-ui.badge>{{ $platform->label() }}</x-ui.badge>
                @endforeach
            </div>
        </section>

        {{-- Links & Details --}}
        <section class="flex gap-6">
            <div class="flex flex-1 flex-col gap-2.5">
                <p class="text-sm font-medium text-foreground">{{ __('app/tools/show.links_heading') }}</p>

                <div class="flex flex-col items-start gap-1">
                    @foreach ([
                        ['icon' => 'globe-02', 'url' => $tool->website_url],
                        ['icon' => 'github', 'url' => $tool->github_url],
                        ['icon' => 'twitter', 'url' => $tool->twitter_url],
                        ['icon' => 'apple', 'url' => $tool->app_store_url],
                        ['icon' => 'google-play', 'url' => $tool->play_store_url],
                    ] as $link)
                        @if ($link['url'])
                            <a href="{{ $link['url'] }}" target="_blank" class="flex items-center gap-1 rounded-md px-2 py-2 text-xs font-medium text-primary hover:bg-muted">
                                <x-dynamic-component :component="'ui.icon.'.$link['icon']" size="xs"/>
                                {{ str($link['url'])->after('://')->rtrim('/') }}
                            </a>
                        @else
                            <span class="flex items-center gap-1 rounded-md px-2 py-2 text-xs font-medium text-muted-foreground">
                                <x-dynamic-component :component="'ui.icon.'.$link['icon']" size="xs"/>
                                {{ __('app/tools/show.not_linked_yet') }}
                            </span>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="flex items-center">
                <div class="h-full w-px bg-border"></div>
            </div>

            <div class="flex flex-col gap-2.5">
                <p class="text-sm font-medium text-foreground">{{ __('app/tools/show.details_heading') }}</p>

                <div class="flex w-[350px] flex-col gap-2.5">
                    <div class="flex items-center gap-2.5">
                        <p class="flex-1 text-sm font-medium text-muted-foreground">{{ __('app/tools/show.detail_company') }}</p>
                        <p class="text-base text-foreground">{{ $tool->team->name }}</p>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <p class="flex-1 text-sm font-medium text-muted-foreground">{{ __('app/tools/show.detail_category') }}</p>
                        <p class="text-base text-foreground">{{ $tool->categories->map->label()->join(', ') }}</p>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <p class="flex-1 text-sm font-medium text-muted-foreground">{{ __('app/tools/show.detail_pricing') }}</p>
                        <p class="text-base text-foreground">{{ $tool->pricing->label() }}</p>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <p class="flex-1 text-sm font-medium text-muted-foreground">{{ __('app/tools/show.detail_added') }}</p>
                        <p class="text-base text-foreground">{{ $tool->created_at?->format('Y') }}</p>
                    </div>

                    @if ($tool->founded_year)
                        <div class="flex items-center gap-2.5">
                            <p class="flex-1 text-sm font-medium text-muted-foreground">{{ __('app/tools/show.detail_founded') }}</p>
                            <p class="text-base text-foreground">{{ $tool->founded_year }}</p>
                        </div>
                    @endif

                    @if ($tool->first_release_year)
                        <div class="flex items-center gap-2.5">
                            <p class="flex-1 text-sm font-medium text-muted-foreground">{{ __('app/tools/show.detail_first_release') }}</p>
                            <p class="text-base text-foreground">{{ $tool->first_release_year }}</p>
                        </div>
                    @endif

                    @if ($tool->headquarters)
                        <div class="flex items-center gap-2.5">
                            <p class="flex-1 text-sm font-medium text-muted-foreground">{{ __('app/tools/show.detail_headquarters') }}</p>
                            <p class="text-base text-foreground">{{ $tool->headquarters }}</p>
                        </div>
                    @endif

                    @if ($tool->headcount)
                        <div class="flex items-center gap-2.5">
                            <p class="flex-1 text-sm font-medium text-muted-foreground">{{ __('app/tools/show.detail_headcount') }}</p>
                            <p class="text-base text-foreground">{{ $tool->headcount->label() }}</p>
                        </div>
                    @endif

                    <div class="flex items-center gap-2.5">
                        <p class="flex-1 text-sm font-medium text-muted-foreground">{{ __('app/tools/show.detail_status') }}</p>
                        <p class="text-base text-foreground">{{ $tool->status->label() }}</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Gallery --}}
        <section class="flex flex-col gap-6">
            <div class="flex items-end justify-between gap-6">
                <div class="flex flex-1 flex-col gap-2">
                    <p class="text-sm font-medium text-foreground">{{ __('app/tools/show.gallery_heading') }}</p>
                    <p class="text-lg font-semibold text-foreground">{{ __('app/tools/show.gallery_title', ['name' => $tool->name]) }}</p>
                </div>

                <p class="w-[400px] text-right text-base text-foreground">{{ __('app/tools/show.gallery_intro') }}</p>
            </div>

            <div class="flex gap-4 overflow-x-auto">
                @foreach ([
                    ['shot' => __('app/tools/show.gallery_shots.overview'), 'image' => $tool->banner_url],
                    ['shot' => __('app/tools/show.gallery_shots.workflow'), 'image' => $tool->gallery?->get(0)],
                    ['shot' => __('app/tools/show.gallery_shots.collaboration'), 'image' => $tool->gallery?->get(1)],
                ] as $item)
                    <div class="w-[320px] shrink-0 overflow-clip rounded-xl border border-foreground/10 bg-card shadow-xs">
                        @if ($item['image'])
                            <img src="{{ $item['image'] }}" alt="" class="aspect-video w-full object-cover">
                        @else
                            <div class="aspect-video w-full bg-gradient-to-br from-muted to-border"></div>
                        @endif

                        <div class="flex flex-col gap-1 p-4">
                            <p class="text-base font-medium text-card-foreground">{{ $item['shot']['label'] }}</p>
                            <p class="text-sm text-muted-foreground">{{ $item['shot']['description'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Pricing --}}
        <section class="flex flex-col gap-6">
            <div class="flex items-end justify-between gap-6">
                <div class="flex flex-1 flex-col gap-2">
                    <p class="text-sm font-medium text-foreground">{{ __('app/tools/show.pricing_heading') }}</p>
                    <p class="text-lg font-semibold text-foreground">{{ trans_choice('app/tools/show.pricing_plans_count', count(__('app/tools/show.pricing_plans')), ['count' => count(__('app/tools/show.pricing_plans'))]) }}</p>
                </div>
            </div>

            <div class="flex gap-6">
                @foreach (__('app/tools/show.pricing_plans') as $plan)
                    <div class="flex flex-1 flex-col overflow-clip rounded-xl border border-foreground/10 bg-card shadow-xs">
                        <div class="flex flex-col gap-1 p-4">
                            <p class="text-base font-medium text-card-foreground">{{ $plan['name'] }}</p>
                            <p class="text-sm text-muted-foreground">{{ $plan['price'] }}</p>
                        </div>

                        <div class="flex flex-col gap-2 border-t border-border p-4 pt-2">
                            @foreach ($plan['features'] as $feature)
                                <div class="flex items-start gap-2.5 py-2.5">
                                    <x-ui.icon.checkmark-circle-02 size="sm" class="shrink-0 text-foreground"/>
                                    <p class="text-sm font-medium text-foreground">{{ $feature }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Reviews --}}
        <section class="flex flex-col gap-6">
            <div class="flex items-end justify-between gap-6">
                <p class="text-lg font-semibold text-foreground">{{ __('app/tools/show.reviews_heading') }}</p>

                <div class="flex items-center gap-2">
                    <x-ui.button variant="secondary" size="xs" icon="arrow-up-right-01" :label="__('app/tools/show.cta_view_all_reviews')"/>
                    <x-ui.button variant="primary" size="xs" icon="pen-01" :label="__('app/tools/show.cta_write_a_review')"/>
                </div>
            </div>

            <div class="flex flex-col gap-6">
                <div class="flex items-stretch overflow-clip rounded-xl border border-foreground/10 bg-card shadow-xs">
                    <div class="flex w-[175px] shrink-0 flex-col items-center justify-center gap-3 p-4">
                        <p class="text-base font-medium text-card-foreground">4.8</p>

                        <div class="flex items-center gap-0.5">
                            @for ($i = 0; $i < 5; $i++)
                                <x-ui.icon.star size="sm" class="text-foreground"/>
                            @endfor
                        </div>
                    </div>

                    <div class="flex flex-1 flex-col justify-center gap-2.5 border-l border-border px-4 pt-4 pb-4">
                        @foreach ([
                            ['stars' => 5, 'percent' => 82, 'reviews' => 1200],
                            ['stars' => 4, 'percent' => 75, 'reviews' => 950],
                            ['stars' => 3, 'percent' => 60, 'reviews' => 300],
                            ['stars' => 2, 'percent' => 45, 'reviews' => 150],
                            ['stars' => 1, 'percent' => 20, 'reviews' => 50],
                        ] as $row)
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-1.5">
                                    <p class="text-sm font-medium text-muted-foreground">{{ $row['stars'] }}</p>
                                    <x-ui.icon.star size="xs" class="text-foreground"/>
                                </div>

                                <div class="h-1 flex-1 overflow-clip rounded-full bg-foreground/10">
                                    <div class="h-full rounded-full bg-primary" style="width: {{ $row['percent'] }}%"></div>
                                </div>

                                <p class="shrink-0 text-sm font-medium text-muted-foreground">{{ __('app/tools/show.review_summary_row', ['percent' => $row['percent'], 'reviews' => number_format($row['reviews'])]) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                @foreach (__('app/tools/show.reviews') as $review)
                    <div class="flex flex-col gap-2 rounded-xl border border-foreground/10 bg-card p-4 shadow-xs">
                        <div class="flex items-center gap-3">
                            <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-muted text-xs font-medium text-muted-foreground">
                                {{ str($review['name'])->substr(0, 2)->upper() }}
                            </div>

                            <div class="flex min-w-0 flex-1 flex-col">
                                <p class="text-base font-medium text-card-foreground">{{ $review['name'] }}</p>
                                <p class="text-sm text-muted-foreground">{{ $review['role'] }}</p>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-0.5">
                                    @for ($i = 0; $i < 5; $i++)
                                        <x-ui.icon.star size="xs" class="{{ $i < $review['stars'] ? 'text-foreground' : 'text-foreground/20' }}"/>
                                    @endfor
                                </div>

                                <p class="shrink-0 text-sm text-muted-foreground">{{ $review['ago'] }}</p>
                            </div>
                        </div>

                        <div class="border-t border-border pt-2">
                            <p class="text-base text-foreground">{{ $review['body'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Activity --}}
        <section class="flex flex-col gap-6">
            <div class="flex items-end justify-between gap-6">
                <div class="flex flex-1 flex-col gap-2">
                    <p class="text-sm font-medium text-foreground">{{ __('app/tools/show.activity_heading') }}</p>
                    <p class="text-lg font-semibold text-foreground">{{ __('app/tools/show.activity_title') }}</p>
                </div>

                <x-ui.button variant="secondary" size="xs" :label="__('app/tools/show.cta_view_all_activity')"/>
            </div>

            <div class="flex flex-col">
                @foreach (__('app/tools/show.releases') as $index => $release)
                    @if ($index > 0)
                        <div class="h-px w-full bg-border"></div>
                    @endif

                    <div class="flex items-center gap-6 py-4">
                        <div class="flex w-[175px] shrink-0 items-center gap-1.5">
                            <div class="flex size-5 shrink-0 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground">
                                <x-ui.icon.command class="size-3"/>
                            </div>
                            <p class="truncate text-sm font-semibold text-foreground">{{ $tool->name }}</p>
                        </div>

                        <div class="h-5 w-px shrink-0 bg-border"></div>

                        <div class="flex min-w-0 flex-1 flex-col">
                            <p class="text-base text-foreground">{{ $release['title'] }}</p>
                            <p class="text-sm font-medium text-muted-foreground">{{ $release['description'] }}</p>
                        </div>

                        <p class="w-20 shrink-0 text-right text-sm font-medium text-muted-foreground">{{ $release['date'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</div>
