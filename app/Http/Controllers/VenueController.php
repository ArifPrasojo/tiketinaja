<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use App\Models\Sport;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    public function index(Request $request)
    {
        $query = Venue::withCount('courts')->withAvg('reviews', 'rating')->where('is_active', true);

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
        $cities = Venue::where('is_active', true)->distinct()->pluck('city');
        $sports = Sport::all();

        return view('venues.index', compact('venues', 'cities', 'sports'));
    }

    public function show(Venue $venue)
    {
        $venue->load([
            'courts' => function ($q) {
                $q->where('is_active', true)->with('sport');
            },
            'operatingHours'
        ]);

        return view('venues.show', compact('venue'));
    }
}
