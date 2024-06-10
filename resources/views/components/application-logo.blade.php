@props(['size' => ''])

<img src="{{ asset('images/logo' . ($size ? '-' . $size : '') . '.png') }}" class="img-fluid" alt="ES Consulting">
