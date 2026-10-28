@props(['label' => ''])

@php
    $label = trim(preg_replace('/\s+/', ' ', strip_tags((string) $label)));
@endphp

<div class="sidebar-hide-rail px-3 pt-4 pb-1 mb-1">
    <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ $label }}</span>
</div>
