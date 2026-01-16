@props(['value' => 0, 'currency' => '$', 'decimals' => 2])

<span {{ $attributes->merge(['class' => 'text-gray-900 dark:text-white']) }}>
    {{ $currency }}{{ number_format($value, $decimals) }}
</span>
