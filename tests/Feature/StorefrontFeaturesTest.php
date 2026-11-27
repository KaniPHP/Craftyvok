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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class StorefrontFeaturesTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('authFormRoutes')]
    public function test_auth_forms_use_https_action_behind_render_proxy(string $path): void
    {
        $this->get('http://craftyvok.onrender.com'.$path, [
            'X-Forwarded-Proto' => 'https',
        ])
            ->assertOk()
            ->assertSee('action="https://craftyvok.onrender.com'.$path.'"', false);
    }

    public static function authFormRoutes(): iterable
    {
        yield 'shopper login' => ['/login'];
        yield 'shopper registration' => ['/register'];
        yield 'admin login' => ['/admin/login'];
    }

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
            ->assertSee('img/craftyvok-award-gift.png')
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

    public function test_non_admin_cannot_open_admin_pages(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->get('/admin')
            ->assertForbidden();

        $this->get('/admin/gifts')
            ->assertForbidden();
    }

    public function test_admin_menu_items_render_on_separate_pages(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->get('/admin')
            ->assertOk()
            ->assertSee('Products by category')
            ->assertSee(route('admin.gifts.index'), false)
            ->assertSee(route('admin.orders.index'), false)
            ->assertSee(route('admin.enquiries.index'), false)
            ->assertSee(route('admin.channels.index'), false);

        $this->get(route('admin.gifts.index'))
            ->assertOk()
            ->assertSee('All products')
            ->assertSee('<table', false);

        $this->get(route('admin.enquiries.index'))
            ->assertOk()
            ->assertSee('Gift enquiry inbox');

        $this->get(route('admin.orders.index'))
            ->assertOk()
            ->assertSee('Customer orders');

        $this->get(route('admin.channels.index'))
            ->assertOk()
            ->assertSee('Social channels');
    }

    public function test_admin_can_create_edit_search_and_delete_gifts_from_product_pages(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        $this->get(route('admin.gifts.create'))
            ->assertOk()
            ->assertSee('Add a gift');

        $this->post('/admin/gifts', [
            'name' => 'Personalised mug',
            'category' => 'love',
            'price' => '499.00',
            'description' => 'Add a name or message',
            'image_url' => 'https://example.test/mug.jpg',
            'is_active' => '1',
        ])->assertRedirect(route('admin.gifts.index'));

        $gift = Gift::query()->where('name', 'Personalised mug')->firstOrFail();

        $this->assertDatabaseHas('gifts', [
            'id' => $gift->id,
            'category' => 'love',
            'price' => '499.00',
            'is_active' => 1,
        ]);

        $this->get(route('admin.gifts.index'))
            ->assertOk()
            ->assertSee('Personalised mug')
            ->assertSee('href="'.route('admin.gifts.edit', $gift).'"', false);

        Gift::create([
            'name' => 'Corporate gift box',
            'category' => 'corporate',
            'is_active' => true,
        ]);

        $this->get(route('admin.gifts.index', ['search' => 'Personalised']))
            ->assertOk()
            ->assertSee('Personalised mug')
            ->assertDontSee('Corporate gift box');

        $this->get(route('admin.gifts.index', ['search' => 'Corporate Gifts']))
            ->assertOk()
            ->assertSee('Corporate gift box')
            ->assertDontSee('Personalised mug');

        $this->get(route('admin.gifts.edit', $gift))
            ->assertOk()
            ->assertSee('Edit gift')
            ->assertSee('value="Personalised mug"', false);

        $this->patch(route('admin.gifts.update', $gift), [
            'name' => 'Personalised mug updated',
            'category' => 'birthday',
            'price' => '549.00',
            'description' => 'Add a name or message',
            'image_url' => 'https://example.test/mug.jpg',
            'is_active' => '1',
        ])->assertRedirect(route('admin.gifts.index'));

        $this->assertDatabaseHas('gifts', [
            'id' => $gift->id,
            'name' => 'Personalised mug updated',
            'category' => 'birthday',
            'price' => '549.00',
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

        $this->delete(route('admin.gifts.destroy', $gift))
            ->assertRedirect(route('admin.gifts.index'));

        $this->assertDatabaseMissing('gifts', ['id' => $gift->id]);
    }

    public function test_admin_can_upload_and_remove_multiple_product_images(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        $this->post(route('admin.gifts.store'), [
            'name' => 'Photo Gift Box',
            'category' => 'birthday',
            'price' => '1299.00',
            'images' => [
                UploadedFile::fake()->image('gift-front.jpg'),
                UploadedFile::fake()->image('gift-detail.jpg'),
            ],
            'is_active' => '1',
        ])->assertRedirect(route('admin.gifts.index'));

        $gift = Gift::query()->where('name', 'Photo Gift Box')->firstOrFail();
        $this->assertCount(2, $gift->images);
        foreach ($gift->images as $image) {
            $this->assertStringStartsWith('/storage/gifts/', $image);
            Storage::disk('public')->assertExists(Str::after($image, '/storage/'));
        }

        $this->get(route('admin.gifts.index'))
            ->assertOk()
            ->assertSee('2 photos');

        $this->get(route('admin.gifts.edit', $gift))
            ->assertOk()
            ->assertSee('Photo Gift Box photo 1')
            ->assertSee('Photo Gift Box photo 2');

        $this->get('/')
            ->assertOk()
            ->assertSee('₹1,299.00')
            ->assertSee('product-gallery-thumbnails', false)
            ->assertSee('data-gallery-image=', false)
            ->assertSee('storage/gifts/', false);

        $removedImage = $gift->images[0];
        $remainingImage = $gift->images[1];
        $this->patch(route('admin.gifts.update', $gift), [
            'name' => $gift->name,
            'category' => 'birthday',
            'price' => '1299.00',
            'remove_images' => [$removedImage],
            'images' => [UploadedFile::fake()->image('gift-back.jpg')],
            'is_active' => '1',
        ])->assertRedirect(route('admin.gifts.index'));

        $gift->refresh();
        $this->assertCount(2, $gift->images);
        $this->assertSame($remainingImage, $gift->images[0]);
        Storage::disk('public')->assertMissing(Str::after($removedImage, '/storage/'));
        Storage::disk('public')->assertExists(Str::after($gift->images[1], '/storage/'));

        $this->delete(route('admin.gifts.destroy', $gift))
            ->assertRedirect(route('admin.gifts.index'));

        Storage::disk('public')->assertMissing([
            Str::after($remainingImage, '/storage/'),
            Str::after($gift->images[1], '/storage/'),
        ]);
    }

    public function test_admin_rejects_non_image_product_uploads(): void
    {
        Storage::fake('public');

        $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->post(route('admin.gifts.store'), [
                'name' => 'Invalid upload',
                'category' => 'occasions',
                'images' => [UploadedFile::fake()->create('notes.txt', 1, 'text/plain')],
            ])
            ->assertSessionHasErrors([
                'images.0' => 'The images.0 field must be an image.',
            ]);

        $this->assertDatabaseMissing('gifts', ['name' => 'Invalid upload']);
        Storage::disk('public')->assertDirectoryEmpty('gifts');
    }

    public function test_admin_rejects_product_images_larger_than_five_megabytes(): void
    {
        Storage::fake('public');

        $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->post(route('admin.gifts.store'), [
                'name' => 'Oversized upload',
                'category' => 'occasions',
                'images' => [UploadedFile::fake()->image('large.jpg')->size(5121)],
            ])
            ->assertSessionHasErrors([
                'images.0' => 'The images.0 field must not be greater than 5120 kilobytes.',
            ]);

        $this->assertDatabaseMissing('gifts', ['name' => 'Oversized upload']);
        Storage::disk('public')->assertDirectoryEmpty('gifts');
    }

    public function test_admin_rejects_more_than_eight_product_images(): void
    {
        Storage::fake('public');
        $images = [];
        for ($index = 1; $index <= 9; $index++) {
            $images[] = UploadedFile::fake()->image("gift-{$index}.jpg");
        }

        $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->post(route('admin.gifts.store'), [
                'name' => 'Too many images',
                'category' => 'occasions',
                'images' => $images,
            ])
            ->assertSessionHasErrors([
                'images' => 'The images field must not have more than 8 items.',
            ]);

        $this->assertDatabaseMissing('gifts', ['name' => 'Too many images']);
        Storage::disk('public')->assertDirectoryEmpty('gifts');
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
        $this->assertCount(2, Gift::where('name', 'Love Note Gift Box')->firstOrFail()->images);

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
