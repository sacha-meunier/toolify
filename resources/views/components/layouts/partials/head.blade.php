<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Applies the persisted theme before first paint (and again after every
    wire:navigate swap) so the page never flashes light before Alpine boots and
    reactively takes over via root.blade.php's x-data/x-init. --}}
    <script>
        (function () {
            function applyTheme() {
                document.documentElement.classList.toggle('dark', localStorage.getItem('theme') === 'dark');
            }

            applyTheme();
            document.addEventListener('livewire:navigated', applyTheme);
        })();
    </script>

    <title>{{ $title ?? config('app.name') }}</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>
