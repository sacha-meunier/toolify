@props([
    'surveys',
    'emptyIcon' => 'tool-view',
    'emptyTitle' => null,
    'emptyDescription' => null,
])

@if ($surveys->isEmpty())
    <div class="border border-border rounded-md">
        <x-domain.app.empty-state :icon="$emptyIcon" :title="$emptyTitle" :description="$emptyDescription">
            {{ $slot ?? '' }}
        </x-domain.app.empty-state>
    </div>
@else
    <div class="flex flex-col divide-y border border-border rounded-md">
        @foreach ($surveys as $survey)
            <x-domain.app.survey-list-item :survey="$survey" :class="trim(($loop->first ? 'rounded-t-md ' : '').($loop->last ? 'rounded-b-md' : ''))"/>
        @endforeach
    </div>
@endif
