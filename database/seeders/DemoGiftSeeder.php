<?php

namespace Database\Seeders;

use App\Models\Gift;
use Illuminate\Database\Seeder;

class DemoGiftSeeder extends Seeder
{
    public function run(): void
    {
        $images = [
            'WhatsApp Image 2026-09-09 at 11.44.52 AM.jpeg',
            'WhatsApp Image 2026-09-09 at 11.44.52 AM (1).jpeg',
            'WhatsApp Image 2026-09-09 at 11.44.52 AM (2).jpeg',
            'WhatsApp Image 2026-09-09 at 11.44.52 AM (3).jpeg',
            'WhatsApp Image 2026-09-09 at 11.44.52 AM (4).jpeg',
            'WhatsApp Image 2026-09-09 at 11.45.07 AM.jpeg',
            'WhatsApp Image 2026-09-09 at 11.45.07 AM (1).jpeg',
            'WhatsApp Image 2026-09-09 at 11.45.07 AM (2).jpeg',
            'WhatsApp Image 2026-09-09 at 11.45.07 AM (3).jpeg',
            'WhatsApp Image 2026-09-09 at 11.45.07 AM (4).jpeg',
            'WhatsApp Image 2026-09-09 at 11.45.07 AM (5).jpeg',
            'WhatsApp Image 2026-09-09 at 11.45.07 AM (6).jpeg',
            'WhatsApp Image 2026-09-09 at 11.45.07 AM (7).jpeg',
        ];

        $gifts = [
            ['name' => 'Love Note Gift Box', 'category' => 'love', 'description' => 'A keepsake box with a personal message.', 'price' => 799, 'image' => 9],
            ['name' => 'Together Forever Frame', 'category' => 'love', 'description' => 'Celebrate your favourite photo together.', 'price' => 999, 'image' => 12],
            ['name' => 'Sweetheart Surprise Hamper', 'category' => 'love', 'description' => 'A little collection of thoughtful surprises.', 'price' => 1299, 'image' => 11],

            ['name' => 'Birthday Surprise Box', 'category' => 'birthday', 'description' => 'A cheerful gift box for their big day.', 'price' => 899, 'image' => 3],
            ['name' => 'Birthday Photo Album', 'category' => 'birthday', 'description' => 'Keep all their favourite birthday memories.', 'price' => 699, 'image' => 4],
            ['name' => 'Make a Wish Gift Hamper', 'category' => 'birthday', 'description' => 'A bright, celebration-ready gift hamper.', 'price' => 1499, 'image' => 10],

            ['name' => 'Just Because Gift Box', 'category' => 'occasions', 'description' => 'A lovely surprise for an ordinary day.', 'price' => 599, 'image' => 5],
            ['name' => 'New Home Gift Set', 'category' => 'occasions', 'description' => 'A warm welcome for a brand-new home.', 'price' => 1199, 'image' => 8],
            ['name' => 'Festive Cheer Hamper', 'category' => 'occasions', 'description' => 'Share a little extra joy this festive season.', 'price' => 1599, 'image' => 6],

            ['name' => 'Best Friend Memory Box', 'category' => 'relationship', 'description' => 'A little keepsake for your favourite person.', 'price' => 899, 'image' => 9],
            ['name' => 'Couple Photo Keepsake', 'category' => 'relationship', 'description' => 'Turn a favourite moment into a keepsake.', 'price' => 1099, 'image' => 12],
            ['name' => 'Family Moments Gift Set', 'category' => 'relationship', 'description' => 'Thoughtful personalised gifts for your people.', 'price' => 1299, 'image' => 11],

            ['name' => 'Team Welcome Gift Box', 'category' => 'corporate', 'description' => 'A warm welcome for a new teammate.', 'price' => 1499, 'image' => 0],
            ['name' => 'Employee Thank You Hamper', 'category' => 'corporate', 'description' => 'A personal thank-you for great work.', 'price' => 1799, 'image' => 7],
            ['name' => 'Branded Desk Gift Set', 'category' => 'corporate', 'description' => 'Thoughtful gifting for clients and teams.', 'price' => 999, 'image' => 10],

            ['name' => 'Personalised Greeting Card', 'category' => 'greetings', 'description' => 'Add your own words to make it theirs.', 'price' => 199, 'image' => 2],
            ['name' => 'A Little Hello Gift Box', 'category' => 'greetings', 'description' => 'Send a smile with a small thoughtful gift.', 'price' => 499, 'image' => 1],
            ['name' => 'Pop-Up Celebration Card', 'category' => 'greetings', 'description' => 'A colourful card for a moment worth celebrating.', 'price' => 299, 'image' => 3],
        ];

        foreach ($gifts as $gift) {
            Gift::updateOrCreate(
                ['name' => $gift['name']],
                [
                    'category' => $gift['category'],
                    'description' => $gift['description'],
                    'price' => $gift['price'],
                    'image_url' => '/img/'.rawurlencode($images[$gift['image']]),
                    'is_active' => true,
                ],
            );
        }
    }
}
