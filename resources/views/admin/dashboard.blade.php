<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Craftyvok admin</title>
  <link rel="stylesheet" href="{{ asset('styles.css') }}">
</head>
<body class="admin-page">
  <header class="admin-header">
    <a class="brand" href="{{ route('home') }}">
      <img class="brand-logo" src="{{ asset('images/craftyvok-logo.jpg') }}" alt="Craftyvok logo">
      <span class="brand-name">craftyvok<small>GIFT ADMIN</small></span>
    </a>
    <form method="post" action="{{ route('admin.logout') }}">@csrf<button class="admin-logout" type="submit">Sign out</button></form>
  </header>
  <main class="admin-main">
    <div class="admin-title-row"><div><p class="eyebrow"><span class="eyebrow-line"></span> YOUR STOREFRONT</p><h1>Good to see you.</h1></div><a class="text-link" href="{{ route('home') }}" target="_blank">View website ↗</a></div>
    @if(session('status'))<div class="admin-success" role="status">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="admin-alert" role="alert">{{ $errors->first() }}</div>@endif

    <section class="admin-stats" aria-label="Store overview">
      <article><strong>{{ $conversations->count() }}</strong><span>Recent chats</span></article>
      <article><strong>{{ $gifts->count() }}</strong><span>Gift items</span></article>
      <article><strong>{{ $videoCount }}</strong><span>Instagram videos</span></article>
      <article><strong>{{ $highlightCount }}</strong><span>Highlights</span></article>
    </section>

    <section class="admin-section" id="inbox">
      <div class="admin-section-title"><div><p class="eyebrow"><span class="eyebrow-line"></span> CUSTOMER QUESTIONS</p><h2>Gift enquiry inbox</h2></div></div>
      @forelse($conversations as $conversation)
        <article class="admin-chat-card">
          <div class="admin-chat-meta"><strong>{{ $conversation->item_name }}</strong><span>{{ $conversation->status }} · {{ $conversation->last_message_at?->format('d M, g:i a') }}</span></div>
          <div class="admin-chat-messages">
            @foreach($conversation->messages as $message)
              <p class="admin-message admin-message-{{ $message->sender }}"><strong>{{ ucfirst($message->sender) }}:</strong> {{ $message->body }}</p>
            @endforeach
          </div>
          <form method="post" action="{{ route('admin.chat.reply', $conversation) }}" class="admin-reply-form">
            @csrf
            <label class="sr-only" for="reply-{{ $conversation->id }}">Reply to {{ $conversation->item_name }}</label>
            <input id="reply-{{ $conversation->id }}" name="message" maxlength="2000" placeholder="Type a price or reply…" required>
            <button type="submit">Send reply</button>
          </form>
        </article>
      @empty
        <p class="admin-empty">New customer chats will appear here.</p>
      @endforelse
    </section>

    <section class="admin-section">
      <div class="admin-section-title"><div><p class="eyebrow"><span class="eyebrow-line"></span> YOUR CATALOGUE</p><h2>Gift items &amp; prices</h2></div></div>
      <form method="post" action="{{ route('admin.gifts.create') }}" class="admin-gift-form">
        @csrf
        <h3>Add a gift item</h3>
        <div class="admin-form-grid">
          <label>Gift name<input name="name" maxlength="160" required></label>
          <label>Menu category<select name="category" required><option value="love">Love</option><option value="birthday">Birthday Gifts</option><option value="occasions" selected>Occasions</option><option value="relationship">Relationship</option><option value="corporate">Corporate</option><option value="greetings">Greetings</option></select></label>
          <label>Price (₹)<input name="price" type="number" min="0" step="0.01" placeholder="Leave blank to quote in chat"></label>
          <label class="admin-wide">Description<textarea name="description" rows="2" maxlength="2000"></textarea></label>
          <label class="admin-wide">Image URL<input name="image_url" type="url" placeholder="https://…"></label>
          <label class="admin-checkbox"><input type="checkbox" name="is_active" value="1" checked> Show on website</label>
        </div>
        <button class="button button-primary" type="submit">Add gift</button>
      </form>
      <div class="admin-gift-list">
        @forelse($gifts as $gift)
          <article class="admin-gift-item">
            <form method="post" action="{{ route('admin.gifts.update', $gift) }}" class="admin-form-grid">
              @csrf @method('PATCH')
              <label>Gift name<input name="name" value="{{ $gift->name }}" maxlength="160" required></label>
              <label>Menu category<select name="category" required>@foreach(['love' => 'Love', 'birthday' => 'Birthday Gifts', 'occasions' => 'Occasions', 'relationship' => 'Relationship', 'corporate' => 'Corporate', 'greetings' => 'Greetings'] as $value => $label)<option value="{{ $value }}" @selected($gift->category === $value)>{{ $label }}</option>@endforeach</select></label>
              <label>Price (₹)<input name="price" type="number" min="0" step="0.01" value="{{ $gift->price }}"></label>
              <label class="admin-wide">Description<textarea name="description" rows="2" maxlength="2000">{{ $gift->description }}</textarea></label>
              <label class="admin-wide">Image URL<input name="image_url" type="url" value="{{ $gift->image_url }}"></label>
              <label class="admin-checkbox"><input type="checkbox" name="is_active" value="1" @checked($gift->is_active)> Show on website</label>
              <div class="admin-gift-actions"><button type="submit">Save changes</button></div>
            </form>
            <form method="post" action="{{ route('admin.gifts.delete', $gift) }}" onsubmit="return confirm('Remove this gift from the catalogue?')">
              @csrf @method('DELETE')<button class="admin-delete" type="submit">Remove gift</button>
            </form>
          </article>
        @empty
          <p class="admin-empty">Add products here to replace the sample categories on your homepage.</p>
        @endforelse
      </div>
    </section>

    <section class="admin-section" id="orders">
      <div class="admin-section-title"><div><p class="eyebrow"><span class="eyebrow-line"></span> DELIVERY UPDATES</p><h2>Customer orders</h2></div></div>
      <form method="post" action="{{ route('admin.orders.create') }}" class="admin-gift-form">
        @csrf
        <h3>Add an order to a customer account</h3>
        <p class="admin-help">The customer must have registered with the email you enter here to see this order in My Orders.</p>
        <div class="admin-form-grid">
          <label>Customer account email<input name="email" type="email" required></label>
          <label>Order number<input name="order_number" maxlength="40" placeholder="e.g. CV-2026-001" required></label>
          <label>Gift item<input name="gift_name" maxlength="160" required></label>
          <label>Total (₹)<input name="total" type="number" min="0" step="0.01"></label>
          <label>Status<select name="status" required><option value="confirmed">Confirmed</option><option value="processing">Processing</option><option value="shipped">Shipped</option><option value="out_for_delivery">Out for delivery</option><option value="delivered">Delivered</option><option value="cancelled">Cancelled</option></select></label>
          <label>Estimated delivery<input name="estimated_delivery" type="date"></label>
          <label class="admin-wide">Delivery note<textarea name="tracking_note" rows="2" maxlength="2000" placeholder="Optional tracking or delivery update"></textarea></label>
        </div>
        <button class="button button-primary" type="submit">Add tracked order</button>
      </form>
      <div class="admin-order-list">
        @forelse($orders as $order)
          <article class="admin-gift-item">
            <div class="admin-chat-meta"><strong>{{ $order->order_number }} · {{ $order->gift_name }}</strong><span>{{ $order->user->email }}</span></div>
            <form method="post" action="{{ route('admin.orders.update', $order) }}" class="admin-form-grid admin-order-update">
              @csrf @method('PATCH')
              <label>Status<select name="status" required>@foreach(['confirmed', 'processing', 'shipped', 'out_for_delivery', 'delivered', 'cancelled'] as $status)<option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>@endforeach</select></label>
              <label>Estimated delivery<input name="estimated_delivery" type="date" value="{{ $order->estimated_delivery?->format('Y-m-d') }}"></label>
              <label class="admin-wide">Delivery note<textarea name="tracking_note" rows="2" maxlength="2000">{{ $order->tracking_note }}</textarea></label>
              <div class="admin-gift-actions"><button type="submit">Update tracking</button></div>
            </form>
          </article>
        @empty
          <p class="admin-empty">Add an order here and the customer will see its tracking updates in their account.</p>
        @endforelse
      </div>
    </section>

    <section class="admin-section admin-integration-note">
      <p class="eyebrow"><span class="eyebrow-line"></span> CONNECTED CHANNELS</p>
      <h2>Instagram &amp; Google</h2>
      <p>Instagram videos and highlights sync every 30 minutes after you add your Meta Graph API account details to <code>.env</code> and keep Laravel’s scheduler running. To sync once now, run <code>php artisan craftyvok:sync-instagram</code>.</p>
      <p>Google reviews use your official review link. Add it as <code>GOOGLE_REVIEW_URL</code> in <code>.env</code>; customers then post directly to Google.</p>
    </section>
  </main>
</body>
</html>
