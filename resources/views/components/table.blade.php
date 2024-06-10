@props([
    'head',
    'body',
    'foot'
])

<div class="table-responsive">
    <table {{ $attributes->class(['table mb-0']) }}>
        @if ($head ?? null)
        <thead {{ $head->attributes->class(['']) }}>
            {{ $head }}
        </thead>
        @endif
        <tbody {{ $body->attributes->class(['']) }}>
            {{ $body }}
        </tbody>
        @if ($foot ?? null)
        <tfoot {{ $foot->attributes->class(['']) }}>
            {{ $foot }}
        </tfoot>
        @endif
    </table>
</div>
