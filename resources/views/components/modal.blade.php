@props([
    'name',                    // wajib — id unik modal, contoh: 'logout', 'hapus-alat'
    'title'    => '',          // judul modal
    'size'     => 'sm',        // sm | md | lg
    'type'     => 'default',   // default | danger | warning | success
])

@php
$sizes = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
];

$icons = [
    'danger'  => ['icon' => 'fa-exclamation-triangle', 'bg' => '#FEF2F2', 'color' => '#EF4444'],
    'warning' => ['icon' => 'fa-exclamation-circle',   'bg' => '#FFFBEB', 'color' => '#F59E0B'],
    'success' => ['icon' => 'fa-check-circle',         'bg' => '#ECFDF5', 'color' => '#10B981'],
    'default' => ['icon' => 'fa-info-circle',          'bg' => '#EBF3FD', 'color' => '#185FA5'],
];

$icon   = $icons[$type];
$sizeClass = $sizes[$size] ?? $sizes['sm'];
@endphp

<div
    x-data="{ open: false }"
    x-on:open-modal-{{ $name }}.window="open = true"
    x-on:close-modal-{{ $name }}.window="open = false"
    @keydown.escape.window="open = false"
    x-cloak>

    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="background:rgba(30,43,74,0.35);backdrop-filter:blur(4px);"
        @click.self="open = false">

        {{-- Modal card --}}
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-2"
            class="relative bg-white rounded-2xl w-full {{ $sizeClass }} p-6"
            style="box-shadow:0 20px 60px rgba(30,43,74,0.18);border:1px solid #EBF3FD;"
            @click.stop>

            {{-- Icon (kalau bukan default tanpa title) --}}
            @if($type !== 'default' || !$title)
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-4"
                 style="background:{{ $icon['bg'] }};">
                <i class="fas {{ $icon['icon'] }}" style="color:{{ $icon['color'] }};font-size:18px;"></i>
            </div>
            @endif

            {{-- Title --}}
            @if($title)
            <h3 class="font-bold text-base text-center mb-1.5"
                style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">
                {{ $title }}
            </h3>
            @endif

            {{-- Konten slot --}}
            {{ $slot }}

            {{-- Footer slot (opsional) --}}
            @isset($footer)
            <div class="flex gap-2.5 mt-5">
                {{ $footer }}
            </div>
            @endisset

        </div>
    </div>
</div>