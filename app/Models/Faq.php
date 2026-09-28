<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    public const CATEGORIES = ['General', 'Tour Packages', 'Visa', 'Flight Tickets', 'Bus Tickets', 'Payments & Refunds'];

    protected $fillable = ['category', 'question', 'answer', 'position', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByRaw('position IS NULL')->orderBy('position')->orderBy('id');
    }
}
