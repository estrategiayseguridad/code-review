@props(['value'])

<label {{ $attributes->merge(['class' => 'form-label text-body']) }}>
    {{ $value ?? $slot }}
</label>
