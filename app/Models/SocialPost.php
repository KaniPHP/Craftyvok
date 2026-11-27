<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'instagram_id',
    'media_type',
    'title',
    'caption',
    'media_url',
    'thumbnail_url',
    'permalink',
    'published_at',
    'is_highlight',
])]
class SocialPost extends Model
{
    protected $appends = ['embed_url'];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_highlight' => 'boolean',
        ];
    }

    public function getEmbedUrlAttribute(): ?string
    {
        $path = parse_url((string) $this->permalink, PHP_URL_PATH);

        if (! is_string($path)) {
            return null;
        }

        $segments = array_values(array_filter(explode('/', trim($path, '/'))));
        $type = $segments[0] ?? null;
        $shortcode = $segments[1] ?? null;

        if (! in_array($type, ['p', 'reel', 'tv'], true) || ! is_string($shortcode) || ! preg_match('/\A[A-Za-z0-9_-]+\z/', $shortcode)) {
            return null;
        }

        return "https://www.instagram.com/{$type}/{$shortcode}/embed/";
    }
}
