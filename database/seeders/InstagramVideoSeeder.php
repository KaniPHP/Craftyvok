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
            'https://www.instagram.com/p/DQWVePqib6p/',
            'https://www.instagram.com/p/DQMUotsiVG-/',
            'https://www.instagram.com/p/DOAzxMnEvHU/',
            'https://www.instagram.com/p/DNUm-E2Rohp/',
            'https://www.instagram.com/p/DNRvReSTNv-/',
            'https://www.instagram.com/p/DMlQ95YTwrI/',
            'https://www.instagram.com/p/DJYogEuxPjb/',
            'https://www.instagram.com/p/DF8Z-2kzhvg/',
            'https://www.instagram.com/p/DHVUxh3RH49/',
            'https://www.instagram.com/p/DF4MEK4yaJc/',
            'https://www.instagram.com/p/DFt34feyMT6/',
            'https://www.instagram.com/p/DEokPO_yMy_/',
            'https://www.instagram.com/p/DCwQfWGyvWP/',
            'https://www.instagram.com/p/DDY45NXS4JP/',
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
