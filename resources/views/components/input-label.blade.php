@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-pitch-black']) }}>
    {{ $value ?? $slot }}
</label>
