@props([
    'title' => null,
])

<div class="bg-white rounded-3xl border border-slate-100 overflow-hidden shadow-sm">

    @if($title)
        <div class="px-6 py-5 border-b border-slate-100">
            <h3 class="text-lg font-bold text-[#1E2B4A]">
                {{ $title }}
            </h3>
        </div>
    @endif

    <div class="overflow-x-auto">

        <table class="w-full">

            {{ $slot }}

        </table>

    </div>

</div>