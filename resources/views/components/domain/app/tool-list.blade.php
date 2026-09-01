@props([
    'tools',
    'emptyIcon' => 'inbox',
    'emptyTitle' => null,
    'emptyDescription' => null,
])

@if ($tools->isEmpty())
    <div class="flex flex-1 items-center justify-center">
        <x-domain.app.empty-state :icon="$emptyIcon" :title="$emptyTitle" :description="$emptyDescription"/>
    </div>
@else
    <div class="flex flex-col divide-y border border-border rounded-md">
        @foreach ($tools as $tool)
            <x-domain.app.tool-list-item :tool="$tool" :class="trim(($loop->first ? 'rounded-t-md ' : '').($loop->last ? 'rounded-b-md' : ''))"/>
        @endforeach
    </div>
@endif
