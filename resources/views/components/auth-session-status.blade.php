@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'fw-bold text-sucess']) }}>
        {{ $status }}
    </div>
@endif
