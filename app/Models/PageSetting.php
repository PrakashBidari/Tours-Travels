<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PageSetting extends Model
{
    protected $fillable = [
        'about_title',
        'about_description',
        'about_image',
        'vision_title',
        'vision_description',
        'vision_image',
        'mission_title',
        'mission_description',
        'mission_image',
        'contact_email',
        'contact_phone',
        'contact_address',
        'footer_tagline',
        'footer_icon',
        'copyright_text',
        'company_name',
        'contact_mobile',
        'whatsapp_number',
        'office_hours',
        'map_embed_url',
        'hero_script',
        'hero_title',
        'hero_subtitle',
        'hero_image',
        'hero_video_url',
        'why_title',
        'why_description',
        'why_image',
        'stat_years',
        'stat_travelers',
        'stat_destinations',
        'meta_title',
        'meta_description',
        'google_analytics_id',
        'facebook_pixel_id',
    ];

    /**
     * There is only ever one settings row. Callers always go through this
     * accessor instead of querying the model directly.
     */
    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }

    public function getAboutImageUrlAttribute(): ?string
    {
        return $this->about_image ? Storage::disk('public')->url($this->about_image) : null;
    }

    public function getVisionImageUrlAttribute(): ?string
    {
        return $this->vision_image ? Storage::disk('public')->url($this->vision_image) : null;
    }

    public function getMissionImageUrlAttribute(): ?string
    {
        return $this->mission_image ? Storage::disk('public')->url($this->mission_image) : null;
    }

    public function getHeroImageUrlAttribute(): string
    {
        return media_url($this->hero_image, config('travel.images.hero'));
    }

    public function getWhyImageUrlAttribute(): string
    {
        return media_url($this->why_image, config('travel.images.traveler'));
    }

    public function getFooterIconUrlAttribute(): ?string
    {
        return $this->footer_icon ? Storage::disk('public')->url($this->footer_icon) : null;
    }

    /**
     * The copyright line shown in the footer. Admins may type raw HTML
     * (e.g. an <a> tag) and use the {year} token, which is swapped for the
     * current year on every render so it never goes stale.
     */
    public function getRenderedCopyrightAttribute(): string
    {
        $template = $this->copyright_text ?: '&copy; {year} '.config('app.name', 'Booking').'. All rights reserved.';

        return str_replace('{year}', date('Y'), $template);
    }
}
