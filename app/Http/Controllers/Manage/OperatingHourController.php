<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\OperatingHour;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OperatingHourController extends Controller
{
    use AuthorizesRequests;

    public function update(Request $request, Venue $venue)
    {
        $this->authorize('update', $venue);

        $validated = $request->validate([
            'hours'                => ['required', 'array'],
            'hours.*.day_of_week'  => ['required', 'integer', 'min:0', 'max:6'],
            'hours.*.is_closed'    => ['boolean'],
            'hours.*.open_time'    => ['required_if:hours.*.is_closed,false,hours.*.is_closed,null', 'nullable', 'date_format:H:i'],
            'hours.*.close_time'   => ['required_if:hours.*.is_closed,false,hours.*.is_closed,null', 'nullable', 'date_format:H:i', 'after:hours.*.open_time'],
        ]);

        foreach ($validated['hours'] as $hourData) {
            $isClosed = isset($hourData['is_closed']) && $hourData['is_closed'];

            $venue->operatingHours()->updateOrCreate(
                ['day_of_week' => $hourData['day_of_week']],
                [
                    'is_closed'  => $isClosed,
                    'open_time'  => $isClosed ? null : $hourData['open_time'],
                    'close_time' => $isClosed ? null : $hourData['close_time'],
                ]
            );
        }

        return back()->with('success', 'Jam operasional berhasil diperbarui.');
    }
}
