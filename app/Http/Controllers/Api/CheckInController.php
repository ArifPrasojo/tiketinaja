<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CheckInController extends Controller
{
    public function __construct(private BookingService $bookingService)
    {
    }

    /**
     * Verify a QR token and perform check-in.
     * Used by manager/admin scanner via mobile app.
     *
     * POST /api/checkin/verify
     */
    public function verify(Request $request)
    {
        $request->validate([
            'qr_token' => ['required', 'string'],
        ]);

        $booking = Booking::where('qr_code', $request->qr_token)
            ->with(['user', 'venue', 'items.court'])
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'QR code tidak valid.',
            ], 404);
        }

        $user = $request->user();
        if ($user->isManager() && $booking->venue->manager_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak berwenang untuk venue ini.',
            ], 403);
        }

        try {
            $result    = $this->bookingService->checkIn($booking);
            $firstItem = $booking->items->first();
            $lastItem  = $booking->items->last();

            return response()->json([
                'success'         => true,
                'message'         => $result['is_play_time']
                    ? 'Check-in berhasil! Waktunya main.'
                    : 'Check-in berhasil! Lapangan siap pukul ' . $result['play_time'],
                'is_play_time'    => $result['is_play_time'],
                'minutes_to_play' => $result['minutes_to_play'],
                'play_time'       => $result['play_time'],
                'booking_code'    => $booking->booking_code,
                'customer'        => $booking->user->name,
                'court'           => $firstItem?->court?->name ?? '-',
                'date'            => $firstItem
                    ? Carbon::parse($firstItem->booking_date)->isoFormat('dddd, D MMMM Y')
                    : '-',
                'time'            => ($firstItem?->start_time ?? '-') . ' – ' . ($lastItem?->end_time ?? '-'),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        }
    }

    /**
     * Customer self check-in via mobile.
     *
     * POST /api/bookings/{booking}/checkin
     */
    public function store(Request $request, Booking $booking)
    {
        if ($booking->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        try {
            $result = $this->bookingService->checkIn($booking);

            return response()->json([
                'success'         => true,
                'message'         => $result['is_play_time']
                    ? 'Check-in berhasil! Waktunya main — segera masuki venue.'
                    : 'Check-in berhasil! Lapangan siap pukul ' . $result['play_time'],
                'is_play_time'    => $result['is_play_time'],
                'minutes_to_play' => $result['minutes_to_play'],
                'booking'         => $booking->fresh(),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        }
    }
}
