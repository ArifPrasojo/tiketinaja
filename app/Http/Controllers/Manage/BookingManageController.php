<?php

namespace App\Http\Controllers\Manage;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BookingManageController extends Controller
{
    public function __construct(private BookingService $bookingService)
    {
    }

    public function index(Request $request)
    {
        $user  = auth()->user();
        $query = Booking::with(['user', 'venue', 'payment', 'items.court']);

        if ($user->isManager()) {
            $query->whereHas('venue', fn($q) => $q->where('manager_id', $user->id));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('booking_code', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', '%' . $request->search . '%'));
            });
        }

        $bookings = $query->latest()->paginate(15)->withQueryString();

        // Stats for header
        $baseQuery = Booking::query();
        if ($user->isManager()) {
            $baseQuery->whereHas('venue', fn($q) => $q->where('manager_id', $user->id));
        }
        $stats = [
            'total'     => (clone $baseQuery)->count(),
            'pending'   => (clone $baseQuery)->where('status', BookingStatus::Pending)->count(),
            'confirmed' => (clone $baseQuery)->where('status', BookingStatus::Confirmed)->count(),
            'today'     => (clone $baseQuery)->whereHas('items', fn($q) => $q->whereDate('booking_date', today()))->count(),
        ];

        return view('manage.bookings.index', compact('bookings', 'stats'));
    }

    public function show(Booking $booking)
    {
        $user = auth()->user();

        // Scope check for manager
        if ($user->isManager() && $booking->venue->manager_id !== $user->id) {
            abort(403);
        }

        $booking->load(['user', 'venue', 'payment', 'items.court.sport', 'review', 'promo']);

        return view('manage.bookings.show', compact('booking'));
    }

    public function confirm(Booking $booking)
    {
        $user = auth()->user();

        if ($user->isManager() && $booking->venue->manager_id !== $user->id) {
            abort(403);
        }

        if ($booking->status !== BookingStatus::Pending) {
            return back()->with('error', 'Hanya booking pending yang bisa dikonfirmasi.');
        }

        try {
            $this->bookingService->processPayment($booking);
            return back()->with('success', 'Booking berhasil dikonfirmasi.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengkonfirmasi booking.');
        }
    }

    public function cancel(Booking $booking)
    {
        $user = auth()->user();

        if ($user->isManager() && $booking->venue->manager_id !== $user->id) {
            abort(403);
        }

        try {
            $this->bookingService->cancelBooking($booking);
            return back()->with('success', 'Booking berhasil dibatalkan.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }
}
