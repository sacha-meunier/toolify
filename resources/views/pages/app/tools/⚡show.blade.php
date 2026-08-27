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
            <span class="text-foreground">{{ $tool->name }}</span>
        </p>

        <x-slot:actions>
            <livewire:domain.app.dropdown.add-to-stack-menu :tool="$tool" :key="'add-to-stack-'.$tool->id"/>
        </x-slot:actions>
    </x-domain.app.topbar>

    <div class="flex flex-col gap-10 py-6 lg:gap-16 lg:py-8">
        {{-- Header --}}
        <section class="flex flex-col gap-6 px-4 lg:px-8" aria-labelledby="tool-name-heading">
            <div class="flex items-start gap-3">
                <div class="flex size-12 shrink-0 items-center justify-center rounded-lg text-sidebar-primary-foreground">
                    @if ($tool->logo_url)
                        <img src="{{ $tool->logo_url }}" alt="" class="size-full rounded-2xl object-cover">
                    @else
                        <x-ui.icon.command class="size-7"/>
                    @endif
                </div>

                <div class="flex min-w-0 flex-1 flex-col">
                    <h1 id="tool-name-heading" class="truncate text-lg font-semibold text-foreground">{{ $tool->name }}</h1>
                    <p class="truncate text-base text-foreground">{{ $tool->tagline }}</p>
                </div>
            </div>
        </section>

        {{-- About --}}
        <section class="flex flex-col gap-4 px-4 lg:px-8" aria-labelledby="about-heading">
            <div class="flex flex-col gap-2">
                <h2 id="about-heading" class="text-sm font-medium text-foreground">{{ __('app/tools/show.about_heading') }}</h2>
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

        {{-- Gallery --}}
        <section class="flex flex-col gap-6" aria-labelledby="gallery-heading">
            <div class="flex flex-col items-start justify-between gap-3 px-4 lg:flex-row lg:items-end lg:gap-6 lg:px-8">
                <div class="flex flex-1 flex-col gap-2">
                    <p class="text-sm font-medium text-foreground">{{ __('app/tools/show.gallery_heading') }}</p>
                    <h2 id="gallery-heading" class="text-lg font-semibold text-foreground">{{ __('app/tools/show.gallery_title', ['name' => $tool->name]) }}</h2>
                </div>

                <p class="text-base text-foreground lg:w-[400px] lg:text-right">{{ __('app/tools/show.gallery_intro') }}</p>
            </div>

            <div class="flex gap-4 overflow-x-auto px-4 pb-4 lg:px-8">
                @foreach ([$tool->banner_url, $tool->gallery?->get(0), $tool->gallery?->get(1)] as $image)
                    <div class="w-[320px] shrink-0 overflow-clip rounded-xl border border-foreground/10 bg-card shadow-xs">
                        @if ($image)
                            <img src="{{ $image }}" alt="" class="aspect-video w-full object-cover">
                        @else
                            <div class="aspect-video w-full bg-gradient-to-br from-muted to-border"></div>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Links & Details --}}
        <section class="flex flex-col gap-6 px-4 lg:flex-row lg:px-8" aria-labelledby="links-heading details-heading">
            <div class="flex flex-1 flex-col gap-2.5">
                <h2 id="links-heading" class="text-sm font-medium text-foreground">{{ __('app/tools/show.links_heading') }}</h2>

                <div class="flex flex-col items-start gap-1">
                    @foreach ([
                        ['icon' => 'globe-02', 'url' => $tool->website_url],
                        ['icon' => 'github', 'url' => $tool->github_url],
                        ['icon' => 'twitter', 'url' => $tool->twitter_url],
                        ['icon' => 'apple', 'url' => $tool->app_store_url],
                        ['icon' => 'google-play', 'url' => $tool->play_store_url],
                    ] as $link)
                        @if ($link['url'])
                            <x-ui.button
                                variant="link"
                                size="lg"
                                :href="$link['url']"
                                target="_blank"
                                :icon="$link['icon']"
                                :label="str($link['url'])->after('://')->rtrim('/')"
                                class="!px-0"
                            />
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="flex items-center">
                <div class="h-px w-full bg-border lg:h-full lg:w-px"></div>
            </div>

            <div class="flex flex-col gap-2.5">
                <h2 id="details-heading" class="text-sm font-medium text-foreground">{{ __('app/tools/show.details_heading') }}</h2>

                <div class="flex w-full flex-col gap-2.5 lg:w-[350px]">
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
    </div>
</div>
