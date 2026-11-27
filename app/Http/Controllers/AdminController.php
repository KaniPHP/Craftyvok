<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\Gift;
use App\Models\Order;
use App\Models\SocialPost;
use App\Models\User;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function loginForm(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials) && Auth::user()->is_admin) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        Auth::logout();

        return back()->withErrors([
            'email' => 'Those admin sign-in details did not match.',
        ])->onlyInput('email');
    }

    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'conversations' => ChatConversation::query()
                ->with('messages')
                ->latest('last_message_at')
                ->take(50)
                ->get(),
            'gifts' => Gift::query()->latest()->get(),
            'videoCount' => SocialPost::query()->where('is_highlight', false)->count(),
            'highlightCount' => SocialPost::query()->where('is_highlight', true)->count(),
            'orders' => Order::query()->with('user')->latest()->take(50)->get(),
        ]);
    }

    public function createOrder(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'order_number' => ['required', 'string', 'max:40', 'unique:orders,order_number'],
            'gift_name' => ['required', 'string', 'max:160'],
            'total' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'status' => ['required', 'in:confirmed,processing,shipped,out_for_delivery,delivered,cancelled'],
            'estimated_delivery' => ['nullable', 'date'],
            'tracking_note' => ['nullable', 'string', 'max:2000'],
        ]);
        $user = User::query()->where('email', $data['email'])->firstOrFail();
        unset($data['email']);

        $user->orders()->create($data);

        return back()->with('status', 'Order added to the customer’s order history.');
    }

    public function updateOrder(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:confirmed,processing,shipped,out_for_delivery,delivered,cancelled'],
            'estimated_delivery' => ['nullable', 'date'],
            'tracking_note' => ['nullable', 'string', 'max:2000'],
        ]);
        $order->update($data);

        return back()->with('status', 'Order tracking information updated.');
    }

    public function createGift(Request $request): RedirectResponse
    {
        $data = $this->validatedGift($request);
        $data['is_active'] = $request->boolean('is_active');
        Gift::create($data);

        return back()->with('status', 'Gift added to the storefront.');
    }

    public function updateGift(Request $request, Gift $gift): RedirectResponse
    {
        $data = $this->validatedGift($request);
        $data['is_active'] = $request->boolean('is_active');
        $gift->update($data);

        return back()->with('status', 'Gift updated.');
    }

    public function deleteGift(Gift $gift): RedirectResponse
    {
        $gift->delete();

        return back()->with('status', 'Gift removed from the storefront.');
    }

    public function reply(Request $request, ChatConversation $conversation): RedirectResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $conversation->messages()->create([
            'sender' => 'admin',
            'body' => $data['message'],
        ]);
        $conversation->update([
            'status' => 'answered',
            'last_message_at' => now(),
        ]);

        return back()->with('status', 'Reply sent in the customer chat.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    private function validatedGift(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'category' => ['required', 'in:love,birthday,occasions,relationship,corporate,greetings'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'image_url' => [
                'nullable',
                'string',
                'max:2048',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (! is_string($value) || $value === '') {
                        return;
                    }

                    $isLocalImage = preg_match('/\A\/img\/[A-Za-z0-9._() %\-]+\z/', $value) === 1;
                    if ($isLocalImage) {
                        $localPath = rawurldecode(ltrim($value, '/'));

                        if (! str_contains($localPath, '..') && is_file(public_path($localPath))) {
                            return;
                        }
                    }

                    $scheme = parse_url($value, PHP_URL_SCHEME);
                    $isHttpUrl = filter_var($value, FILTER_VALIDATE_URL) !== false
                        && in_array($scheme, ['http', 'https'], true);

                    if (! $isHttpUrl) {
                        $fail('The image must be an HTTP(S) URL or an existing image path under /img/.');
                    }
                },
            ],
        ]);
    }
}
