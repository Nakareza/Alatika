@props(['title', 'value', 'icon', 'color' => 'blue', 'trend' => null])

@php
    $colorClasses = [
        'blue' => 'from-blue-400 to-blue-500',
        'green' => 'from-emerald-400 to-emerald-500',
        'red' => 'from-rose-400 to-rose-500',
        'purple' => 'from-purple-400 to-purple-500',
        'yellow' => 'from-amber-400 to-amber-500',
        'indigo' => 'from-indigo-400 to-indigo-500',
    ];
    
    $iconBgClasses = [
        'blue' => 'from-blue-50 to-blue-100 text-blue-600',
        'green' => 'from-emerald-50 to-emerald-100 text-emerald-600',
        'red' => 'from-rose-50 to-rose-100 text-rose-600',
        'purple' => 'from-purple-50 to-purple-100 text-purple-600',
        'yellow' => 'from-amber-50 to-amber-100 text-amber-600',
        'indigo' => 'from-indigo-50 to-indigo-100 text-indigo-600',
    ];
@endphp

<div class="relative bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg hover:shadow-xl transition-all duration-500 overflow-hidden group border border-slate-200/50">
    <!-- Subtle gradient overlay on hover -->
    <div class="absolute inset-0 bg-gradient-to-br {{ $colorClasses[$color] }} opacity-0 group-hover:opacity-5 transition-opacity duration-500"></div>
    
    <div class="relative p-6">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-medium text-slate-500 uppercase tracking-wide mb-2">{{ $title }}</p>
                <h3 class="text-4xl font-bold text-slate-800 mb-1">{{ $value }}</h3>
                
                @if($trend)
                    <p class="text-xs text-slate-500 mt-2 flex items-center gap-1">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full {{ $trend > 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                            <i class="fas fa-arrow-{{ $trend > 0 ? 'up' : 'down' }} text-xs"></i>
                            {{ abs($trend) }}%
                        </span>
                        <span>dari bulan lalu</span>
                    </p>
                @endif
            </div>
            
            <div class="w-16 h-16 bg-gradient-to-br {{ $iconBgClasses[$color] }} rounded-2xl flex items-center justify-center text-2xl shadow-md transform group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                <i class="{{ $icon }}"></i>
            </div>
        </div>
    </div>
    
    <!-- Subtle bottom accent -->
    <div class="h-1 bg-gradient-to-r {{ $colorClasses[$color] }} opacity-50"></div>
</div>
