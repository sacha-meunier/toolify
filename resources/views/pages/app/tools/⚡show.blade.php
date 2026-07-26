<?php

use App\Models\Tool;
use Livewire\Component;

new class extends Component
{
    public Tool $tool;
};
?>

<div class="flex flex-col">
    <x-domain.app.topbar>
        <p class="truncate text-sm text-muted-foreground">
            <a href="{{ route('search') }}" wire:navigate class="hover:text-foreground">Search</a>
            <span class="px-1">/</span>
            <span>{{ $tool->categories->first()?->label() }}</span>
            <span class="px-1">/</span>
            <span class="text-foreground">{{ $tool->name }}</span>
        </p>

        <x-slot:actions>
            <x-ui.button variant="primary" icon="layer" label="Add to stack"/>
        </x-slot:actions>
    </x-domain.app.topbar>

    <div class="flex flex-col gap-16 px-8 py-8">
        {{-- Header --}}
        <section class="flex flex-col gap-6">
            <div class="flex items-start gap-3">
                <div class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-sidebar-primary text-sidebar-primary-foreground">
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
                        <p class="text-sm text-muted-foreground">Rating</p>
                        <p class="text-3xl font-semibold text-card-foreground">4.7</p>
                    </div>
                </div>

                <div class="flex flex-1 flex-col gap-6 rounded-xl border border-border bg-gradient-to-b from-primary/0 to-primary/5 p-6 shadow-xs">
                    <div class="flex flex-col gap-1.5">
                        <p class="text-sm text-muted-foreground">Collections</p>
                        <p class="text-3xl font-semibold text-card-foreground">175</p>
                    </div>
                </div>

                <div class="flex flex-1 flex-col gap-6 rounded-xl border border-border bg-gradient-to-b from-primary/0 to-primary/5 p-6 shadow-xs">
                    <div class="flex flex-col gap-1.5">
                        <p class="text-sm text-muted-foreground">Followers</p>
                        <p class="text-3xl font-semibold text-card-foreground">1 290</p>
                    </div>
                </div>

                <div class="flex flex-1 flex-col gap-6 rounded-xl border border-border bg-gradient-to-b from-primary/0 to-primary/5 p-6 shadow-xs">
                    <div class="flex flex-col gap-1.5">
                        <p class="text-sm text-muted-foreground">Views</p>
                        <p class="text-3xl font-semibold text-card-foreground">12 290</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- About --}}
        <section class="flex flex-col gap-4">
            <div class="flex flex-col gap-2">
                <p class="text-sm font-medium text-foreground">About</p>
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
                <p class="text-sm font-medium text-foreground">Links</p>

                <div class="flex flex-col items-start gap-1">
                    <a href="{{ $tool->website_url }}" target="_blank" class="flex items-center gap-1 rounded-md px-2 py-2 text-xs font-medium text-primary hover:bg-muted">
                        <x-ui.icon.globe-02 size="xs"/>
                        {{ str($tool->website_url)->after('://')->rtrim('/') }}
                    </a>

                    <span class="flex items-center gap-1 rounded-md px-2 py-2 text-xs font-medium text-muted-foreground">
                        <x-ui.icon.github size="xs"/>
                        Not linked yet
                    </span>

                    <span class="flex items-center gap-1 rounded-md px-2 py-2 text-xs font-medium text-muted-foreground">
                        <x-ui.icon.x size="xs"/>
                        Not linked yet
                    </span>
                </div>
            </div>

            <div class="flex items-center">
                <div class="h-full w-px bg-border"></div>
            </div>

            <div class="flex flex-col gap-2.5">
                <p class="text-sm font-medium text-foreground">Details</p>

                <div class="flex w-[350px] flex-col gap-2.5">
                    <div class="flex items-center gap-2.5">
                        <p class="flex-1 text-sm font-medium text-muted-foreground">Company</p>
                        <p class="text-base text-foreground">{{ $tool->team->name }}</p>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <p class="flex-1 text-sm font-medium text-muted-foreground">Category</p>
                        <p class="text-base text-foreground">{{ $tool->categories->map->label()->join(', ') }}</p>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <p class="flex-1 text-sm font-medium text-muted-foreground">Pricing</p>
                        <p class="text-base text-foreground">{{ $tool->pricing->label() }}</p>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <p class="flex-1 text-sm font-medium text-muted-foreground">Added</p>
                        <p class="text-base text-foreground">{{ $tool->created_at?->format('Y') }}</p>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <p class="flex-1 text-sm font-medium text-muted-foreground">Status</p>
                        <p class="text-base text-foreground">Active</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Gallery --}}
        <section class="flex flex-col gap-6">
            <div class="flex items-end justify-between gap-6">
                <div class="flex flex-1 flex-col gap-2">
                    <p class="text-sm font-medium text-foreground">Gallery</p>
                    <p class="text-lg font-semibold text-foreground">A closer look at {{ $tool->name }}</p>
                </div>

                <p class="w-[400px] text-right text-base text-foreground">Browse detailed visuals that showcase the experience, features, and design across every variant.</p>
            </div>

            <div class="flex gap-4 overflow-x-auto">
                @foreach (['Overview', 'Workflow', 'Collaboration'] as $shot)
                    <div class="w-[320px] shrink-0 overflow-clip rounded-xl border border-foreground/10 bg-card shadow-xs">
                        <div class="aspect-video w-full bg-gradient-to-br from-muted to-border"></div>

                        <div class="flex flex-col gap-1 p-4">
                            <p class="text-base font-medium text-card-foreground">{{ $shot }}</p>
                            <p class="text-sm text-muted-foreground">A look at the {{ strtolower($shot) }} experience.</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Pricing --}}
        <section class="flex flex-col gap-6">
            <div class="flex items-end justify-between gap-6">
                <div class="flex flex-1 flex-col gap-2">
                    <p class="text-sm font-medium text-foreground">Pricing</p>
                    <p class="text-lg font-semibold text-foreground">4 plans</p>
                </div>
            </div>

            <div class="flex gap-6">
                @foreach ([
                    ['name' => 'Free', 'price' => '$0', 'features' => ['Unlimited workspaces', '2 teams', 'Max 20 tools in stack']],
                    ['name' => 'Basic', 'price' => '$10 per user/month', 'features' => ['All free features +', 'Item title 1', 'Item title 2', 'Item title 3']],
                    ['name' => 'Business', 'price' => '$16 per user/month', 'features' => ['All basic features +']],
                    ['name' => 'Entreprise', 'price' => 'Custom', 'features' => ['All business features +']],
                ] as $plan)
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
                <p class="text-lg font-semibold text-foreground">What web users have to say</p>

                <div class="flex items-center gap-2">
                    <x-ui.button variant="secondary" size="xs" icon="arrow-up-right-01" label="View all reviews"/>
                    <x-ui.button variant="primary" size="xs" icon="pen-01" label="Write a review"/>
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

                                <p class="shrink-0 text-sm font-medium text-muted-foreground">{{ $row['percent'] }}% · {{ number_format($row['reviews']) }} reviews</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                @foreach ([
                    ['name' => 'Lucie', 'role' => 'Product Designer · Mistral', 'stars' => 5, 'ago' => '3d ago', 'body' => 'The cycle model finally clicked with our team. We replaced three tools with one and shipped two cycles before anyone asked for a feature back.'],
                    ['name' => 'Raj', 'role' => 'UX Researcher · Innovate Labs', 'stars' => 3, 'ago' => '2d ago', 'body' => 'User feedback led us to a breakthrough in our onboarding process. Implementing interactive tutorials resulted in a 30% decrease in drop-off rates.'],
                    ['name' => 'Maya', 'role' => 'Visual Designer · PixelCraft', 'stars' => 2, 'ago' => '1w ago', 'body' => "Revisiting our brand's color palette has received positive feedback from stakeholders. The fresh look aligns better with our mission and resonates with our audience."],
                ] as $review)
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
                    <p class="text-sm font-medium text-foreground">Activity</p>
                    <p class="text-lg font-semibold text-foreground">Latest releases</p>
                </div>

                <x-ui.button variant="secondary" size="xs" label="View all activity"/>
            </div>

            <div class="flex flex-col">
                @foreach ([
                    ['title' => 'User feedback tool', 'description' => 'A new feature that allows users to submit feedback directly from the app.', 'date' => 'June 12'],
                    ['title' => 'Real-time notifications', 'description' => 'Users receive instant alerts for important updates and messages.', 'date' => 'June 12'],
                    ['title' => 'Contrast slider', 'description' => 'An option in the settings to choose the right amount of contrast.', 'date' => 'June 12'],
                ] as $index => $release)
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
