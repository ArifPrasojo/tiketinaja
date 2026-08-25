<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Promo extends Model
{
    protected $fillable = [
        'venue_id', 'code', 'description', 'type', 'value',
        'max_uses', 'used_count', 'min_booking_amount', 'max_discount',
        'starts_at', 'expires_at', 'is_active',
    ];

    protected $casts = [
        'value' => 'integer',
        'max_uses' => 'integer',
        'used_count' => 'integer',
        'min_booking_amount' => 'integer',
        'max_discount' => 'integer',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(PromoUsage::class);
    }

    /**
     * Check if this promo is currently valid (not checking per-user usage)
     */
    public function isValid(): bool
    {
        if (!$this->is_active) return false;

        $now = now();

        if ($this->starts_at) {
            $starts = $this->starts_at instanceof \Carbon\Carbon
                ? $this->starts_at : \Carbon\Carbon::parse($this->starts_at);
            if ($starts->isFuture()) return false;
        }

        if ($this->expires_at) {
            $expires = $this->expires_at instanceof \Carbon\Carbon
                ? $this->expires_at : \Carbon\Carbon::parse($this->expires_at);
            if ($expires->isPast()) return false;
        }

        if ($this->max_uses && $this->used_count >= $this->max_uses) return false;

        return true;
    }

    /**
     * Calculate the discount amount for a given booking total
     */
    public function calculateDiscount(int $amount): int
    {
        if ($amount < $this->min_booking_amount) return 0;

        if ($this->type === 'percentage') {
            $discount = (int) round($amount * $this->value / 100);
            if ($this->max_discount) {
                $discount = min($discount, $this->max_discount);
            }
            return $discount;
        }

        return min($this->value, $amount);
    }
}
