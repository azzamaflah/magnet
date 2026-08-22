{{-- resources/views/vendor/pagination/magnet.blade.php --}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman" class="flex items-center justify-end">
        <div class="inline-flex items-center gap-1.5 overflow-x-auto max-w-full py-1">

            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="@lang('pagination.previous')"
                    class="inline-flex items-center justify-center h-9 px-3.5 rounded-xl text-xs font-semibold
                           border border-[#3a3a3a]/40 bg-white/[0.02] text-gray-500 cursor-not-allowed select-none">
                    <i class="fas fa-chevron-left text-[10px] mr-1.5"></i>
                    <span>Prev</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')"
                    class="inline-flex items-center justify-center h-9 px-3.5 rounded-xl text-xs font-semibold
                           border border-[#3a3a3a] bg-white/5 text-gray-300 hover:text-white hover:border-[#d97757]/60 hover:bg-[#d97757]/15 transition-all shadow-sm">
                    <i class="fas fa-chevron-left text-[10px] mr-1.5"></i>
                    <span>Prev</span>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span aria-disabled="true"
                        class="inline-flex items-center justify-center h-9 w-8 text-xs font-bold text-gray-500 select-none">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page"
                                class="inline-flex items-center justify-center h-9 min-w-9 px-3 rounded-xl text-xs font-bold
                                       border border-[#d97757] bg-gradient-to-r from-[#d97757] to-[#e88968] text-white shadow-[0_4px_16px_rgba(217,119,87,0.35)]">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                                class="inline-flex items-center justify-center h-9 min-w-9 px-3 rounded-xl text-xs font-semibold
                                       border border-[#3a3a3a] bg-white/5 text-gray-300
                                       hover:text-white hover:border-[#d97757]/60 hover:bg-[#d97757]/15 transition-all shadow-sm">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')"
                    class="inline-flex items-center justify-center h-9 px-3.5 rounded-xl text-xs font-semibold
                           border border-[#3a3a3a] bg-white/5 text-gray-300 hover:text-white hover:border-[#d97757]/60 hover:bg-[#d97757]/15 transition-all shadow-sm">
                    <span>Next</span>
                    <i class="fas fa-chevron-right text-[10px] ml-1.5"></i>
                </a>
            @else
                <span aria-disabled="true" aria-label="@lang('pagination.next')"
                    class="inline-flex items-center justify-center h-9 px-3.5 rounded-xl text-xs font-semibold
                           border border-[#3a3a3a]/40 bg-white/[0.02] text-gray-500 cursor-not-allowed select-none">
                    <span>Next</span>
                    <i class="fas fa-chevron-right text-[10px] ml-1.5"></i>
                </span>
            @endif

        </div>
    </nav>
@endif
