@php
    /** @var \App\Models\Booking $booking */
    $status = $booking->status instanceof \App\Enums\BookingStatus
        ? $booking->status
        : \App\Enums\BookingStatus::tryFrom((string) $booking->status);

    $label    = $status?->labelFor($booking)  ?? ucfirst($booking->status ?? '-');
    $badgeCls = $status?->badgeClassFor($booking) ?? 'bg-gray-100 text-gray-600';

    // Extra pulse for "sedang bermain"
    $isOngoing = $status === BookingStatus::Confirmed
        && $booking->checked_in
        && $booking->items->count() > 0;
@endphp

<span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full {{ $badgeCls }} {{ $class ?? '' }}">
    @if($isOngoing)
        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
    @endif
    {{ $label }}
</span>
