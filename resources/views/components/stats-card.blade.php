@props([
    'title',
    'value',
    'icon' => null,
    'trend' => null,
    'trendDirection' => 'up',
    'color' => 'blue'
])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6']) }}>
    <div class="flex items-center justify-between">
        <div class="flex-1">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400 truncate">
                {{ $title }}
            </p>
            <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">
                {{ $value }}
            </p>
        </div>
        
        @if($icon)
        <div class="flex-shrink-0">
            <div class="p-3 rounded-full bg-{{ $color }}-100 dark:bg-{{ $color }}-900">
                <svg class="w-8 h-8 text-{{ $color }}-600 dark:text-{{ $color }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {!! $icon !!}
                </svg>
            </div>
        </div>
        @endif
    </div>
    
    @if($trend)
    <div class="mt-4 flex items-center text-sm">
        @if($trendDirection === 'up')
        <svg class="w-4 h-4 text-green-500 dark:text-green-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
        </svg>
        <span class="text-green-600 dark:text-green-400 font-medium">{{ $trend }}</span>
        @elseif($trendDirection === 'down')
        <svg class="w-4 h-4 text-red-500 dark:text-red-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
        </svg>
        <span class="text-red-600 dark:text-red-400 font-medium">{{ $trend }}</span>
        @else
        <span class="text-gray-600 dark:text-gray-400 font-medium">{{ $trend }}</span>
        @endif
        <span class="ml-2 text-gray-500 dark:text-gray-400">{{ $slot }}</span>
    </div>
    @endif
</div>
