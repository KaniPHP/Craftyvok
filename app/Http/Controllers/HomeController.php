<?php

namespace App\Http\Controllers;

use App\Models\Gift;
use App\Models\SocialPost;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $gifts = Gift::query()->where('is_active', true)->orderBy('id')->get();
        $portfolioPhotos = $gifts
            ->filter(fn (Gift $gift): bool => str_starts_with((string) $gift->image_url, '/img/'))
            ->unique('image_url')
            ->values();

        $giftCategories = [
            'love' => [
                'label' => 'Love',
                'subtitle' => 'Little details, big feelings.',
                'eyebrow' => 'MADE WITH A LITTLE EXTRA LOVE',
                'section_class' => 'collection-love',
                'card_class' => 'category-pink',
            ],
            'birthday' => [
                'label' => 'Birthday Gifts',
                'subtitle' => 'Make their birthday the one they remember.',
                'eyebrow' => 'ANOTHER YEAR. ANOTHER REASON TO CELEBRATE',
                'section_class' => 'collection-birthday',
                'card_class' => 'category-yellow',
            ],
            'occasions' => [
                'label' => 'Occasions',
                'subtitle' => 'A thoughtful gift for every kind of day.',
                'eyebrow' => 'FOR ALL THE MOMENTS IN BETWEEN',
                'section_class' => 'collection-occasions',
                'card_class' => 'category-red',
            ],
            'relationship' => [
                'label' => 'Relationship',
                'subtitle' => 'For your person, your people, your kind of love.',
                'eyebrow' => 'GIFTS THAT FEEL LIKE YOU KNOW THEM',
                'section_class' => 'collection-relationship',
                'card_class' => 'category-blue',
            ],
            'corporate' => [
                'label' => 'Corporate Gifts',
                'subtitle' => 'Say thanks to the people behind the good work.',
                'eyebrow' => 'A LITTLE APPRECIATION GOES A LONG WAY',
                'section_class' => 'collection-corporate',
                'card_class' => 'category-gold',
            ],
            'greetings' => [
                'label' => 'Greetings',
                'subtitle' => 'Send a note, a smile, and a little surprise.',
                'eyebrow' => 'SOME THINGS ARE BETTER SAID WITH A GIFT',
                'section_class' => 'collection-greetings',
                'card_class' => 'category-rose',
            ],
        ];

        return view('welcome', [
            'giftGroups' => $gifts->groupBy('category'),
            'portfolioPhotos' => $portfolioPhotos,
            'giftCategories' => $giftCategories,
            'photos' => SocialPost::query()
                ->where('is_highlight', false)
                ->where('media_type', 'IMAGE')
                ->latest('published_at')
                ->orderByDesc('id')
                ->take(12)
                ->get(),
            'videos' => SocialPost::query()
                ->where('is_highlight', false)
                ->whereIn('media_type', ['VIDEO', 'REELS'])
                ->latest('published_at')
                ->orderByDesc('id')
                ->take(30)
                ->get(),
            'highlights' => SocialPost::query()
                ->where('is_highlight', true)
                ->latest('published_at')
                ->take(8)
                ->get(),
            'instagramProfile' => config('services.instagram.profile_url'),
            'googleReviewUrl' => config('services.google_reviews.url'),
        ]);
    }
}
