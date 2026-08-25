<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BookingService;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function __construct(private BookingService $bookingService)
    {
    }

    /**
     * Validate a promo code.
     *
     * POST /api/promos/validate
     */
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
            $request->venue_id
        );

        return response()->json($result, $result['valid'] ? 200 : 422);
    }
}
