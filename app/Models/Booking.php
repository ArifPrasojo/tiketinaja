<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $fillable = [
        'booking_code', 'user_id', 'venue_id', 'promo_id', 'status',
        'total_amount', 'discount_amount', 'expires_at', 'notes',
        'qr_code', 'checked_in', 'checked_in_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'total_amount' => 'integer',
        'discount_amount' => 'integer',
        'checked_in' => 'boolean',
        'status' => BookingStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    public function reservedSlots(): HasMany
    {
        return $this->hasMany(ReservedSlot::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function promoUsage(): HasOne
    {
        return $this->hasOne(PromoUsage::class);
    }

    /**
     * Final amount after discount (computed column, but also accessible as attribute)
     */
    public function getFinalAmountAttribute(): int
    {
        return $this->total_amount - ($this->discount_amount ?? 0);
    }
}
