<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = [
        'label',
        'discount_percentage',
        'is_active',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'is_active'           => 'boolean',
        'starts_at'           => 'datetime',
        'ends_at'             => 'datetime',
        'discount_percentage' => 'float',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(fn($q) => $q
                ->whereNull('starts_at')
                ->orWhere('starts_at', '<=', now()))
            ->where(fn($q) => $q
                ->whereNull('ends_at')
                ->orWhere('ends_at', '>=', now()));
    }
}
