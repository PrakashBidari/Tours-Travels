<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
    protected $fillable = ['title', 'album', 'type', 'path', 'position'];

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByRaw('position IS NULL')->orderBy('position')->orderByDesc('created_at');
    }

    public function getUrlAttribute(): string
    {
        return media_url($this->path);
    }

    /** YouTube embed URL for video items (accepts watch / youtu.be / embed links). */
    public function getEmbedUrlAttribute(): ?string
    {
        if ($this->type !== 'video') {
            return null;
        }

        if (preg_match('~(?:youtu\.be/|v=|embed/)([\w-]{11})~', $this->path, $m)) {
            return 'https://www.youtube.com/embed/'.$m[1];
        }

        return $this->url;
    }

    public function getThumbnailAttribute(): string
    {
        if ($this->type === 'video' && preg_match('~(?:youtu\.be/|v=|embed/)([\w-]{11})~', $this->path, $m)) {
            return 'https://img.youtube.com/vi/'.$m[1].'/hqdefault.jpg';
        }

        return $this->url;
    }
}
