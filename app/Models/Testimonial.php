<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    public const SOURCES = ['website' => 'Website', 'google' => 'Google', 'facebook' => 'Facebook', 'video' => 'Video'];

    protected $fillable = ['name', 'location', 'avatar', 'rating', 'content', 'source', 'video_url', 'is_approved', 'position'];

    protected function casts(): array
    {
        return ['is_approved' => 'boolean'];
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByRaw('position IS NULL')->orderBy('position')->orderByDesc('created_at');
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? media_url($this->avatar)
            : 'https://ui-avatars.com/api/?background=0047AB&color=fff&name='.urlencode($this->name);
    }
}
