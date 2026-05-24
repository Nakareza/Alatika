{{-- Card Statistik untuk Admin Dashboard --}}
@props(['title', 'value', 'icon', 'color' => 'blue', 'trend' => null])

@php
$colorClasses = [
    'blue' => 'bg-blue-50 text-blue-600 border border-blue-100',
    'green' => 'bg-emerald-50 text-emerald-600 border border-emerald-100',
    'red' => 'bg-rose-50 text-rose-600 border border-rose-100',
    'purple' => 'bg-purple-50 text-purple-600 border border-purple-100',
    'yellow' => 'bg-amber-50 text-amber-600 border border-amber-100',
    'indigo' => 'bg-indigo-50 text-indigo-600 border border-indigo-100',
    'orange' => 'bg-orange-50 text-orange-600 border border-orange-100',
];
$styleClass = $colorClasses[$color] ?? $colorClasses['blue'];
@endphp

<div class="bg-white/90 backdrop-blur-sm rounded-2xl p-6 shadow-sm border border-slate-100 hover:border-slate-200 transition-all duration-300">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">{{ $title }}</p>
            <h3 class="text-3xl font-extrabold text-[#1E2B4A] mb-1">{{ $value }}</h3>
            
            @if($trend)
            <div class="flex items-center gap-1 text-xs">
                @if($trend > 0)
                    <i class="fas fa-arrow-up text-emerald-500"></i>
                    <span class="text-emerald-500 font-semibold">+{{ $trend }}%</span>
                @else
                    <i class="fas fa-arrow-down text-rose-500"></i>
                    <span class="text-rose-500 font-semibold">{{ $trend }}%</span>
                @endif
                <span class="text-slate-400 ml-1">dari bulan lalu</span>
            </div>
            @endif
        </div>
        
        <div class="w-12 h-12 rounded-xl {{ $styleClass }} flex items-center justify-center transition-transform duration-300 hover:scale-105">
            <i class="{{ $icon }} text-lg"></i>
        </div>
    </div>
</div>
