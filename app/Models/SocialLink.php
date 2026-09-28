<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{
    /**
     * Fixed palette an admin can pick from — matches the inline SVGs in
     * resources/views/components/social-icon.blade.php. Any platform not in
     * this list is simply never offered in the "add social icon" modal.
     */
    public const PLATFORMS = [
        'facebook' => 'Facebook',
        'instagram' => 'Instagram',
        'twitter' => 'X (Twitter)',
        'youtube' => 'YouTube',
        'linkedin' => 'LinkedIn',
        'tiktok' => 'TikTok',
        'whatsapp' => 'WhatsApp',
        'pinterest' => 'Pinterest',
    ];

    protected $fillable = [
        'platform',
        'url',
        'position',
    ];

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position')->orderBy('id');
    }

    public function getLabelAttribute(): string
    {
        return self::PLATFORMS[$this->platform] ?? ucfirst($this->platform);
    }
}
