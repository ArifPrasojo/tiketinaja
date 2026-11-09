<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use App\Services\BookingService;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function __construct(private BookingService $bookingService)
    {
    }

    /**
     * List active promos available for customers.
     *
     * GET /api/promos
     */
    public function index(Request $request)
    {
        $now = now();
        $query = Promo::with('venue:id,name,city')
            ->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', $now);
            })
            ->where(function ($q) {
                $q->whereNull('max_uses')->orWhereRaw('used_count < max_uses');
            });

        if ($request->filled('venue_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('venue_id', $request->venue_id)->orWhereNull('venue_id');
            });
        }

        $promos = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data'    => $promos,
        ]);
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
            $request->venue_id,
            $request->user()?->id
        );

        return response()->json($result, $result['valid'] ? 200 : 422);
    }
}
