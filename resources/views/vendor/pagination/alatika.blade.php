@if ($paginator->hasPages())

<nav class="flex flex-col md:flex-row items-center justify-between gap-5 mt-6">

    {{-- Info --}}
    <div class="text-sm text-slate-500">

        Menampilkan

        <span class="font-semibold text-[#1E2B4A]">
            {{ $paginator->firstItem() }}
        </span>

        -

        <span class="font-semibold text-[#1E2B4A]">
            {{ $paginator->lastItem() }}
        </span>

        dari

        <span class="font-semibold text-[#1E2B4A]">
            {{ $paginator->total() }}
        </span>

        data

    </div>

    {{-- Pagination --}}
    <div class="flex items-center gap-2">

        {{-- Previous --}}
        @if ($paginator->onFirstPage())

            <span
                class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100 text-slate-300 cursor-not-allowed">

                <i class="fas fa-chevron-left"></i>

            </span>

        @else

            <a href="{{ $paginator->previousPageUrl() }}"
               class="w-10 h-10 flex items-center justify-center rounded-xl border border-[#D4E6F8] bg-white text-[#185FA5] hover:bg-[#F5F8FF] transition">

                <i class="fas fa-chevron-left"></i>

            </a>

        @endif

        {{-- Nomor --}}
        @foreach ($elements as $element)

            @if (is_string($element))

                <span class="px-3 text-slate-400">
                    ...
                </span>

            @endif

            @if (is_array($element))

                @foreach ($element as $page => $url)

                    @if ($page == $paginator->currentPage())

                        <span
                            class="w-10 h-10 flex items-center justify-center rounded-xl bg-[#185FA5] text-white font-semibold shadow">

                            {{ $page }}

                        </span>

                    @else

                        <a href="{{ $url }}"
                           class="w-10 h-10 flex items-center justify-center rounded-xl border border-[#D4E6F8] bg-white text-[#185FA5] hover:bg-[#F5F8FF] hover:border-[#185FA5] transition">

                            {{ $page }}

                        </a>

                    @endif

                @endforeach

            @endif

        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())

            <a href="{{ $paginator->nextPageUrl() }}"
               class="w-10 h-10 flex items-center justify-center rounded-xl border border-[#D4E6F8] bg-white text-[#185FA5] hover:bg-[#F5F8FF] transition">

                <i class="fas fa-chevron-right"></i>

            </a>

        @else

            <span
                class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100 text-slate-300 cursor-not-allowed">

                <i class="fas fa-chevron-right"></i>

            </span>

        @endif

    </div>

</nav>

@endif