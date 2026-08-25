@blaze()

@props([
    'method',
    'config',
    'primary' => false,
    'isLastAuthMethodUsed' => false,
])

@if ($isLastAuthMethodUsed)
    <div {{ $attributes->class('flex flex-col gap-2') }}>
        @if ($config['action'])
            <x-ui.button wire:click="{{ $config['action'] }}" variant="{{ $primary ? 'primary' : 'secondary' }}"
                         size="lg" label="{{ $config['label'] }}"/>
        @else
            <x-ui.button variant="{{ $primary ? 'primary' : 'secondary' }}" size="lg"
                         label="{{ $config['label'] }}" disabled/>
        @endif
        <p class="text-muted-foreground text-sm text-center">{{ __('auth/components/method.used_last_time', ['method' => $method]) }}</p>
    </div>
@else
    @if ($config['action'])
        <x-ui.button {{ $attributes }} wire:click="{{ $config['action'] }}"
                     variant="{{ $primary ? 'primary' : 'secondary' }}" size="lg" label="{{ $config['label'] }}"/>
    @else
        <x-ui.button {{ $attributes }} variant="{{ $primary ? 'primary' : 'secondary' }}" size="lg"
                     label="{{ $config['label'] }}" disabled/>
    @endif
@endif
