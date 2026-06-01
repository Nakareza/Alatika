@props([
    'title',
    'value',
    'icon',
    'color' => 'blue',
])

@php
    $colors = [
        'blue' => 'bg-blue-600',
        'green' => 'bg-emerald-500',
        'yellow' => 'bg-amber-500',
        'red' => 'bg-rose-500',
        'indigo' => 'bg-indigo-500',
        'purple' => 'bg-purple-500',
    ];
@endphp

<div class="card p-5 hover:shadow-md transition-all duration-200">
    <div class="flex items-center justify-between mb-2">
        <div class="flex items-center gap-2">
            <span class="w-1 h-3.5 rounded-full inline-block {{ $colors[$color] ?? 'bg-blue-600' }}"></span>
            <p class="text-sm font-semibold">{{ $title }}</p>
        </div>

        <i class="{{ $icon }} text-slate-400 text-xs"></i>
    </div>

    <div class="mt-2">
        <p class="text-3xl font-bold tracking-tight"
           style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">
            {{ $value }}
        </p>
    </div>
</div>