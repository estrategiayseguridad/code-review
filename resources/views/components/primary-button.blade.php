<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-light']) }}>
    {{ $slot }}
</button>
