@props(['label', 'icon' => null, 'type' => 'text', 'name'])

<label class="flex flex-col gap-1.5">
    <span class="text-sm font-semibold text-tilawa-ink">{{ $label }}</span>
    <div class="flex items-center gap-2.5 rounded-xl border border-tilawa-line bg-tilawa-surface px-3.5 py-3 focus-within:border-tilawa-teal">
        @if ($icon)
            <x-ui.icon :name="$icon" class="h-[18px] w-[18px] shrink-0 text-tilawa-sub/70" />
        @endif
        <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}"
            {{ $attributes->merge(['class' => 'grow border-none p-0 text-sm text-tilawa-ink placeholder:text-tilawa-sub/60 focus:ring-0']) }}>
        {{ $slot ?? '' }}
    </div>
    @error($name)
        <span class="text-xs font-medium text-red-500">{{ $message }}</span>
    @enderror
</label>
