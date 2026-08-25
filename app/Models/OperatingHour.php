<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperatingHour extends Model
{
    protected $fillable = [
        'venue_id', 'day_of_week', 'open_time', 'close_time', 'is_closed'
    ];

    protected $casts = [
        'is_closed' => 'boolean',
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }
}
