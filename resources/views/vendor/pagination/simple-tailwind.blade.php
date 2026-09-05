@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex gap-2 items-center justify-between">
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-on-surface-variant bg-white border border-stadium-gray cursor-not-allowed rounded-lg">
                {!! __('pagination.previous') !!}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-4 py-2 text-sm font-medium text-pitch-black bg-white border border-stadium-gray rounded-lg hover:border-electric-blue hover:text-electric-blue transition">
                {!! __('pagination.previous') !!}
            </a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-4 py-2 text-sm font-medium text-pitch-black bg-white border border-stadium-gray rounded-lg hover:border-electric-blue hover:text-electric-blue transition">
                {!! __('pagination.next') !!}
            </a>
        @else
            <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-on-surface-variant bg-white border border-stadium-gray cursor-not-allowed rounded-lg">
                {!! __('pagination.next') !!}
            </span>
        @endif
    </nav>
@endif
