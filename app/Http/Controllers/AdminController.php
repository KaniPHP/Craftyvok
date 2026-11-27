<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\Gift;
use App\Models\Order;
use App\Models\SocialPost;
use App\Models\User;
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
        $categoryCounts = Gift::query()
            ->selectRaw('category, COUNT(*) as gift_count')
            ->groupBy('category')
            ->pluck('gift_count', 'category');
        $totalGiftCount = $categoryCounts->sum();

        return view('admin.dashboard', [
            'pageTitle' => 'Store dashboard',
            'activePage' => 'overview',
            'conversations' => ChatConversation::query()
                ->with('messages')
                ->latest('last_message_at')
                ->take(4)
                ->get(),
            'gifts' => Gift::query()->latest()->take(5)->get(),
            'videoCount' => SocialPost::query()->where('is_highlight', false)->count(),
            'highlightCount' => SocialPost::query()->where('is_highlight', true)->count(),
            'categories' => collect(Gift::CATEGORIES)->map(function (string $label, string $category) use ($categoryCounts, $totalGiftCount): array {
                $count = (int) $categoryCounts->get($category, 0);

                return [
                    'key' => $category,
                    'label' => $label,
                    'count' => $count,
                    'percentage' => $totalGiftCount > 0 ? (int) round($count / $totalGiftCount * 100) : 0,
                ];
            }),
            'totalGiftCount' => $totalGiftCount,
            'orderCount' => Order::query()->count(),
            'conversationCount' => ChatConversation::query()->count(),
        ]);
    }

    public function enquiries(): View
    {
        return view('admin.enquiries', [
            'pageTitle' => 'Gift enquiry inbox',
            'activePage' => 'enquiries',
            'conversations' => ChatConversation::query()
                ->with('messages')
                ->latest('last_message_at')
                ->take(50)
                ->get(),
        ]);
    }

    public function orders(): View
    {
        return view('admin.orders', [
            'pageTitle' => 'Customer orders',
            'activePage' => 'orders',
            'orders' => Order::query()->with('user')->latest()->take(50)->get(),
            'orderCount' => Order::query()->count(),
        ]);
    }

    public function channels(): View
    {
        return view('admin.channels', [
            'pageTitle' => 'Social channels',
            'activePage' => 'channels',
            'videoCount' => SocialPost::query()->where('is_highlight', false)->whereIn('media_type', ['VIDEO', 'REELS'])->count(),
            'photoCount' => SocialPost::query()->where('is_highlight', false)->where('media_type', 'IMAGE')->count(),
            'highlightCount' => SocialPost::query()->where('is_highlight', true)->count(),
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
}
