@props(['variant' => 'primary', 'type' => 'submit'])

@php
    $variants = [
        'primary' => 'bg-tilawa-teal hover:bg-tilawa-teal-dark text-white',
        'ghost' => 'bg-white/15 hover:bg-white/25 text-white',
    ];
    $variantClass = $variants[$variant] ?? $variants['primary'];
@endphp

<button type="{{ $type }}"
    {{ $attributes->merge(['class' => "flex items-center justify-center gap-2 rounded-xl px-6 py-3.5 text-sm font-bold transition disabled:cursor-not-allowed disabled:opacity-60 $variantClass"]) }}>
    {{ $slot }}
</button>
