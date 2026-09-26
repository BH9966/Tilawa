@props(['tagline'])

<div class="relative hidden w-full max-w-[480px] flex-col justify-between overflow-hidden bg-tilawa-teal px-10 py-11 text-white md:flex">
    {{-- Quran-photo background, faded behind the copy --}}
    <div class="absolute inset-0 bg-cover bg-center opacity-30"
        style="background-image: url('{{ asset('images/tilawa-auth-bg.jpg') }}')"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-tilawa-teal/50 via-tilawa-teal/80 to-tilawa-teal"></div>

    <div class="relative flex items-center gap-3">
        <img src="{{ asset('images/tilawa-logo.png') }}" alt="Tilawa" class="h-11 object-contain">
    </div>

    <div class="relative">
        <div class="mb-5 h-[3px] w-16 rounded-full bg-white/60"></div>
        <h2 class="mb-2.5 text-[28px] font-bold leading-tight">{{ $tagline }}</h2>
        <p class="max-w-[340px] text-[15px] leading-relaxed text-white/85">
            Read, reflect and keep track of your daily Qur'an recitation — wherever you are.
        </p>
    </div>

    <div class="relative flex items-center justify-between">
        <span class="text-xs text-white/70">© {{ date('Y') }} Tilawa. All rights reserved.</span>
    </div>
</div>
