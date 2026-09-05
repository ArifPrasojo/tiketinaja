@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">
        <div class="flex gap-2 items-center justify-between sm:hidden">
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
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:gap-2 sm:items-center sm:justify-between">
            <p class="text-sm text-on-surface-variant">
                {!! __('Showing') !!}
                @if ($paginator->firstItem())
                    <span class="font-medium text-pitch-black">{{ $paginator->firstItem() }}</span>
                    {!! __('to') !!}
                    <span class="font-medium text-pitch-black">{{ $paginator->lastItem() }}</span>
                @else
                    {{ $paginator->count() }}
                @endif
                {!! __('of') !!}
                <span class="font-medium text-pitch-black">{{ $paginator->total() }}</span>
                {!! __('results') !!}
            </p>

            <span class="inline-flex rtl:flex-row-reverse rounded-lg overflow-hidden border border-stadium-gray">
                @if ($paginator->onFirstPage())
                    <span class="inline-flex items-center px-2 py-2 text-sm text-on-surface-variant bg-white cursor-not-allowed" aria-hidden="true">
                        <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-2 py-2 text-sm text-on-surface-variant bg-white hover:bg-slate-50 hover:text-electric-blue transition" aria-label="{{ __('pagination.previous') }}">
                        <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                    </a>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-on-surface-variant bg-white border-l border-stadium-gray">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="inline-flex items-center px-4 py-2 text-sm font-bold text-white bg-electric-blue border-l border-electric-blue">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-pitch-black bg-white border-l border-stadium-gray hover:bg-slate-50 hover:text-electric-blue transition">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-2 py-2 text-sm text-on-surface-variant bg-white border-l border-stadium-gray hover:bg-slate-50 hover:text-electric-blue transition" aria-label="{{ __('pagination.next') }}">
                        <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                    </a>
                @else
                    <span class="inline-flex items-center px-2 py-2 text-sm text-on-surface-variant bg-white border-l border-stadium-gray cursor-not-allowed" aria-hidden="true">
                        <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                    </span>
                @endif
            </span>
        </div>
    </nav>
@endif
