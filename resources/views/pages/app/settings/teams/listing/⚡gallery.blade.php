<?php

use App\Livewire\Forms\Settings\ToolGalleryForm;
use App\Models\Team;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts::shells.settings')] class extends Component
{
    use WithFileUploads;

    public Team $team;

    public ToolGalleryForm $form;

    public function mount(Team $team): void
    {
        Gate::authorize('manageListing', $team);

        $this->team = $team;
        $this->form->setTool($team->tool);
    }

    public function save(): void
    {
        $this->form->update();

        $this->dispatch('tool-saved');
    }

    public function removeImage(int $index): void
    {
        $this->form->removeImage($index);
    }

    public function removeNewImage(int $index): void
    {
        unset($this->form->newImages[$index]);

        $this->form->newImages = array_values($this->form->newImages);
    }

    public function removeBanner(): void
    {
        $this->form->removeBanner();
    }
};
?>

<div class="flex h-full min-h-0 flex-col" x-data="{ lightboxImage: null, confirmRemoveType: null, confirmRemoveIndex: null, confirmRemoveOpen: false }">
    <x-domain.app.topbar>
        <x-domain.app.topbar.breadcrumb :items="[
            __('app/settings/teams/listing/gallery.breadcrumb_settings') => null,
            __('app/settings/teams/listing/gallery.breadcrumb_teams') => null,
            $team->name => null,
            __('app/settings/teams/listing/gallery.breadcrumb_listing') => route('settings.teams.listing.index', $team),
            __('app/settings/teams/listing/gallery.breadcrumb_gallery') => null,
        ]"/>

        <x-slot:actions>
            <x-ui.field.saved event="tool-saved"/>

            <x-ui.button
                variant="outline"
                size="sm"
                icon="arrow-up-right-01"
                :label="__('app/settings/teams/listing/gallery.preview_page')"
                :href="$team->tool ? route('tools.show', $team->tool) : null"
                :disabled="! $team->tool"
                target="_blank"
            />

            <x-ui.button variant="primary" size="sm" :label="__('app/settings/teams/listing/gallery.save_changes')" wire:click="save" x-bind:disabled="! ($wire.form.newImages.length || $wire.form.banner)"/>
        </x-slot:actions>
    </x-domain.app.topbar>

    <div class="min-h-0 flex-1 overflow-y-auto">
        <div class="mx-auto flex w-full max-w-4xl flex-col gap-8 px-4 py-6 lg:px-10 lg:py-10">
            <header class="flex flex-col gap-1 px-4">
                <h1 class="text-3xl font-semibold text-foreground">{{ __('app/settings/teams/listing/gallery.heading') }}</h1>
                <p class="text-sm text-muted-foreground">{{ __('app/settings/teams/listing/gallery.description') }}</p>
            </header>

            <x-domain.app.settings.section :label="__('app/settings/teams/listing/gallery.banner_section_label')" :description="__('app/settings/teams/listing/gallery.banner_section_description')" :card="false">
                <div class="flex flex-col gap-3 px-4">
                    <div class="max-w-sm">
                        @if ($form->banner && $form->banner->isPreviewable())
                            <div class="group relative aspect-video overflow-clip rounded-xl border border-border bg-muted">
                                <img src="{{ $form->banner->temporaryUrl() }}" alt="" class="size-full cursor-zoom-in object-contain" @click="lightboxImage = '{{ $form->banner->temporaryUrl() }}'">

                                <button
                                    type="button"
                                    wire:click="$set('form.banner', null)"
                                    class="extend-touch-target absolute top-0 right-0 flex size-12 items-start justify-end p-2 opacity-100 transition-opacity lg:opacity-0 lg:group-hover:opacity-100"
                                >
                                    <span class="flex size-6 items-center justify-center rounded-full bg-popover text-destructive shadow-xs">
                                        <x-ui.icon.cancel-01 size="xs"/>
                                    </span>
                                    <span class="sr-only">{{ __('app/settings/teams/listing/gallery.remove') }}</span>
                                </button>
                            </div>
                        @elseif ($form->tool?->banner_url)
                            <div class="group relative aspect-video overflow-clip rounded-xl border border-border bg-muted">
                                <img src="{{ $form->tool->banner_url }}" alt="" class="size-full cursor-zoom-in object-contain" @click="lightboxImage = '{{ $form->tool->banner_url }}'">

                                <button
                                    type="button"
                                    @click="confirmRemoveType = 'banner'; confirmRemoveOpen = true"
                                    class="extend-touch-target absolute top-0 right-0 flex size-12 items-start justify-end p-2 opacity-100 transition-opacity lg:opacity-0 lg:group-hover:opacity-100"
                                >
                                    <span class="flex size-6 items-center justify-center rounded-full bg-popover text-destructive shadow-xs">
                                        <x-ui.icon.cancel-01 size="xs"/>
                                    </span>
                                    <span class="sr-only">{{ __('app/settings/teams/listing/gallery.remove') }}</span>
                                </button>
                            </div>
                        @else
                            <button
                                type="button"
                                @click="$refs.bannerInput.click()"
                                class="flex aspect-video w-full flex-col items-center justify-center gap-1.5 rounded-xl border border-dashed border-input text-muted-foreground hover:border-ring hover:text-foreground"
                            >
                                <x-ui.icon.image-02 size="sm"/>
                                <span class="text-xs font-medium">{{ __('app/settings/teams/listing/gallery.add_banner') }}</span>
                            </button>
                        @endif

                        <input type="file" wire:model="form.banner" accept="image/*" class="hidden" x-ref="bannerInput">
                    </div>

                    @error('form.banner')
                    <x-ui.field.error>{{ $message }}</x-ui.field.error>
                    @enderror
                </div>
            </x-domain.app.settings.section>

            @php
                $existingCount = $form->tool?->gallery?->count() ?? 0;
                $totalCount = $existingCount + count($form->newImages);
                $limitReached = $totalCount >= ToolGalleryForm::MAX_IMAGES;
            @endphp

            <x-domain.app.settings.section :label="__('app/settings/teams/listing/gallery.gallery_section_label')" :description="__('app/settings/teams/listing/gallery.gallery_section_description')" :card="false">
                <div class="flex flex-col gap-3 px-4">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        @foreach ($form->tool?->gallery ?? [] as $index => $url)
                            <div class="group relative aspect-video overflow-clip rounded-xl border border-border bg-muted" wire:key="gallery-existing-{{ $index }}">
                                <img src="{{ $url }}" alt="" class="size-full cursor-zoom-in object-contain" @click="lightboxImage = '{{ $url }}'">

                                <button
                                    type="button"
                                    @click="confirmRemoveType = 'existing'; confirmRemoveIndex = {{ $index }}; confirmRemoveOpen = true"
                                    class="extend-touch-target absolute top-0 right-0 flex size-12 items-start justify-end p-2 opacity-100 transition-opacity lg:opacity-0 lg:group-hover:opacity-100"
                                >
                                    <span class="flex size-6 items-center justify-center rounded-full bg-popover text-destructive shadow-xs">
                                        <x-ui.icon.cancel-01 size="xs"/>
                                    </span>
                                    <span class="sr-only">{{ __('app/settings/teams/listing/gallery.remove') }}</span>
                                </button>
                            </div>
                        @endforeach

                        @foreach ($form->newImages as $index => $image)
                            <div class="group relative aspect-video overflow-clip rounded-xl border border-border bg-muted" wire:key="gallery-new-{{ $index }}">
                                @if ($image->isPreviewable())
                                    <img src="{{ $image->temporaryUrl() }}" alt="" class="size-full cursor-zoom-in object-contain" @click="lightboxImage = '{{ $image->temporaryUrl() }}'">
                                @endif

                                <button
                                    type="button"
                                    wire:click="removeNewImage({{ $index }})"
                                    class="extend-touch-target absolute top-0 right-0 flex size-12 items-start justify-end p-2 opacity-100 transition-opacity lg:opacity-0 lg:group-hover:opacity-100"
                                >
                                    <span class="flex size-6 items-center justify-center rounded-full bg-popover text-destructive shadow-xs">
                                        <x-ui.icon.cancel-01 size="xs"/>
                                    </span>
                                    <span class="sr-only">{{ __('app/settings/teams/listing/gallery.remove') }}</span>
                                </button>
                            </div>
                        @endforeach

                        @unless ($limitReached)
                            <button
                                type="button"
                                @click="$refs.galleryInput.click()"
                                class="flex aspect-video flex-col items-center justify-center gap-1.5 rounded-xl border border-dashed border-input text-muted-foreground hover:border-ring hover:text-foreground"
                            >
                                <x-ui.icon.image-02 size="sm"/>
                                <span class="text-xs font-medium">{{ __('app/settings/teams/listing/gallery.add_images') }}</span>
                            </button>

                            <input type="file" wire:model="form.newImages" accept="image/*" multiple class="hidden" x-ref="galleryInput">
                        @endunless
                    </div>

                    <div class="flex items-center justify-between gap-3">
                        <p class="text-xs text-muted-foreground">{{ __('app/settings/teams/listing/gallery.rules', ['max' => ToolGalleryForm::MAX_IMAGES]) }}</p>
                        <p class="shrink-0 text-xs font-medium text-muted-foreground">{{ __('app/settings/teams/listing/gallery.count', ['filled' => $totalCount, 'max' => ToolGalleryForm::MAX_IMAGES]) }}</p>
                    </div>

                    @error('form.newImages')
                    <x-ui.field.error>{{ $message }}</x-ui.field.error>
                    @enderror

                    @error('form.newImages.*')
                    <x-ui.field.error>{{ $message }}</x-ui.field.error>
                    @enderror
                </div>
            </x-domain.app.settings.section>
        </div>
    </div>

    <x-domain.app.settings.listing-nav
        :prev-href="route('settings.teams.listing.links', $team)"
        :prev-label="__('app/settings/teams/listing/gallery.nav_prev_label')"
        :next-href="route('settings.teams.listing.basics', $team)"
        :next-label="__('app/settings/teams/listing/gallery.nav_next_label')"
    />

    <x-ui.confirm-modal
        show="confirmRemoveOpen"
        :heading="__('app/settings/teams/listing/gallery.remove_modal_heading')"
        :description="__('app/settings/teams/listing/gallery.remove_modal_description')"
        :cancel-label="__('app/settings/teams/listing/gallery.cancel')"
    >
        <x-ui.button
            variant="destructive"
            :label="__('app/settings/teams/listing/gallery.remove')"
            @click="confirmRemoveType === 'banner' ? $wire.call('removeBanner') : $wire.call('removeImage', confirmRemoveIndex); confirmRemoveOpen = false"
        />
    </x-ui.confirm-modal>

    <x-ui.lightbox :label="__('app/settings/teams/listing/gallery.close')"/>
</div>
