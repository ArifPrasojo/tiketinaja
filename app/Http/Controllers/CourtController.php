<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Services\BookingService;
use Illuminate\Http\Request;

class CourtController extends Controller
{
    public function show(Court $court)
    {
        $court->load(['venue.operatingHours', 'sport']);

        return view('courts.show', compact('court'));
    }

    public function availability(Court $court, Request $request, BookingService $bookingService)
    {
        $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $slots = $bookingService->getAvailableSlots($court, $request->date);

        return response()->json($slots);
    }
}
