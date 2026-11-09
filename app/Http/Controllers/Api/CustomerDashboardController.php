<?php

namespace App\Http\Controllers\Api;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerDashboardController extends Controller
{
    public function __construct(private BookingService $bookingService)
    {
    }

    /**
     * Get mobile home / dashboard data for customer.
     *
     * GET /api/customer/dashboard
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $now  = now();

        // Expire any sessions that ended without check-in for this user
        $pendingChecks = $user->bookings()
            ->where('status', BookingStatus::Confirmed)
            ->where('checked_in', false)
            ->get();
        foreach ($pendingChecks as $b) {
            $this->bookingService->checkAndExpireIfSessionEnded($b);
        }

        // 1. Metrics
        $upcomingBookings = $user->bookings()
            ->where('status', BookingStatus::Confirmed)
            ->whereHas('items', fn ($q) => $q->whereDate('booking_date', '>=', $now->toDateString()))
            ->with(['venue:id,name,city,address,image', 'items.court:id,name,venue_id,sport_id,price_per_hour,image'])
            ->latest()
            ->get();

        $totalSpent = (int) $user->bookings()
            ->whereIn('status', [BookingStatus::Confirmed, BookingStatus::Completed])
            ->sum(DB::raw('total_amount - discount_amount'));

        $totalBookings = $user->bookings()->count();

        // 2. Active Match / "Sedang Bermain" widget
        $activeMatch = null;
        foreach ($upcomingBookings as $b) {
            if ($b->checked_in && $b->items->isNotEmpty()) {
                $firstItem = $b->items->first();
                $lastItem  = $b->items->last();
                $startDt   = Carbon::parse($firstItem->booking_date)->setTimeFromTimeString($firstItem->start_time);
                $endDt     = Carbon::parse($lastItem->booking_date)->setTimeFromTimeString($lastItem->end_time);

                if ($now->gte($startDt) && $now->lt($endDt)) {
                    $activeMatch = [
                        'booking_id'        => $b->id,
                        'booking_code'      => $b->booking_code,
                        'venue_name'        => $b->venue?->name,
                        'court_name'        => $firstItem->court?->name,
                        'date'              => $firstItem->booking_date,
                        'start_time'        => $firstItem->start_time,
                        'end_time'          => $lastItem->end_time,
                        'end_datetime'      => $endDt->toISOString(),
                        'remaining_seconds' => max(0, $now->diffInSeconds($endDt, false)),
                    ];
                    break;
                }
            }
        }

        // 3. Recent bookings (5 items)
        $recentBookings = $user->bookings()
            ->with(['venue:id,name,city,image', 'items.court:id,name,venue_id', 'payment:id,booking_id,payment_code,status,amount,payment_method'])
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'user' => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role'  => $user->role,
                ],
                'stats' => [
                    'upcoming_count' => $upcomingBookings->count(),
                    'total_bookings' => $totalBookings,
                    'total_spent'    => $totalSpent,
                ],
                'active_match'      => $activeMatch,
                'upcoming_bookings' => $upcomingBookings->take(3)->values(),
                'recent_bookings'   => $recentBookings,
            ],
        ]);
    }
}
