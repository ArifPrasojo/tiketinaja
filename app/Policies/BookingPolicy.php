<?php

namespace App\Policies;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id
            || $user->role === 'admin'
            || ($user->role === 'manager' && $booking->venue->manager_id === $user->id);
    }

    public function pay(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id
            && $booking->status === BookingStatus::Pending
            && ($booking->expires_at === null || $booking->expires_at->isFuture());
    }

    public function cancel(User $user, Booking $booking): bool
    {
        $cancellableStatuses = [BookingStatus::Pending, BookingStatus::Confirmed];

        if ($user->role === 'admin' || ($user->role === 'manager' && $booking->venue->manager_id === $user->id)) {
            return in_array($booking->status, $cancellableStatuses);
        }

        return $user->id === $booking->user_id
            && in_array($booking->status, $cancellableStatuses);
    }
}
