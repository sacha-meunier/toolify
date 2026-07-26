<x-layouts.root>
    <div class="min-h-screen flex justify-center py-12 bg-background text-foreground">
        <div {{ $attributes->merge(["class" => "flex flex-col gap-7 mt-[max(0px,-240px+50svh)] w-full max-w-2xs"]) }}>
            {{ $slot }}
        </div>
    </div>
</x-layouts.root>
