<?php

namespace Database\Seeders;

use App\Models\SocialPost;
use Illuminate\Database\Seeder;

class InstagramVideoSeeder extends Seeder
{
    public function run(): void
    {
        $links = [
            'https://www.instagram.com/p/DGkQe7Yxpj3/',
            'https://www.instagram.com/p/Dfv8C60SCLr/',
            'https://www.instagram.com/p/DeWHhZTJUyq/',
            'https://www.instagram.com/p/DbcdstVJN_U/',
            'https://www.instagram.com/p/DbfXQFYJzhL/',
            'https://www.instagram.com/p/DQv-8vcidf9/',
            'https://www.instagram.com/p/DZW9cNrJduc/',
            'https://www.instagram.com/p/DQmJ44fiXUO/',
            'https://www.instagram.com/p/DNafGoSJjPt/',
            'https://www.instagram.com/p/DNruAcjUpAv/',
            'https://www.instagram.com/p/DMRnJfnRiv9/',
        ];

        foreach (array_reverse($links) as $link) {
            $path = trim((string) parse_url($link, PHP_URL_PATH), '/');
            $shortcode = explode('/', $path)[1];

            SocialPost::updateOrCreate(
                ['instagram_id' => $shortcode],
                [
                    'media_type' => 'VIDEO',
                    'title' => 'Craftyvok Instagram video',
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
