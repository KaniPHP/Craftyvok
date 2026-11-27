@extends('admin.layout')

@section('content')
  <section class="admin-overview">
    <div class="admin-page-heading">
      <div>
        <p class="eyebrow"><span class="eyebrow-line"></span> YOUR STOREFRONT</p>
        <h1>Good to see you, {{ Str::before(auth()->user()->name, ' ') }}!</h1>
        <p>Here’s what’s happening with your gift store today.</p>
      </div>
      <a class="admin-view-store" href="{{ route('home') }}" target="_blank" rel="noopener">View website <span>↗</span></a>
    </div>

    <div class="admin-stats" aria-label="Store overview">
      <article class="admin-stat-card admin-stat-red">
        <span class="admin-stat-icon">▦</span><span class="admin-stat-label">Total products</span>
        <strong>{{ number_format($totalGiftCount) }}</strong><small>Across all collections</small>
      </article>
      <article class="admin-stat-card admin-stat-yellow">
        <span class="admin-stat-icon">▤</span><span class="admin-stat-label">Customer orders</span>
        <strong>{{ number_format($orderCount) }}</strong><small>Orders in your store</small>
      </article>
      <article class="admin-stat-card admin-stat-cream">
        <span class="admin-stat-icon">✉</span><span class="admin-stat-label">Gift enquiries</span>
        <strong>{{ number_format($conversationCount) }}</strong><small>Customer conversations</small>
      </article>
      <article class="admin-stat-card admin-stat-pink">
        <span class="admin-stat-icon">◎</span><span class="admin-stat-label">Instagram videos</span>
        <strong>{{ number_format($videoCount) }}</strong><small>{{ number_format($highlightCount) }} highlights</small>
      </article>
    </div>

    <div class="admin-overview-grid">
      <section class="admin-panel admin-category-panel">
        <div class="admin-panel-heading">
          <div><p class="admin-panel-kicker">CATALOGUE</p><h2>Products by category</h2></div>
          <a href="{{ route('admin.gifts.index') }}">Manage products <span>→</span></a>
        </div>
        <div class="admin-category-chart">
          @foreach($categories as $category)
            <div class="admin-category-row">
              <div class="admin-category-label"><span>{{ $category['label'] }}</span><strong>{{ number_format($category['count']) }}</strong></div>
              <div class="admin-category-track" role="img" aria-label="{{ $category['label'] }}: {{ $category['count'] }} products">
                <span style="width: {{ $category['percentage'] }}%"></span>
              </div>
            </div>
          @endforeach
        </div>
        <p class="admin-chart-note">A live view of gifts currently in your catalogue.</p>
      </section>

      <section class="admin-panel admin-latest-enquiries">
        <div class="admin-panel-heading">
          <div><p class="admin-panel-kicker">CUSTOMER QUESTIONS</p><h2>Latest enquiries</h2></div>
          <a href="{{ route('admin.enquiries.index') }}">Open inbox <span>→</span></a>
        </div>
        <div class="admin-latest-list">
          @forelse($conversations as $conversation)
            <a class="admin-latest-row" href="{{ route('admin.enquiries.index') }}">
              <span class="admin-enquiry-avatar">{{ mb_substr($conversation->item_name, 0, 1) }}</span>
              <span class="admin-latest-copy"><strong>{{ $conversation->item_name }}</strong><small>{{ Str::limit($conversation->messages->last()?->body ?? 'New enquiry', 52) }}</small></span>
              <span class="admin-enquiry-status admin-enquiry-{{ $conversation->status }}">{{ ucfirst($conversation->status) }}</span>
            </a>
          @empty
            <p class="admin-empty">New customer chats will appear here.</p>
          @endforelse
        </div>
      </section>
    </div>

    <section class="admin-panel admin-products-preview">
      <div class="admin-panel-heading">
        <div><p class="admin-panel-kicker">YOUR CATALOGUE</p><h2>Recently added gifts</h2></div>
        <a href="{{ route('admin.gifts.index') }}">View all products <span>→</span></a>
      </div>
      <div class="admin-table-wrap">
        <table class="admin-products-table">
          <thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Visibility</th><th></th></tr></thead>
          <tbody>
            @forelse($gifts as $gift)
              <tr>
                <td><a class="admin-product-name" href="{{ route('admin.gifts.edit', $gift) }}">
                  @if($gift->image_url)
                    <img src="{{ str_starts_with($gift->image_url, '/') ? asset(ltrim($gift->image_url, '/')) : $gift->image_url }}" alt="">
                  @else
                    <span class="admin-product-placeholder">🎁</span>
                  @endif
                  <strong>{{ $gift->name }}</strong>
                </a></td>
                <td>{{ $categories->firstWhere('key', $gift->category)['label'] ?? ucfirst($gift->category) }}</td>
                <td>{{ $gift->price ? '₹'.number_format((float) $gift->price, 2) : 'Ask for price' }}</td>
                <td><span class="admin-product-status {{ $gift->is_active ? 'is-visible' : 'is-hidden' }}">{{ $gift->is_active ? 'Live' : 'Hidden' }}</span></td>
                <td><a class="admin-row-action" href="{{ route('admin.gifts.edit', $gift) }}" aria-label="Edit {{ $gift->name }}">Edit →</a></td>
              </tr>
            @empty
              <tr><td colspan="5"><p class="admin-empty">Add your first gift to see it here.</p></td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>
  </section>
@endsection
