@props(['number', 'class' => 'h-10 w-10', 'textClass' => 'text-sm'])

@php
    $arabicDigits = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    $arabicNumber = collect(str_split((string) $number))
        ->map(fn ($digit) => $arabicDigits[(int) $digit])
        ->implode('');
@endphp

<span {{ $attributes->merge(['class' => "$class inline-flex shrink-0 items-center justify-center bg-contain bg-center bg-no-repeat align-middle"]) }}
    style="background-image: url('{{ asset('images/ayah-marker.png') }}')">
    <span class="font-arabic {{ $textClass }} font-bold leading-none text-tilawa-teal-dark">{{ $arabicNumber }}</span>
</span>
