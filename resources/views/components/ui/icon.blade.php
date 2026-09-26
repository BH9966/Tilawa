@props(['name', 'class' => 'h-[18px] w-[18px]'])

@php
    $paths = [
        'mail' => '<rect x="2.5" y="4.5" width="19" height="15" rx="2"/><path d="M3 6l9 6 9-6"/>',
        'lock' => '<rect x="4" y="10" width="16" height="10" rx="2"/><path d="M7 10V7a5 5 0 0 1 10 0v3"/>',
        'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 4-6.5 8-6.5s8 2.5 8 6.5"/>',
        'eye' => '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/>',
        'arrow-right' => '<path d="M5 12h14"/><path d="M12 5l7 7-7 7"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/>',
        'chevron-down' => '<path d="M6 9l6 6 6-6"/>',
        'book' => '<path d="M3 5.5A2.5 2.5 0 0 1 5.5 3H12v18H5.5A2.5 2.5 0 0 1 3 18.5v-13z"/><path d="M21 5.5A2.5 2.5 0 0 0 18.5 3H12v18h6.5a2.5 2.5 0 0 0 2.5-2.5v-13z"/>',
        'grid' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
        'list' => '<path d="M8 6h13"/><path d="M8 12h13"/><path d="M8 18h13"/><circle cx="3.5" cy="6" r="1.2" fill="currentColor" stroke="none"/><circle cx="3.5" cy="12" r="1.2" fill="currentColor" stroke="none"/><circle cx="3.5" cy="18" r="1.2" fill="currentColor" stroke="none"/>',
        'sort' => '<path d="M6 4v16"/><path d="M3 7l3-3 3 3"/><path d="M18 20V4"/><path d="M21 17l-3 3-3-3"/>',
        'arrow-left' => '<path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/>',
        'copy' => '<rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>',
        'share' => '<circle cx="18" cy="5" r="2.5"/><circle cx="6" cy="12" r="2.5"/><circle cx="18" cy="19" r="2.5"/><path d="M8.3 10.7l7.4-4.4"/><path d="M8.3 13.3l7.4 4.4"/>',
        'bookmark' => '<path d="M6 4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v17l-6-4-6 4V4z"/>',
        'bookmark-filled' => '<path d="M6 4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v17l-6-4-6 4V4z" fill="currentColor" stroke="none"/>',
        'trash' => '<path d="M4 7h16"/><path d="M9 7V4.5A1.5 1.5 0 0 1 10.5 3h3A1.5 1.5 0 0 1 15 4.5V7"/><path d="M6 7l1 13a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-13"/><path d="M10 11v6"/><path d="M14 11v6"/>',
        'check-circle' => '<circle cx="12" cy="12" r="9"/><path d="M8.5 12.5l2.3 2.3 4.7-5.1"/>',
        'alert-triangle' => '<path d="M12 3.5L2.5 20h19L12 3.5z"/><path d="M12 10v4.5"/><circle cx="12" cy="17.5" r="0.9" fill="currentColor" stroke="none"/>',
        'refresh' => '<path d="M3 12a9 9 0 0 1 15.3-6.3L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-15.3 6.3L3 16"/><path d="M3 21v-5h5"/>',
        'globe' => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18"/><path d="M12 3c2.4 2.5 3.8 5.7 3.8 9s-1.4 6.5-3.8 9c-2.4-2.5-3.8-5.7-3.8-9s1.4-6.5 3.8-9z"/>',
        'close' => '<path d="M6 6l12 12"/><path d="M18 6L6 18"/>',
        'palette' => '<path d="M12 21a9 9 0 0 1 0-18c4 0 7 2.5 7 5.8 0 2-1.6 3.4-3.4 3.4h-1.3c-.9 0-1.3.7-1.3 1.3 0 .4.2.7.4 1 .2.3.4.6.4 1A2.5 2.5 0 0 1 12 21z"/><circle cx="7.5" cy="11" r="1.1" fill="currentColor" stroke="none"/><circle cx="9.5" cy="7.3" r="1.1" fill="currentColor" stroke="none"/><circle cx="14.5" cy="7.3" r="1.1" fill="currentColor" stroke="none"/>',
        'sun' => '<circle cx="12" cy="12" r="4.2"/><path d="M12 2.5v2.4"/><path d="M12 19.1v2.4"/><path d="M4.2 4.2l1.7 1.7"/><path d="M18.1 18.1l1.7 1.7"/><path d="M2.5 12h2.4"/><path d="M19.1 12h2.4"/><path d="M4.2 19.8l1.7-1.7"/><path d="M18.1 5.9l1.7-1.7"/>',
        'moon' => '<path d="M20.8 14.3a9 9 0 1 1-9.1-13c.3 0 .5.3.4.6a7.3 7.3 0 0 0 9.1 9.1c.3-.1.6.1.6.4a9 9 0 0 1-1 2.9z"/>',
    ];
@endphp

<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
    stroke-linecap="round" stroke-linejoin="round" {{ $attributes->merge(['class' => $class]) }}>
    {!! $paths[$name] ?? '' !!}
</svg>
