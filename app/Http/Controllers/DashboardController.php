<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Court;
use App\Models\Venue;
use App\Models\User;
use App\Models\ActivityLog;
use App\Enums\BookingStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            return $this->adminDashboard();
        }

        if ($user->role === 'manager') {
            return $this->managerDashboard($user);
        }

        return $this->customerDashboard($user);
    }

    private function customerDashboard(User $user)
    {
        $upcomingBookings = $user->bookings()
            ->where('status', BookingStatus::Confirmed)
            ->whereHas('items', fn($q) => $q->whereDate('booking_date', '>=', today()))
            ->with(['venue', 'items.court'])
            ->latest()
            ->get();

        $recentBookings = $user->bookings()
            ->with(['venue', 'payment', 'items.court'])
            ->latest()
            ->paginate(5);

        $totalSpent = $user->bookings()
            ->where('status', BookingStatus::Confirmed)
            ->sum(DB::raw('total_amount - discount_amount'));

        $totalBookings = $user->bookings()->count();

        $activeVouchers = 0; // placeholder for future voucher wallet

        return view('dashboard', compact(
            'upcomingBookings',
            'recentBookings',
            'totalSpent',
            'totalBookings',
            'activeVouchers'
        ));
    }

    private function managerDashboard(User $user)
    {
        $venueIds = $user->venues()->pluck('id');

        $todayBookings = Booking::whereIn('venue_id', $venueIds)
            ->whereHas('items', fn($q) => $q->whereDate('booking_date', today()))
            ->count();

        $todayBookingList = Booking::whereIn('venue_id', $venueIds)
            ->whereHas('items', fn($q) => $q->whereDate('booking_date', today()))
            ->with(['user', 'items.court', 'payment'])
            ->latest()
            ->get();

        $totalRevenue = Booking::whereIn('venue_id', $venueIds)
            ->where('status', BookingStatus::Confirmed)
            ->sum(DB::raw('total_amount - discount_amount'));

        $bookingCount = Booking::whereIn('venue_id', $venueIds)->count();

        // Most popular court
        $popularCourt = Court::whereIn('venue_id', $venueIds)
            ->withCount(['bookingItems as total_bookings' => fn($q) => $q->whereHas(
                'booking', fn($bq) => $bq->where('status', BookingStatus::Confirmed)
            )])
            ->orderByDesc('total_bookings')
            ->first();

        // Peak hours — top 5 start times by booking count
        $peakHours = BookingItem::whereHas(
            'booking', fn($q) => $q->whereIn('venue_id', $venueIds)
                ->where('status', BookingStatus::Confirmed)
        )
            ->select('start_time', DB::raw('count(*) as count'))
            ->groupBy('start_time')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Monthly revenue (last 6 months) — DB-agnostic
        $monthlyRevenue = Booking::whereIn('venue_id', $venueIds)
            ->where('status', BookingStatus::Confirmed)
            ->where('created_at', '>=', now()->subMonths(6))
            ->get(['created_at', 'total_amount', 'discount_amount'])
            ->groupBy(fn($b) => $b->created_at->format('Y-m'))
            ->map(fn($group, $key) => [
                'label'   => $group->first()->created_at->translatedFormat('M Y'),
                'revenue' => $group->sum(fn($b) => $b->total_amount - $b->discount_amount),
            ])
            ->values();

        return view('dashboard', compact(
            'todayBookings',
            'todayBookingList',
            'totalRevenue',
            'bookingCount',
            'popularCourt',
            'peakHours',
            'monthlyRevenue'
        ));
    }

    private function adminDashboard()
    {
        $totalUsers    = User::count();
        $totalVenues   = Venue::count();
        $totalBookings = Booking::count();
        $totalRevenue  = Booking::where('status', BookingStatus::Confirmed)
            ->sum(DB::raw('total_amount - discount_amount'));

        // Booking status breakdown
        $bookingsByStatus = Booking::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        // Recent activity log
        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->take(15)
            ->get();

        // Monthly revenue (last 6 months) — DB-agnostic
        $monthlyRevenue = Booking::where('status', BookingStatus::Confirmed)
            ->where('created_at', '>=', now()->subMonths(6))
            ->get(['created_at', 'total_amount', 'discount_amount'])
            ->groupBy(fn($b) => $b->created_at->format('Y-m'))
            ->map(fn($group) => [
                'label'   => $group->first()->created_at->translatedFormat('M Y'),
                'revenue' => $group->sum(fn($b) => $b->total_amount - $b->discount_amount),
            ])
            ->values();

        // Top venues by revenue
        $topVenues = Venue::withSum(
            ['bookings as revenue' => fn($q) => $q->where('status', BookingStatus::Confirmed)],
            DB::raw('total_amount - discount_amount')
        )
            ->orderByDesc('revenue')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalUsers',
            'totalVenues',
            'totalBookings',
            'totalRevenue',
            'bookingsByStatus',
            'recentActivities',
            'monthlyRevenue',
            'topVenues'
        ));
    }
}
