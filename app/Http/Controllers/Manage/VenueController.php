<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class VenueController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $query = Venue::withCount('courts');

        if ($request->user()->isManager()) {
            $query->where('manager_id', $request->user()->id);
        }

        $venues = $query->latest()->paginate(10);

        // Stats
        $baseQuery = Venue::query();
        if ($request->user()->isManager()) {
            $baseQuery->where('manager_id', $request->user()->id);
        }

        $stats = [
            'total'        => (clone $baseQuery)->count(),
            'active'       => (clone $baseQuery)->where('is_active', true)->count(),
            'total_courts' => (clone $baseQuery)->withCount('courts')->get()->sum('courts_count'),
        ];

        return view('manage.venues.index', compact('venues', 'stats'));
    }

    public function create()
    {
        return view('manage.venues.create');
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'address'     => ['required', 'string'],
            'city'        => ['required', 'string', 'max:100'],
            'phone'       => ['required', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
            'image'       => ['nullable', 'image', 'max:3072'],
            'is_active'   => ['boolean'],
            'manager_id'  => $user->isAdmin() ? ['nullable', 'exists:users,id'] : [],
        ]);

        // Admin bisa tentukan manager, manager selalu jadi manager venue miliknya
        $validated['manager_id'] = $user->isAdmin() && !empty($validated['manager_id'])
            ? $validated['manager_id']
            : $user->id;

        $validated['slug']      = $this->uniqueSlug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('venues', 'public');
        }

        unset($validated['manager_id']); // remove from mass assignment, set manually below
        $venue = Venue::create(array_merge($validated, [
            'manager_id' => $user->isAdmin() && $request->filled('manager_id')
                ? $request->manager_id
                : $user->id,
        ]));

        return redirect()->route('manage.venues.index')
            ->with('success', 'Venue berhasil ditambahkan.');
    }

    public function edit(Venue $venue)
    {
        $this->authorize('update', $venue);
        $venue->load(['operatingHours', 'courts.sport']);

        return view('manage.venues.edit', compact('venue'));
    }

    public function update(Request $request, Venue $venue)
    {
        $this->authorize('update', $venue);

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'address'     => ['required', 'string'],
            'city'        => ['required', 'string', 'max:100'],
            'phone'       => ['required', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
            'image'       => ['nullable', 'image', 'max:3072'],
            'is_active'   => ['boolean'],
        ]);

        // Regenerate slug only if name changed
        if ($venue->name !== $validated['name']) {
            $validated['slug'] = $this->uniqueSlug($validated['name'], $venue->id);
        }

        $validated['is_active'] = $request->boolean('is_active', $venue->is_active);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($venue->image) {
                Storage::disk('public')->delete($venue->image);
            }
            $validated['image'] = $request->file('image')->store('venues', 'public');
        }

        $venue->update($validated);

        return redirect()->route('manage.venues.edit', $venue)
            ->with('success', 'Venue berhasil diperbarui.');
    }

    public function destroy(Venue $venue)
    {
        $this->authorize('delete', $venue);

        if ($venue->image) {
            Storage::disk('public')->delete($venue->image);
        }

        $venue->delete();

        return redirect()->route('manage.venues.index')
            ->with('success', 'Venue berhasil dihapus.');
    }

    private function uniqueSlug(string $name, ?int $excludeId = null): string
    {
        $base  = Str::slug($name);
        $slug  = $base;
        $i     = 2;

        while (
            Venue::where('slug', $slug)
                ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
                ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
