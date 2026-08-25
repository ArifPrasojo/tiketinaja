<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sport extends Model
{
    protected $fillable = ['name', 'slug', 'icon'];

    public function courts(): HasMany
    {
        return $this->hasMany(Court::class);
    }
}
