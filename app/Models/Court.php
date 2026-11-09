<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Court extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'venue_id', 'sport_id', 'name', 'description',
        'facilities', 'image', 'price_per_hour', 'is_active'
    ];

    protected $casts = [
        'facilities' => 'array',
        'is_active' => 'boolean',
        'price_per_hour' => 'integer',
    ];

    protected $appends = [
        'image_url',
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function bookingItems(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    public function reservedSlots(): HasMany
    {
        return $this->hasMany(ReservedSlot::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
