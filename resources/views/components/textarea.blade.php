@props([
    'disabled' => false,
    'rows' => 3
])

<textarea rows="{{ $rows }}" {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'form-control']) !!}></textarea>
