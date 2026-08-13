<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-danger btn-md']) }}>
    {{ $slot }}
</button>
