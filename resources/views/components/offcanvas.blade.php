<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvas">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">{{ __('Jobs') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        {{ $slot }}
    </div>
</div>