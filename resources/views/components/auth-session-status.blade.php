@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm bg-blue-50 border border-electric-blue/30 text-electric-blue rounded-xl px-4 py-3']) }}>
        {{ $status }}
    </div>
@endif