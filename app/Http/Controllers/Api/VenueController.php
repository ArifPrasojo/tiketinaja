<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Court;
use App\Models\Sport;
use App\Models\Venue;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    /**
     * List venues with optional filters.
     *
     * GET /api/venues
     */
    public function index(Request $request)
    {
        $query = Venue::withCount('courts')
            ->withAvg('reviews', 'rating')
            ->where('is_active', true);

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('sport')) {
            $query->whereHas('courts', function ($q) use ($request) {
                $q->where('sport_id', $request->sport)->where('is_active', true);
            });
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('city', 'like', '%' . $request->search . '%');
            });
        }

        $venues = $query->paginate(12);

        return response()->json($venues);
    }

    /**
     * Get venue detail with courts and operating hours.
     *
     * GET /api/venues/{venue}
     */
    public function show(Venue $venue)
    {
        $venue->load([
            'courts' => fn($q) => $q->where('is_active', true)->with('sport'),
            'operatingHours',
        ]);

        $venue->loadCount('reviews');
        $venue->loadAvg('reviews', 'rating');

        return response()->json($venue);
    }

    /**
     * Get available time slots for a court on a specific date.
     *
     * GET /api/courts/{court}/availability?date=YYYY-MM-DD
     */
    public function availability(Request $request, Court $court)
    {
        $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $service = app(\App\Services\BookingService::class);
        $slots   = $service->getAvailableSlots($court, $request->date);

        return response()->json([
            'court' => [
                'id'             => $court->id,
                'name'           => $court->name,
                'price_per_hour' => $court->price_per_hour,
            ],
            'date'  => $request->date,
            'slots' => $slots,
        ]);
    }

    /**
     * List all sports.
     *
     * GET /api/sports
     */
    public function sports()
    {
        return response()->json(Sport::all());
    }

    /**
     * List all unique cities.
     *
     * GET /api/cities
     */
    public function cities()
    {
        return response()->json(Venue::where('is_active', true)->distinct()->pluck('city'));
    }

    /**
     * Get reviews for a venue.
     *
     * GET /api/venues/{venue}/reviews
     */
    public function reviews(Venue $venue)
    {
        $reviews = $venue->reviews()
            ->with('user:id,name')
            ->latest()
            ->paginate(10);

        return response()->json($reviews);
    }
}
