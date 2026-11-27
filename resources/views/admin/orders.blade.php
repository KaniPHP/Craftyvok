@extends('admin.layout')

@section('content')
  <div class="admin-page-heading">
    <div>
      <p class="eyebrow"><span class="eyebrow-line"></span> DELIVERY UPDATES</p>
      <h1>Customer orders</h1>
      <p>Track gifts and keep customers updated on delivery.</p>
    </div>
    <span class="admin-section-count">{{ number_format($orderCount) }} orders</span>
  </div>

  <form method="post" action="{{ route('admin.orders.create') }}" class="admin-gift-form">
    @csrf
    <h3>Add an order to a customer account</h3>
    <p class="admin-help">The customer must have registered with the email you enter here to see this order in My Orders.</p>
    <div class="admin-form-grid">
      <label>Customer account email<input name="email" type="email" value="{{ old('email') }}" required></label>
      <label>Order number<input name="order_number" maxlength="40" value="{{ old('order_number') }}" placeholder="e.g. CV-2026-001" required></label>
      <label>Gift item<input name="gift_name" maxlength="160" value="{{ old('gift_name') }}" required></label>
      <label>Total (₹)<input name="total" type="number" min="0" step="0.01" value="{{ old('total') }}"></label>
      <label>Status<select name="status" required>@foreach(['confirmed', 'processing', 'shipped', 'out_for_delivery', 'delivered', 'cancelled'] as $status)<option value="{{ $status }}" @selected(old('status', 'confirmed') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>@endforeach</select></label>
      <label>Estimated delivery<input name="estimated_delivery" type="date" value="{{ old('estimated_delivery') }}"></label>
      <label class="admin-wide">Delivery note<textarea name="tracking_note" rows="2" maxlength="2000" placeholder="Optional tracking or delivery update">{{ old('tracking_note') }}</textarea></label>
    </div>
    <button class="button button-primary" type="submit">Add tracked order</button>
  </form>

  <section class="admin-section">
    <div class="admin-section-title">
      <div><p class="admin-panel-kicker">ORDER HISTORY</p><h2>Recent orders</h2></div>
      <span class="admin-section-count">{{ number_format($orderCount) }} total</span>
    </div>
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
@endsection
