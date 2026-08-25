<?php

namespace App\Http\Controllers;

use App\Services\BookingService;
use Illuminate\Http\Request;

class PromoValidateController extends Controller
{
    public function __construct(private BookingService $bookingService)
    {
    }

    public function validate(Request $request)
    {
        $request->validate([
            'code'     => ['required', 'string'],
            'amount'   => ['required', 'integer', 'min:1'],
            'venue_id' => ['required', 'exists:venues,id'],
        ]);

        $result = $this->bookingService->validatePromo(
            $request->code,
            $request->amount,
            $request->venue_id,
            $request->user()?->id   // pass user ID untuk cek per-user usage
        );

        return response()->json($result);
    }
}
