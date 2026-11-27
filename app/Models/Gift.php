<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'category', 'description', 'price', 'image_url', 'images', 'is_active'])]
class Gift extends Model
{
    public const CATEGORIES = [
        'love' => 'Love',
        'birthday' => 'Birthday Gifts',
        'occasions' => 'Occasions',
        'relationship' => 'Relationship',
        'corporate' => 'Corporate Gifts',
        'greetings' => 'Greetings',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'images' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return array<int, string>
     */
    public function getImageGalleryAttribute(): array
    {
        return $this->images ?: ($this->image_url ? [$this->image_url] : []);
    }
}
