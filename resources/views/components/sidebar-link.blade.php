@props([
    'active' => false,
    'href' => '#',
])

@php
    // In the collapsed rail the label is hidden via CSS, so it doubles as a tooltip.
    $label = trim(preg_replace('/\s+/', ' ', strip_tags((string) $slot)));

    $state = ($active ?? false)
        ? 'bg-indigo-50 text-indigo-700'
        : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900';
@endphp

<a
    href="{{ $href }}"
    {{ $attributes->merge([
        'class' => 'sidebar-rail-center group relative flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1 '.$state,
    ]) }}
    :title="collapsed ? {{ Illuminate\Support\Js::from($label) }} : null"
>
    @isset($icon)
        <span class="shrink-0 [&>svg]:h-5 [&>svg]:w-5" aria-hidden="true">{{ $icon }}</span>
    @endisset

    <span class="sidebar-hide-rail truncate">{{ $slot }}</span>
</a>