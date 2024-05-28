@props(['active'])

@php
$classes = ($active ?? false)
            ? 'active'
            : '';
@endphp

<li class="nav-item">
    <a {{ $attributes->merge(['class' => 'nav-link ' . $classes]) }}>
        {{ $slot }}
    </a>
</li>
