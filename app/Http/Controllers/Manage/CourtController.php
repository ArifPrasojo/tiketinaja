<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\Court;
use App\Models\Sport;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CourtController extends Controller
{
    use AuthorizesRequests;

    public function create(Venue $venue)
    {
        $this->authorize('update', $venue);
        $sports = Sport::all();

        return view('manage.courts.create', compact('venue', 'sports'));
    }

    public function store(Request $request, Venue $venue)
    {
        $this->authorize('update', $venue);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sport_id' => ['required', 'exists:sports,id'],
            'price_per_hour' => ['required', 'integer', 'min:0'],
            'facilities' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if (isset($validated['facilities'])) {
            $validated['facilities'] = array_map('trim', explode(',', $validated['facilities']));
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('courts', 'public');
        }

        $venue->courts()->create($validated);

        return redirect()->route('manage.venues.edit', $venue)->with('success', 'Lapangan berhasil ditambahkan.');
    }

    public function edit(Court $court)
    {
        $this->authorize('update', $court->venue);
        $sports = Sport::all();

        return view('manage.courts.edit', compact('court', 'sports'));
    }

    public function update(Request $request, Court $court)
    {
        $this->authorize('update', $court->venue);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sport_id' => ['required', 'exists:sports,id'],
            'price_per_hour' => ['required', 'integer', 'min:0'],
            'facilities' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if (isset($validated['facilities'])) {
            $validated['facilities'] = array_map('trim', explode(',', $validated['facilities']));
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('courts', 'public');
        }

        $court->update($validated);

        return redirect()->route('manage.venues.edit', $court->venue)->with('success', 'Lapangan berhasil diperbarui.');
    }

    public function destroy(Court $court)
    {
        $this->authorize('update', $court->venue);
        $court->delete();

        return back()->with('success', 'Lapangan berhasil dihapus.');
    }
}
