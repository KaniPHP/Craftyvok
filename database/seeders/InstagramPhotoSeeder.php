<?php

namespace Database\Seeders;

use App\Models\SocialPost;
use Illuminate\Database\Seeder;

class InstagramPhotoSeeder extends Seeder
{
    public function run(): void
    {
        $links = [
            'https://www.instagram.com/p/Dfr5gUzEi7a/?img_index=1',
            'https://www.instagram.com/p/DdUAo0gprB9/',
            'https://www.instagram.com/p/Da9d7YBsi5K/',
            'https://www.instagram.com/p/DNXZwXSxDH5/',
            'https://www.instagram.com/p/DKerMjjxKhI/',
            'https://www.instagram.com/p/DKWiDsKRB-N/',
            'https://www.instagram.com/p/DJZHcm1yAif/',
        ];

        foreach (array_reverse($links) as $link) {
            $path = trim((string) parse_url($link, PHP_URL_PATH), '/');
            $shortcode = explode('/', $path)[1];

            SocialPost::updateOrCreate(
                ['instagram_id' => $shortcode],
                [
                    'media_type' => 'IMAGE',
                    'title' => 'Craftyvok Instagram photo',
                    'caption' => 'A little gifting inspiration from Craftyvok.',
                    'media_url' => null,
                    'thumbnail_url' => null,
                    'permalink' => $link,
                    'published_at' => null,
                    'is_highlight' => false,
                ],
            );
        }
    }
}
