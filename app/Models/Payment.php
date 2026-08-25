<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'booking_id', 'payment_code', 'amount', 'payment_method',
        'status', 'paid_at', 'payload'
    ];

    protected $casts = [
        'amount' => 'integer',
        'paid_at' => 'datetime',
        'status' => \App\Enums\PaymentStatus::class,
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
