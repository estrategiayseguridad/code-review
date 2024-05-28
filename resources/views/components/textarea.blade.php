@props(['disabled' => false])

<textarea {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'form-control border border-secondary rounded shadow-sm']) !!}></textarea>
