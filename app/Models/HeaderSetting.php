<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HeaderSetting extends Model
{
    protected $fillable = [
        'logo',
        'background_color',
    ];

    /**
     * There is only ever one settings row. Callers always go through this
     * accessor instead of querying the model directly.
     */
    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? Storage::disk('public')->url($this->logo) : null;
    }
}
