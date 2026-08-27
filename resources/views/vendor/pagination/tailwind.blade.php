@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between gap-4">
        <div>
            @if ($paginator->onFirstPage())
                <span class="inline-flex size-8 items-center justify-center rounded-lg text-muted-foreground opacity-50">
                    <x-ui.icon.arrow-left-01 size="sm"/>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex size-8 items-center justify-center rounded-lg text-foreground hover:bg-muted focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-ring/50" aria-label="{{ __('pagination.previous') }}">
                    <x-ui.icon.arrow-left-01 size="sm"/>
                </a>
            @endif
        </div>

        <div class="flex items-center gap-1">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span aria-disabled="true" class="inline-flex size-8 items-center justify-center text-sm font-medium text-muted-foreground">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="inline-flex size-8 items-center justify-center rounded-lg bg-muted text-sm font-medium text-foreground">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="inline-flex size-8 items-center justify-center rounded-lg text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-ring/50" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        <div>
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex size-8 items-center justify-center rounded-lg text-foreground hover:bg-muted focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-ring/50" aria-label="{{ __('pagination.next') }}">
                    <x-ui.icon.arrow-right-01 size="sm"/>
                </a>
            @else
                <span class="inline-flex size-8 items-center justify-center rounded-lg text-muted-foreground opacity-50">
                    <x-ui.icon.arrow-right-01 size="sm"/>
                </span>
            @endif
        </div>
    </nav>
@endif
