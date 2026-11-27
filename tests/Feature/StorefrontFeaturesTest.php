<?php

namespace Tests\Feature;

use App\Models\ChatConversation;
use App\Models\Gift;
use App\Models\Order;
use App\Models\SocialPost;
use App\Models\User;
use App\Services\InstagramFeedService;
use Database\Seeders\DemoGiftSeeder;
use Database\Seeders\InstagramPhotoSeeder;
use Database\Seeders\InstagramVideoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class StorefrontFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_gifts_and_google_review_link(): void
    {
        config(['services.google_reviews.url' => 'https://g.page/r/example/review']);
        Gift::create([
            'name' => 'Custom gift box',
            'description' => 'Made for a special day',
            'price' => 1499,
            'is_active' => true,
        ]);
        SocialPost::create([
            'instagram_id' => 'homepage-reel',
            'media_type' => 'REELS',
            'caption' => 'A little gift inspiration',
            'thumbnail_url' => 'https://example.test/reel.jpg',
            'permalink' => 'https://www.instagram.com/reel/homepage-reel/',
            'published_at' => now(),
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Custom gift box')
            ->assertSee('Ask about this gift')
            ->assertSee('A little gift inspiration')
            ->assertSee('Videos & Reels')
            ->assertSee('id="collection-love"', false)
            ->assertSee('id="collection-birthday"', false)
            ->assertSee('id="collection-corporate"', false)
            ->assertSee('All products')
            ->assertSee('Birthday gifts')
            ->assertSee('My Orders')
            ->assertSee('aria-label="Search gifts"', false)
            ->assertSee('Sample Google-style reviews')
            ->assertSee('not actual customer testimonials or Google reviews')
            ->assertSee('The personalised hamper was beautifully packed')
            ->assertSee('பரிசுக் கூடை அழகாக பேக் செய்து')
            ->assertSee('Google-ல் மதிப்புரை எழுதுங்கள்')
            ->assertSee('https://g.page/r/example/review')
            ->assertSee('203, Ground Floor, 5th Street, Cross Cut Road, Gandhipuram, Coimbatore, Tamil Nadu 641012')
            ->assertSee('View on Google Maps');
    }

    public function test_guest_can_chat_about_a_gift_and_receive_an_admin_reply(): void
    {
        $start = $this->postJson('/chat/conversations', [
            'item' => 'Custom gift box',
            'message' => 'How much is this gift?',
        ])->assertOk()->assertJsonPath('messages.1.sender', 'visitor');

        $token = $start->json('token');
        $conversationId = $start->json('conversationId');
        $conversation = ChatConversation::findOrFail($conversationId);

        $this->getJson("/chat/conversations/{$conversationId}/messages", [
            'X-Chat-Token' => $token,
        ])->assertOk()->assertJsonPath('messages.1.body', 'How much is this gift?');

        $this->getJson("/chat/conversations/{$conversationId}/messages", [
            'X-Chat-Token' => Str::random(64),
        ])->assertNotFound();

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)
            ->post("/admin/conversations/{$conversation->id}/messages", ['message' => 'It is ₹1,499.'])
            ->assertRedirect();

        $this->getJson("/chat/conversations/{$conversationId}/messages", [
            'X-Chat-Token' => $token,
        ])->assertJsonPath('messages.2.body', 'It is ₹1,499.');
    }

    public function test_non_admin_cannot_open_admin_dashboard(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_admin_can_manage_gifts_from_the_dashboard(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->get('/admin')
            ->assertOk()
            ->assertSee('Gift enquiry inbox')
            ->assertSee('Add a gift item');

        $this->post('/admin/gifts', [
            'name' => 'Personalised mug',
            'category' => 'love',
            'price' => '499.00',
            'description' => 'Add a name or message',
            'image_url' => 'https://example.test/mug.jpg',
            'is_active' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('gifts', [
            'name' => 'Personalised mug',
            'category' => 'love',
            'price' => '499.00',
            'is_active' => 1,
        ]);

        $this->post('/admin/gifts', [
            'name' => 'Custom name board',
            'category' => 'occasions',
            'image_url' => '/img/WhatsApp%20Image%202026-09-09%20at%2011.44.52%20AM.jpeg',
            'is_active' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('gifts', [
            'name' => 'Custom name board',
            'image_url' => '/img/WhatsApp%20Image%202026-09-09%20at%2011.44.52%20AM.jpeg',
        ]);
    }

    public function test_admin_rejects_image_paths_outside_the_local_image_folder(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->post('/admin/gifts', [
                'name' => 'Invalid image item',
                'category' => 'occasions',
                'image_url' => 'javascript:alert(1)',
            ])
            ->assertSessionHasErrors([
                'image_url' => 'The image must be an HTTP(S) URL or an existing image path under /img/.',
            ]);
    }

    public function test_customer_can_register_and_only_sees_their_own_orders(): void
    {
        $this->get('/login')->assertOk()->assertSee('Welcome back.');
        $this->get('/register')->assertOk()->assertSee('Create your account.');

        $this->post('/register', [
            'name' => 'Gift Shopper',
            'email' => 'shopper@example.test',
            'password' => 'a-strong-password',
            'password_confirmation' => 'a-strong-password',
        ])->assertRedirect(route('orders.index'));

        $shopper = User::where('email', 'shopper@example.test')->firstOrFail();
        $otherShopper = User::factory()->create(['email' => 'someone-else@example.test']);
        $shopper->orders()->create([
            'order_number' => 'CV-2026-001',
            'gift_name' => 'Custom gift box',
            'total' => '1499.00',
            'status' => 'shipped',
            'tracking_note' => 'Your gift is on the way.',
        ]);
        $otherShopper->orders()->create([
            'order_number' => 'CV-2026-002',
            'gift_name' => 'Private order',
            'status' => 'processing',
        ]);

        $this->get('/orders')
            ->assertOk()
            ->assertSee('CV-2026-001')
            ->assertSee('Your gift is on the way.')
            ->assertDontSee('CV-2026-002')
            ->assertDontSee('Private order');
    }

    public function test_customer_can_log_in_but_admin_accounts_use_the_admin_sign_in(): void
    {
        User::factory()->create([
            'email' => 'shopper-login@example.test',
            'password' => Hash::make('customer-password'),
            'is_admin' => false,
        ]);

        $this->post('/login', [
            'email' => 'shopper-login@example.test',
            'password' => 'customer-password',
        ])->assertRedirect(route('orders.index'));
        $this->assertAuthenticated();

        $this->post('/logout')->assertRedirect(route('home'));
        $this->assertGuest();

        User::factory()->create([
            'email' => 'admin-login@example.test',
            'password' => Hash::make('admin-password'),
            'is_admin' => true,
        ]);

        $this->from('/login')->post('/login', [
            'email' => 'admin-login@example.test',
            'password' => 'admin-password',
        ])->assertRedirect('/login')
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_can_add_an_order_and_update_tracking_status(): void
    {
        $customer = User::factory()->create([
            'email' => 'customer@example.test',
            'is_admin' => false,
        ]);
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        $this->post('/admin/orders', [
            'email' => $customer->email,
            'order_number' => 'CV-TRACK-1',
            'gift_name' => 'Personalised photo frame',
            'total' => '899.00',
            'status' => 'confirmed',
            'estimated_delivery' => '2026-12-01',
            'tracking_note' => 'We are preparing your gift.',
        ])->assertRedirect();

        $order = Order::where('order_number', 'CV-TRACK-1')->firstOrFail();
        $this->patch("/admin/orders/{$order->id}", [
            'status' => 'shipped',
            'estimated_delivery' => '2026-12-02',
            'tracking_note' => 'Your gift has been shipped.',
        ])->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'shipped',
            'tracking_note' => 'Your gift has been shipped.',
        ]);
    }

    public function test_instagram_sync_stores_reels_videos_and_highlights(): void
    {
        config([
            'services.instagram.user_id' => 'instagram-user-123',
            'services.instagram.access_token' => 'test-token',
            'services.instagram.graph_version' => 'v25.0',
            'services.instagram.profile_url' => 'https://www.instagram.com/craftyvok/',
        ]);

        Http::fakeSequence()
            ->push(['data' => [
                [
                    'id' => 'video-1',
                    'media_type' => 'VIDEO',
                    'media_url' => 'https://example.test/video.mp4',
                    'thumbnail_url' => 'https://example.test/video.jpg',
                    'permalink' => 'https://www.instagram.com/reel/video-1/',
                    'timestamp' => '2026-11-27T10:00:00+0000',
                ],
                ['id' => 'image-1', 'media_type' => 'IMAGE'],
                [
                    'id' => 'reel-1',
                    'media_type' => 'REELS',
                    'media_url' => 'https://example.test/reel.mp4',
                    'permalink' => 'https://www.instagram.com/reel/reel-1/',
                ],
            ]])
            ->push(['data' => [
                [
                    'id' => 'highlight-1',
                    'name' => 'Gift ideas',
                    'cover_media' => ['url' => 'https://example.test/highlight.jpg'],
                ],
            ]]);

        $count = app(InstagramFeedService::class)->sync();

        $this->assertSame(3, $count);
        $this->assertSame(2, SocialPost::where('is_highlight', false)->count());
        $this->assertSame(1, SocialPost::where('is_highlight', true)->count());
        $this->assertDatabaseHas('social_posts', [
            'instagram_id' => 'highlight:highlight-1',
            'title' => 'Gift ideas',
        ]);
    }

    public function test_supplied_instagram_photos_and_videos_are_embedded_in_their_sections(): void
    {
        $this->seed(InstagramVideoSeeder::class);
        $this->seed(InstagramPhotoSeeder::class);

        $this->assertSame(25, SocialPost::where('media_type', 'VIDEO')->count());
        $this->assertSame(7, SocialPost::where('media_type', 'IMAGE')->count());

        $this->get('/')
            ->assertOk()
            ->assertSee('Recent Instagram photos')
            ->assertSee('https://www.instagram.com/p/Dfr5gUzEi7a/embed/', false)
            ->assertSee('https://www.instagram.com/p/DGkQe7Yxpj3/embed/', false)
            ->assertSee('https://www.instagram.com/p/DMRnJfnRiv9/embed/', false)
            ->assertSee('https://www.instagram.com/p/DDY45NXS4JP/embed/', false);
    }

    public function test_demo_gifts_are_seeded_into_all_menu_collections(): void
    {
        $this->seed(DemoGiftSeeder::class);

        $this->assertSame(18, Gift::where('is_active', true)->count());
        $this->assertSame(6, Gift::query()->distinct()->count('category'));
        $this->assertSame(13, Gift::query()->distinct()->count('image_url'));
        $this->assertDatabaseHas('gifts', [
            'name' => 'Love Note Gift Box',
            'image_url' => '/img/WhatsApp%20Image%202026-09-09%20at%2011.45.07%20AM%20%284%29.jpeg',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Our creations')
            ->assertSee('/img/WhatsApp%20Image', false)
            ->assertSee('Love Note Gift Box')
            ->assertSee('Birthday Surprise Box')
            ->assertSee('New Home Gift Set')
            ->assertSee('Team Welcome Gift Box')
            ->assertSee('Personalised Greeting Card');
    }
}
