@props(['on'])

<div x-data="{ shown: false, timeout: null }"
     x-init="@this.on('{{ $on }}', () => { clearTimeout(timeout); shown = true; timeout = setTimeout(() => { shown = false }, 2000); })"
     x-show.transition.out.opacity.duration.3000ms="shown"
     x-transition:leave.opacity.duration.3000ms
     style="display: none;"
    {{ $attributes->merge(['class' => 'small text-secondary']) }}>
    {{ $slot->isEmpty() ? __('Saved.') : $slot }}
</div>
