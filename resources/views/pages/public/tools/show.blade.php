<x-layouts.shells.public>
    <div class="mx-auto flex max-w-6xl flex-col gap-16 px-6 py-8 lg:px-8" x-data="{ authModalOpen: false }">
        {{-- Breadcrumb --}}
        <p class="truncate text-sm text-muted-foreground">
            <a href="{{ route('public.discover') }}" class="hover:text-foreground">{{ __('public/tools/show.breadcrumb_discover') }}</a>
            <span class="px-1">/</span>
            <span class="text-foreground">{{ $tool->name }}</span>
        </p>

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

                <x-ui.button variant="primary" size="default" icon="layer" :label="__('public/tools/show.cta_add_to_stack')" @click="authModalOpen = true"/>
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

        {{-- Auth prompt modal --}}
        <div
            x-show="authModalOpen"
            x-cloak
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        >
            <div
                x-show="authModalOpen"
                @click.outside="authModalOpen = false"
                @keydown.escape.window="authModalOpen = false"
                class="flex w-full max-w-sm flex-col gap-5 rounded-lg border border-border bg-background p-6 shadow-lg"
            >
                <div class="flex flex-col gap-1.5">
                    <h2 class="text-lg font-semibold text-foreground">{{ __('public/tools/show.auth_modal_title') }}</h2>
                    <p class="text-sm text-muted-foreground">{{ __('public/tools/show.auth_modal_description') }}</p>
                </div>

                <div class="flex items-center gap-2">
                    <x-ui.button variant="secondary" class="flex-1" :href="route('login')" :label="__('public/tools/show.auth_modal_cta_login')"/>
                    <x-ui.button variant="primary" class="flex-1" :href="route('register')" :label="__('public/tools/show.auth_modal_cta_signup')"/>
                </div>
            </div>
        </div>
    </div>
</x-layouts.shells.public>
