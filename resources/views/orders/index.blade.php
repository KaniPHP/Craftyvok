<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My orders — Craftyvok</title>
  <link rel="stylesheet" href="{{ asset('styles.css') }}">
</head>
<body class="admin-page">
  <header class="account-header">
    <a class="brand" href="{{ route('home') }}">
      <img class="brand-logo" src="{{ asset('images/craftyvok-logo.jpg') }}" alt="Craftyvok logo">
      <span class="brand-name">craftyvok<small>GIFTS WITH A PERSONAL TOUCH</small></span>
    </a>
    <div class="account-header-actions">
      <span>{{ auth()->user()->name }}</span>
      <form method="post" action="{{ route('logout') }}">@csrf<button class="admin-logout" type="submit">Logout</button></form>
    </div>
  </header>
  <main class="account-page orders-page">
    <div class="orders-heading">
      <div><p class="eyebrow"><span class="eyebrow-line"></span> YOUR GIFT JOURNEY</p><h1>My Orders</h1></div>
      <a class="text-link" href="{{ route('home') }}#gifts">Explore gifts <span aria-hidden="true">↗</span></a>
    </div>
    <p class="orders-intro">See your order status and delivery updates here.</p>
    @forelse($orders as $order)
      <article class="order-card">
        <div class="order-card-top">
          <div><span class="order-kicker">ORDER {{ $order->order_number }}</span><h2>{{ $order->gift_name }}</h2></div>
          <span class="order-status order-status-{{ $order->status }}">{{ str_replace('_', ' ', $order->status) }}</span>
        </div>
        <div class="order-details">
          <p>Placed <strong>{{ $order->created_at->format('d M Y') }}</strong></p>
          @if($order->total !== null)<p>Total <strong>₹{{ number_format((float) $order->total, 2) }}</strong></p>@endif
          @if($order->estimated_delivery)<p>Estimated delivery <strong>{{ $order->estimated_delivery->format('d M Y') }}</strong></p>@endif
        </div>
        @if($order->tracking_note)<p class="order-note">{{ $order->tracking_note }}</p>@endif
        <div class="order-progress" aria-label="Order progress">
          @php($statusRank = array_search($order->status, ['confirmed', 'processing', 'shipped', 'out_for_delivery', 'delivered'], true))
          @foreach(['confirmed', 'processing', 'shipped', 'out_for_delivery', 'delivered'] as $step)
            <span class="order-progress-step {{ $statusRank !== false && $statusRank >= $loop->index ? 'is-complete' : '' }}">{{ str_replace('_', ' ', $step) }}</span>
          @endforeach
        </div>
      </article>
    @empty
      <section class="orders-empty">
        <span aria-hidden="true">🎁</span>
        <h2>No orders just yet</h2>
        <p>Once Craftyvok adds an order to your account, its progress and delivery details will appear here.</p>
        <a class="button button-primary" href="{{ route('home') }}#gifts">Explore gifts <span aria-hidden="true">→</span></a>
      </section>
    @endforelse
  </main>
</body>
</html>
