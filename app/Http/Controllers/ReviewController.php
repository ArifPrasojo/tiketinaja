<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use App\Enums\BookingStatus;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReviewController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, Booking $booking)
    {
        // Only the booking owner can review
        if ($booking->user_id !== $request->user()->id) {
            abort(403);
        }

        // Only completed bookings can be reviewed
        if ($booking->status !== BookingStatus::Completed) {
            return back()->with('error', 'Ulasan hanya bisa dikirim setelah sesi bermain selesai.');
        }

        // One review per booking
        if ($booking->review()->exists()) {
            return back()->with('error', 'Booking ini sudah diberi ulasan.');
        }

        $validated = $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        Review::create([
            'booking_id' => $booking->id,
            'user_id'    => $request->user()->id,
            'venue_id'   => $booking->venue_id,
            'rating'     => $validated['rating'],
            'comment'    => $validated['comment'] ?? null,
        ]);

        return back()->with('success', 'Ulasan berhasil dikirim. Terima kasih!');
    }

    public function destroy(Review $review)
    {
        // Only admin or owner can delete
        $user = auth()->user();
        if ($review->user_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        $review->delete();
        return back()->with('success', 'Ulasan berhasil dihapus.');
    }
}
