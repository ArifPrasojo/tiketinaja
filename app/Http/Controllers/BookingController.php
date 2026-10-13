<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Court;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BookingController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, BookingService $bookingService)
    {
        $validated = $request->validate([
            'court_id'     => ['required', 'exists:courts,id'],
            'date'         => ['required', 'date', 'after_or_equal:today'],
            'time_slots'   => ['required', 'array', 'min:1'],
            'time_slots.*' => ['string', 'regex:/^\d{2}:\d{2}$/'],
            'promo_code'   => ['nullable', 'string', 'max:50'],
        ]);

        try {
            $court   = Court::findOrFail($validated['court_id']);
            $booking = $bookingService->createBooking(
                $request->user(),
                $court,
                $validated['date'],
                $validated['time_slots'],
                $validated['promo_code'] ?? null
            );

            return redirect()->route('bookings.show', $booking)
                ->with('success', 'Booking berhasil dibuat. Selesaikan pembayaran sebelum waktu habis.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function show(Booking $booking, \App\Services\MidtransService $midtransService)
    {
        $this->authorize('view', $booking);

        $booking->load(['items.court.sport', 'payment', 'venue', 'review', 'promo']);

        $snapToken = null;
        if ($booking->status === \App\Enums\BookingStatus::Pending && auth()->id() === $booking->user_id) {
            $snapToken = $midtransService->getOrCreateSnapToken($booking);
        }

        return view('bookings.show', compact('booking', 'snapToken'));
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $query = $user->bookings()->with(['venue', 'payment', 'items.court']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->latest()->paginate(10)->withQueryString();

        $stats = [
            'total'     => $user->bookings()->count(),
            'pending'   => $user->bookings()->where('status', \App\Enums\BookingStatus::Pending)->count(),
            'confirmed' => $user->bookings()->where('status', \App\Enums\BookingStatus::Confirmed)->count(),
            'completed' => $user->bookings()->where('status', \App\Enums\BookingStatus::Completed)->count(),
        ];

        return view('bookings.index', compact('bookings', 'stats'));
    }

    public function pay(Booking $booking, BookingService $bookingService)
    {
        $this->authorize('pay', $booking);

        // Guard: booking mungkin sudah expired antara user klik dan submit
        if ($booking->expires_at && $booking->expires_at->isPast()) {
            return back()->with('error', 'Waktu pembayaran sudah habis. Silakan buat booking baru.');
        }

        $bookingService->processPayment($booking);

        return back()->with('success', 'Pembayaran berhasil dikonfirmasi. Booking Anda sudah aktif!');
    }

    public function cancel(Booking $booking, BookingService $bookingService)
    {
        $this->authorize('cancel', $booking);

        try {
            $bookingService->cancelBooking($booking);
            return back()->with('success', 'Booking berhasil dibatalkan.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membatalkan booking.');
        }
    }
}
