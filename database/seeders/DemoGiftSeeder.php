<?php

namespace Database\Seeders;

use App\Models\Gift;
use Illuminate\Database\Seeder;

class DemoGiftSeeder extends Seeder
{
    public function run(): void
    {
        $gifts = [
            ['name' => 'Love Note Gift Box', 'category' => 'love', 'description' => 'A keepsake box with a personal message.', 'price' => 799, 'image' => '1549465220-1a8b9238cd48'],
            ['name' => 'Together Forever Frame', 'category' => 'love', 'description' => 'Celebrate your favourite photo together.', 'price' => 999, 'image' => '1544816155-12df9643f363'],
            ['name' => 'Sweetheart Surprise Hamper', 'category' => 'love', 'description' => 'A little collection of thoughtful surprises.', 'price' => 1299, 'image' => '1513201099705-a9746e1e201f'],

            ['name' => 'Birthday Surprise Box', 'category' => 'birthday', 'description' => 'A cheerful gift box for their big day.', 'price' => 899, 'image' => '1607082349566-187342175e2f'],
            ['name' => 'Birthday Photo Album', 'category' => 'birthday', 'description' => 'Keep all their favourite birthday memories.', 'price' => 699, 'image' => '1513201099705-a9746e1e201f'],
            ['name' => 'Make a Wish Gift Hamper', 'category' => 'birthday', 'description' => 'A bright, celebration-ready gift hamper.', 'price' => 1499, 'image' => '1512909006721-3d6018887383'],

            ['name' => 'Just Because Gift Box', 'category' => 'occasions', 'description' => 'A lovely surprise for an ordinary day.', 'price' => 599, 'image' => '1549465220-1a8b9238cd48'],
            ['name' => 'New Home Gift Set', 'category' => 'occasions', 'description' => 'A warm welcome for a brand-new home.', 'price' => 1199, 'image' => '1512909006721-3d6018887383'],
            ['name' => 'Festive Cheer Hamper', 'category' => 'occasions', 'description' => 'Share a little extra joy this festive season.', 'price' => 1599, 'image' => '1607082349566-187342175e2f'],

            ['name' => 'Best Friend Memory Box', 'category' => 'relationship', 'description' => 'A little keepsake for your favourite person.', 'price' => 899, 'image' => '1544816155-12df9643f363'],
            ['name' => 'Couple Photo Keepsake', 'category' => 'relationship', 'description' => 'Turn a favourite moment into a keepsake.', 'price' => 1099, 'image' => '1513201099705-a9746e1e201f'],
            ['name' => 'Family Moments Gift Set', 'category' => 'relationship', 'description' => 'Thoughtful personalised gifts for your people.', 'price' => 1299, 'image' => '1549465220-1a8b9238cd48'],

            ['name' => 'Team Welcome Gift Box', 'category' => 'corporate', 'description' => 'A warm welcome for a new teammate.', 'price' => 1499, 'image' => '1512909006721-3d6018887383'],
            ['name' => 'Employee Thank You Hamper', 'category' => 'corporate', 'description' => 'A personal thank-you for great work.', 'price' => 1799, 'image' => '1607082349566-187342175e2f'],
            ['name' => 'Branded Desk Gift Set', 'category' => 'corporate', 'description' => 'Thoughtful gifting for clients and teams.', 'price' => 999, 'image' => '1513883049090-d0b7439799bf'],

            ['name' => 'Personalised Greeting Card', 'category' => 'greetings', 'description' => 'Add your own words to make it theirs.', 'price' => 199, 'image' => '1513201099705-a9746e1e201f'],
            ['name' => 'A Little Hello Gift Box', 'category' => 'greetings', 'description' => 'Send a smile with a small thoughtful gift.', 'price' => 499, 'image' => '1549465220-1a8b9238cd48'],
            ['name' => 'Pop-Up Celebration Card', 'category' => 'greetings', 'description' => 'A colourful card for a moment worth celebrating.', 'price' => 299, 'image' => '1607082349566-187342175e2f'],
        ];

        foreach ($gifts as $gift) {
            Gift::updateOrCreate(
                ['name' => $gift['name']],
                [
                    'category' => $gift['category'],
                    'description' => $gift['description'],
                    'price' => $gift['price'],
                    'image_url' => "https://images.unsplash.com/photo-{$gift['image']}?auto=format&fit=crop&w=760&q=80",
                    'is_active' => true,
                ],
            );
        }
    }
}
