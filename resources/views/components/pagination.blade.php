@props(['paginator' => null])

@if ($paginator && $paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" {{ $attributes->class('flex items-center justify-between gap-4') }}>
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="btn btn-outline btn-sm pointer-events-none opacity-50">
                <x-icon icon="arrow-right" class="h-4 w-4 rotate-180" />
                Previous
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn btn-outline btn-sm">
                <x-icon icon="arrow-right" class="h-4 w-4 rotate-180" />
                Previous
            </a>
        @endif

        <div class="flex items-center gap-1.5">
            @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span aria-current="page" class="grid h-9 w-9 place-items-center rounded-xl bg-primary text-sm font-semibold text-primary-fg shadow-soft">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="grid h-9 w-9 place-items-center rounded-xl text-sm font-medium text-content-soft transition-colors hover:bg-surface-alt hover:text-content">{{ $page }}</a>
                @endif
            @endforeach
        </div>

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn btn-outline btn-sm">
                Next
                <x-icon icon="arrow-right" class="h-4 w-4" />
            </a>
        @else
            <span class="btn btn-outline btn-sm pointer-events-none opacity-50">
                Next
                <x-icon icon="arrow-right" class="h-4 w-4" />
            </span>
        @endif
    </nav>
@endif
