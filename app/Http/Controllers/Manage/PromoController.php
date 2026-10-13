<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PromoController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $user  = $request->user();
        $query = Promo::with('venue');

        if ($user->isManager()) {
            $venueIds = $user->venues()->pluck('id');
            $query->where(function ($q) use ($venueIds) {
                $q->whereIn('venue_id', $venueIds)->orWhereNull('venue_id');
            });
        }

        $promos = $query->latest()->paginate(15);

        // Stats for metrics
        $baseQuery = Promo::query();
        if ($user->isManager()) {
            $venueIds = $user->venues()->pluck('id');
            $baseQuery->where(function ($q) use ($venueIds) {
                $q->whereIn('venue_id', $venueIds)->orWhereNull('venue_id');
            });
        }

        $stats = [
            'total'      => (clone $baseQuery)->count(),
            'active'     => (clone $baseQuery)->where('is_active', true)->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })->count(),
            'total_used' => (clone $baseQuery)->sum('used_count'),
        ];

        return view('manage.promos.index', compact('promos', 'stats'));
    }

    public function create(Request $request)
    {
        $user   = $request->user();
        $venues = $user->isAdmin()
            ? Venue::where('is_active', true)->get()
            : $user->venues()->where('is_active', true)->get();

        return view('manage.promos.create', compact('venues'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'venue_id'           => $user->isManager() ? ['required', 'exists:venues,id'] : ['nullable', 'exists:venues,id'],
            'code'               => ['required', 'string', 'max:50', 'unique:promos,code'],
            'description'        => ['required', 'string', 'max:255'],
            'type'               => ['required', 'in:percentage,fixed'],
            'value'              => ['required', 'integer', 'min:1'],
            'max_uses'           => ['nullable', 'integer', 'min:1'],
            'min_booking_amount' => ['nullable', 'integer', 'min:0'],
            'max_discount'       => ['nullable', 'integer', 'min:1'],
            'starts_at'          => ['nullable', 'date'],
            'expires_at'         => ['nullable', 'date', 'after:starts_at'],
            'is_active'          => ['boolean'],
        ]);

        if ($validated['type'] === 'percentage' && $validated['value'] > 100) {
            return back()->withErrors(['value' => 'Persentase tidak boleh lebih dari 100'])->withInput();
        }

        // Manager hanya bisa buat promo untuk venue yang dia kelola
        if ($user->isManager() && isset($validated['venue_id'])) {
            $venue = Venue::findOrFail($validated['venue_id']);
            if ($venue->manager_id !== $user->id) {
                abort(403, 'Venue bukan milik Anda.');
            }
        }

        $validated['code']               = strtoupper($validated['code']);
        $validated['min_booking_amount'] = $validated['min_booking_amount'] ?? 0;
        $validated['is_active']          = $request->boolean('is_active', true);

        Promo::create($validated);

        return redirect()->route('manage.promos.index')->with('success', 'Promo berhasil ditambahkan.');
    }

    public function edit(Promo $promo)
    {
        $user = auth()->user();
        if ($user->isManager() && $promo->venue && $promo->venue->manager_id !== $user->id) {
            abort(403);
        }

        $venues = $user->isAdmin()
            ? Venue::where('is_active', true)->get()
            : $user->venues()->where('is_active', true)->get();

        return view('manage.promos.edit', compact('promo', 'venues'));
    }

    public function update(Request $request, Promo $promo)
    {
        $user = $request->user();
        if ($user->isManager() && $promo->venue && $promo->venue->manager_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'venue_id'           => ['nullable', 'exists:venues,id'],
            'code'               => ['required', 'string', 'max:50', 'unique:promos,code,' . $promo->id],
            'description'        => ['required', 'string', 'max:255'],
            'type'               => ['required', 'in:percentage,fixed'],
            'value'              => ['required', 'integer', 'min:1'],
            'max_uses'           => ['nullable', 'integer', 'min:1'],
            'min_booking_amount' => ['nullable', 'integer', 'min:0'],
            'max_discount'       => ['nullable', 'integer', 'min:1'],
            'starts_at'          => ['nullable', 'date'],
            'expires_at'         => ['nullable', 'date'],
            'is_active'          => ['boolean'],
        ]);

        $validated['code']               = strtoupper($validated['code']);
        $validated['min_booking_amount'] = $validated['min_booking_amount'] ?? 0;
        $validated['is_active']          = $request->boolean('is_active', true);

        $promo->update($validated);

        return redirect()->route('manage.promos.index')->with('success', 'Promo berhasil diperbarui.');
    }

    public function destroy(Promo $promo)
    {
        $user = auth()->user();
        if ($user->isManager() && $promo->venue && $promo->venue->manager_id !== $user->id) {
            abort(403);
        }

        $promo->delete();
        return back()->with('success', 'Promo berhasil dihapus.');
    }
}
