<?php

namespace App\Http\Controllers\Api;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use App\Services\BookingService;
use App\Services\MidtransService;
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
        $query = $request->user()
            ->bookings()
            ->with(['venue:id,name,city,address,image', 'items.court', 'payment']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->latest()->paginate(10);

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
    public function show(Request $request, Booking $booking, MidtransService $midtransService)
    {
        if ($booking->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        // Auto-expire check
        $this->bookingService->checkAndExpireIfSessionEnded($booking);
        if ($booking->status === BookingStatus::Pending && $booking->expires_at && $booking->expires_at->isPast()) {
            $this->bookingService->expireSingleBooking($booking);
            $booking->refresh();
        }

        $booking->load(['items.court.sport', 'payment', 'venue', 'review', 'promo']);

        $snapToken = null;
        if ($booking->status === BookingStatus::Pending && $booking->expires_at && $booking->expires_at->isFuture()) {
            $snapToken = $midtransService->getOrCreateSnapToken($booking);
        }

        return response()->json([
            'booking'    => $booking,
            'snap_token' => $snapToken,
            'snap_url'   => $snapToken ? config('midtrans.snap_url') : null,
        ]);
    }

    /**
     * Get Midtrans Snap Token specifically for mobile SDK / WebView.
     *
     * POST /api/bookings/{booking}/payment-token
     */
    public function paymentToken(Request $request, Booking $booking, MidtransService $midtransService)
    {
        if ($booking->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        if ($booking->status !== BookingStatus::Pending) {
            return response()->json(['message' => 'Booking tidak dalam status pending.'], 422);
        }

        if ($booking->expires_at && $booking->expires_at->isPast()) {
            $this->bookingService->expireSingleBooking($booking);
            return response()->json(['message' => 'Waktu pembayaran sudah habis.'], 422);
        }

        $token = $midtransService->getOrCreateSnapToken($booking);

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat token pembayaran Midtrans.',
            ], 500);
        }

        return response()->json([
            'success'      => true,
            'snap_token'   => $token,
            'client_key'   => config('midtrans.client_key'),
            'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/' . $token,
            'expires_at'   => $booking->expires_at?->toISOString(),
        ]);
    }

    /**
     * Callback from mobile after Midtrans payment completion.
     *
     * POST /api/bookings/{booking}/payment-finish
     */
    public function finishPayment(Request $request, Booking $booking)
    {
        if ($booking->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        if ($booking->expires_at && $booking->expires_at->isPast()) {
            if ($booking->status === BookingStatus::Pending) {
                $this->bookingService->expireSingleBooking($booking);
            }
            return response()->json([
                'success' => false,
                'message' => 'Waktu pembayaran 15 menit telah habis. Booking kedaluwarsa.',
            ], 422);
        }

        $payload = $request->all();
        $status  = $payload['transaction_status'] ?? $payload['status'] ?? '';

        if (in_array($status, ['settlement', 'capture', 'success'])) {
            $paymentType = $payload['payment_type'] ?? 'midtrans';
            $this->bookingService->processPayment($booking, $paymentType, $payload);

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dikonfirmasi!',
                'booking' => $booking->fresh()->load(['items.court', 'payment']),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Pembayaran sedang diproses atau belum selesai.',
            'booking' => $booking->fresh(),
        ]);
    }

    /**
     * Process payment (mock / simulation).
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
            $this->bookingService->expireSingleBooking($booking);
            return response()->json(['message' => 'Waktu pembayaran sudah habis.'], 422);
        }
        try {
            $this->bookingService->processPayment($booking, 'mock_api');
            return response()->json(['message' => 'Pembayaran berhasil.', 'booking' => $booking->fresh()->load(['items.court', 'payment'])]);
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

    /**
     * Delete review for a booking.
     *
     * DELETE /api/bookings/{booking}/reviews
     */
    public function destroyReview(Request $request, Booking $booking)
    {
        if ($booking->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        $review = $booking->review;
        if (!$review) {
            return response()->json(['message' => 'Ulasan tidak ditemukan.'], 404);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ulasan berhasil dihapus.',
        ]);
    }
}
