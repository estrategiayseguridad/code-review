@props([
    'heading',
    'body',
    'footer'
])

<div {{ $attributes->class(['card']) }}>
    @if ($heading ?? null)
    <div {{ $heading->attributes->class(['card-header text-uppercase']) }}>
        {{ $heading }}
    </div>
    @endif
    <div {{ $body->attributes->class(['card-body p-4 d-grid gap-3']) }}>
        {{ $body }}
    </div>
    @if ($footer ?? null)
    <div {{ $footer->attributes->class(['card-footer']) }}>
        {{ $footer }}
    </div>
    @endif
</div>
