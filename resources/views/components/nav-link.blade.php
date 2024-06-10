@props(['active'])

@php
$classes = ($active ?? false)
            ? 'link-light active'
            : 'link-secondary';
@endphp

<li class="nav-item">
    <a {{ $attributes->merge(['class' => 'nav-link ' . $classes]) }}>
        {{ $slot }}
    </a>
</li>
