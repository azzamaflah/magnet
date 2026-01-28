@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-end">
        <div class="inline-flex items-center gap-1 overflow-x-auto max-w-full">

            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="@lang('pagination.previous')"
                    class="inline-flex items-center justify-center h-9 min-w-9 px-3 rounded-lg
                           border border-[#3a3a3a] bg-white/5 text-gray-500 cursor-not-allowed">
                    ‹
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')"
                    class="inline-flex items-center justify-center h-9 min-w-9 px-3 rounded-lg
                           border border-[#3a3a3a] bg-white/5 text-gray-300 hover:text-white hover:border-[#d97757]/60 hover:bg-[#d97757]/10 transition">
                    ‹
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span aria-disabled="true"
                        class="inline-flex items-center justify-center h-9 px-2 rounded-lg text-gray-500">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page"
                                class="inline-flex items-center justify-center h-9 min-w-9 px-3 rounded-lg
                                       border border-[#d97757] bg-[#d97757] text-white shadow-[0_10px_24px_rgba(217,119,87,0.25)]">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                                class="inline-flex items-center justify-center h-9 min-w-9 px-3 rounded-lg
                                       border border-[#3a3a3a] bg-white/5 text-gray-300
                                       hover:text-white hover:border-[#d97757]/60 hover:bg-[#d97757]/10 transition">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')"
                    class="inline-flex items-center justify-center h-9 min-w-9 px-3 rounded-lg
                           border border-[#3a3a3a] bg-white/5 text-gray-300 hover:text-white hover:border-[#d97757]/60 hover:bg-[#d97757]/10 transition">
                    ›
                </a>
            @else
                <span aria-disabled="true" aria-label="@lang('pagination.next')"
                    class="inline-flex items-center justify-center h-9 min-w-9 px-3 rounded-lg
                           border border-[#3a3a3a] bg-white/5 text-gray-500 cursor-not-allowed">
                    ›
                </span>
            @endif

        </div>
    </nav>
@endif
