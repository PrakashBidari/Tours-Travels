<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'user_id',
        'property_id',
        'check_in',
        'check_out',
        'guests',
    ];

    protected function casts(): array
    {
        return [
            'check_in' => 'date',
            'check_out' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function nights(): int
    {
        return max(1, $this->check_in->diffInDays($this->check_out));
    }

    public function subtotal(): float
    {
        return $this->nights() * (float) $this->property->price_per_night;
    }
}
