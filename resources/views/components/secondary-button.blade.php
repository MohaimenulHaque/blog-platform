<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-outline btn-md']) }}>
    {{ $slot }}
</button>
