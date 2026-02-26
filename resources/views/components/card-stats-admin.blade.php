{{-- Card Statistik untuk Admin Dashboard --}}
@props(['title', 'value', 'icon', 'color' => 'blue', 'trend' => null])

@php
$colorClasses = [
    'blue' => 'from-blue-500 to-indigo-600',
    'green' => 'from-emerald-500 to-teal-600',
    'red' => 'from-rose-500 to-red-600',
    'purple' => 'from-purple-500 to-indigo-600',
    'yellow' => 'from-amber-500 to-orange-600',
    'indigo' => 'from-indigo-500 to-purple-600',
    'orange' => 'from-orange-500 to-amber-600',
];
$gradientClass = $colorClasses[$color] ?? $colorClasses['blue'];
@endphp

<div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 border border-slate-100">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <p class="text-sm font-medium text-slate-600 mb-1">{{ $title }}</p>
            <h3 class="text-3xl font-bold text-slate-800 mb-2">{{ $value }}</h3>
            
            @if($trend)
            <div class="flex items-center gap-1 text-xs">
                @if($trend > 0)
                    <i class="fas fa-arrow-up text-emerald-500"></i>
                    <span class="text-emerald-500 font-semibold">+{{ $trend }}%</span>
                @else
                    <i class="fas fa-arrow-down text-rose-500"></i>
                    <span class="text-rose-500 font-semibold">{{ $trend }}%</span>
                @endif
                <span class="text-slate-500 ml-1">dari bulan lalu</span>
            </div>
            @endif
        </div>
        
        <div class="w-14 h-14 rounded-xl bg-gradient-to-br {{ $gradientClass }} flex items-center justify-center shadow-lg transition-transform duration-300 hover:scale-110 hover:rotate-6">
            <i class="{{ $icon }} text-2xl text-white"></i>
        </div>
    </div>
</div>
