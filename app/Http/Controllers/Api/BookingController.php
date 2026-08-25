<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function __construct(private BookingService $bookingService)
    {
    }

    /**
     * List authenticated user's bookings.
     *
     * GET /api/bookings
     */
    public function index(Request $request)
    {
        $bookings = $request->user()
            ->bookings()
            ->with(['venue:id,name,city,image', 'items.court', 'payment'])
            ->latest()
            ->paginate(10);

        return response()->json($bookings);
    }

    /**
     * Create a new booking.
     *
     * POST /api/bookings
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'court_id'   => ['required', 'exists:courts,id'],
            'date'       => ['required', 'date', 'after_or_equal:today'],
            'time_slots' => ['required', 'array', 'min:1'],
            'time_slots.*' => ['string', 'regex:/^\d{2}:\d{2}$/'],
            'promo_code' => ['nullable', 'string'],
        ]);

        try {
            $court   = Court::findOrFail($validated['court_id']);
            $booking = $this->bookingService->createBooking(
                $request->user(),
                $court,
                $validated['date'],
                $validated['time_slots'],
                $validated['promo_code'] ?? null
            );

            return response()->json([
                'message' => 'Booking berhasil dibuat.',
                'booking' => $booking->load(['items.court', 'payment', 'venue']),
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Get booking detail.
     *
     * GET /api/bookings/{booking}
     */
    public function show(Request $request, Booking $booking)
    {
        if ($booking->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        return response()->json(
            $booking->load(['items.court', 'payment', 'venue', 'review'])
        );
    }

    /**
     * Process payment (mock).
     *
     * POST /api/bookings/{booking}/pay
     */
    public function pay(Request $request, Booking $booking)
    {
        if ($booking->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        // Guard: status & expiry check
        if ($booking->status !== \App\Enums\BookingStatus::Pending) {
            return response()->json(['message' => 'Booking tidak dalam status pending.'], 422);
        }

        if ($booking->expires_at && $booking->expires_at->isPast()) {
            return response()->json(['message' => 'Waktu pembayaran sudah habis.'], 422);
        }
        try {
            $this->bookingService->processPayment($booking);
            return response()->json(['message' => 'Pembayaran berhasil.', 'booking' => $booking->fresh()]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal memproses pembayaran.'], 500);
        }
    }

    /**
     * Cancel a booking.
     *
     * POST /api/bookings/{booking}/cancel
     */
    public function cancel(Request $request, Booking $booking)
    {
        if ($booking->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        try {
            $this->bookingService->cancelBooking($booking);
            return response()->json(['message' => 'Booking berhasil dibatalkan.']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Submit a review for a completed booking.
     *
     * POST /api/bookings/{booking}/reviews
     */
    public function storeReview(Request $request, Booking $booking)
    {
        if ($booking->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        if ($booking->status !== \App\Enums\BookingStatus::Completed) {
            return response()->json(['message' => 'Ulasan hanya bisa dikirim setelah sesi bermain selesai.'], 422);
        }

        if ($booking->review()->exists()) {
            return response()->json(['message' => 'Booking sudah diberi ulasan.'], 422);
        }

        $validated = $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $review = $booking->review()->create([
            'user_id'  => $request->user()->id,
            'venue_id' => $booking->venue_id,
            ...$validated,
        ]);

        return response()->json(['message' => 'Ulasan berhasil dikirim.', 'review' => $review], 201);
    }
}
