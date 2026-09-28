<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogPost extends Model
{
    public const CATEGORIES = ['Travel Tips', 'Visa Updates', 'Airline News', 'Destination Guide', 'Travel Checklist', 'Festival Travel', 'Trekking'];

    protected $fillable = [
        'user_id', 'title', 'slug', 'category', 'excerpt', 'content', 'featured_image',
        'meta_title', 'meta_description', 'published_at', 'views',
    ];

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Published = has a publish date that has already passed (future dates are "scheduled"). */
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function getImageUrlAttribute(): string
    {
        return media_url($this->featured_image, config('travel.images.mountains'));
    }

    public function getStatusLabelAttribute(): string
    {
        return match (true) {
            ! $this->published_at => 'Draft',
            $this->published_at->isFuture() => 'Scheduled',
            default => 'Published',
        };
    }

    public function getReadingTimeAttribute(): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags($this->content)) / 200));
    }
}
