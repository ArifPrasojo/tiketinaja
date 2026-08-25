<?php

namespace App\Http\Controllers;

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
     * Show QR check-in scan page (for managers/admin)
     */
    public function index()
    {
        return view('checkin.index');
    }

    /**
     * Verify QR token and perform check-in.
     * Used by manager/admin scanner page.
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
                'message' => 'QR code tidak valid atau tidak ditemukan.',
            ], 404);
        }

        // Manager scope: hanya bisa scan booking milik venue sendiri
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

            $message = $result['is_play_time']
                ? '✓ Check-in berhasil! Waktunya main — lapangan sudah siap.'
                : '✓ Check-in berhasil! Lapangan siap pukul ' . $result['play_time'] . '.';

            return response()->json([
                'success'          => true,
                'message'          => $message,
                'is_play_time'     => $result['is_play_time'],
                'minutes_to_play'  => $result['minutes_to_play'],
                'play_time'        => $result['play_time'],
                'booking_code'     => $booking->booking_code,
                'customer'         => $booking->user->name,
                'court'            => $firstItem?->court?->name ?? '-',
                'date'             => $firstItem
                    ? Carbon::parse($firstItem->booking_date)->isoFormat('dddd, D MMMM Y')
                    : '-',
                'time'             => ($firstItem?->start_time ?? '-') . ' – ' . ($lastItem?->end_time ?? '-'),
                'venue'            => $booking->venue?->name,
            ]);

        } catch (ValidationException $e) {
            $msg = collect($e->errors())->flatten()->first();
            return response()->json(['success' => false, 'message' => $msg], 422);
        }
    }

    /**
     * Self check-in by customer from booking detail page.
     * POST /checkin/{booking}
     */
    public function store(Request $request, Booking $booking)
    {
        if ($booking->user_id !== $request->user()->id) {
            abort(403);
        }

        try {
            $result = $this->bookingService->checkIn($booking);

            return back()->with(
                'success',
                $result['is_play_time']
                    ? 'Check-in berhasil! Waktunya main — segera masuki venue.'
                    : 'Check-in berhasil! Lapangan siap pukul ' . $result['play_time'] . '. Mohon bersiap.'
            );

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }

    /**
     * Show QR page for a specific booking (customer view).
     * GET /checkin/{booking}
     */
    public function show(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        $booking->load(['items.court', 'venue']);

        // Calculate check-in window info for the view
        $firstItem     = $booking->items->first();
        $checkInInfo   = null;

        if ($firstItem) {
            $playTime       = Carbon::parse($firstItem->booking_date)
                ->setTimeFromTimeString($firstItem->start_time);
            $checkInOpensAt = $playTime->copy()->subMinutes(10);
            $now            = now();

            $checkInInfo = [
                'opens_at'        => $checkInOpensAt,
                'play_time'       => $playTime,
                'is_open'         => $now->gte($checkInOpensAt),
                'is_play_time'    => $now->gte($playTime),
                'minutes_until'   => $now->lt($checkInOpensAt) ? (int) $now->diffInMinutes($checkInOpensAt) : 0,
            ];
        }

        return view('checkin.show', compact('booking', 'checkInInfo'));
    }
}
