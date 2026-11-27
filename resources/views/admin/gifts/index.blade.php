@extends('admin.layout')

@section('content')
  <div class="admin-page-heading">
    <div>
      <p class="eyebrow"><span class="eyebrow-line"></span> YOUR CATALOGUE</p>
      <h1>Gift products</h1>
      <p>Manage the gifts and prices displayed on your storefront.</p>
    </div>
    <a class="button button-primary admin-add-product" href="{{ route('admin.gifts.create') }}">+ Add a gift</a>
  </div>

  <section class="admin-panel admin-product-manager">
    <div class="admin-table-toolbar">
      <div><h2>All products</h2><span>{{ number_format($gifts->total()) }} products</span></div>
      <form method="get" action="{{ route('admin.gifts.index') }}" class="admin-product-search">
        <label class="sr-only" for="product-search">Search products</label>
        <input id="product-search" name="search" type="search" value="{{ $search }}" maxlength="160" placeholder="Search name or category">
        <button type="submit">Search</button>
        @if($search !== '')
          <a href="{{ route('admin.gifts.index') }}">Clear</a>
        @endif
      </form>
    </div>

    <div class="admin-table-wrap">
      <table class="admin-products-table admin-product-manager-table">
        <thead>
          <tr><th>Product</th><th>Category</th><th>Price</th><th>Website status</th><th>Added</th><th>Actions</th></tr>
        </thead>
        <tbody>
          @forelse($gifts as $gift)
            <tr>
              <td>
                <a class="admin-product-name" href="{{ route('admin.gifts.edit', $gift) }}">
                  @if($gift->image_gallery !== [])
                    <img src="{{ filter_var($gift->image_gallery[0], FILTER_VALIDATE_URL) ? $gift->image_gallery[0] : asset(ltrim($gift->image_gallery[0], '/')) }}" alt="">
                  @else
                    <span class="admin-product-placeholder">🎁</span>
                  @endif
                  <span><strong>{{ $gift->name }}</strong><small>{{ count($gift->image_gallery) }} {{ count($gift->image_gallery) === 1 ? 'photo' : 'photos' }}</small></span>
                </a>
              </td>
              <td>{{ $categories[$gift->category] ?? ucfirst($gift->category) }}</td>
              <td>{{ $gift->price ? '₹'.number_format((float) $gift->price, 2) : 'Ask for price' }}</td>
              <td><span class="admin-product-status {{ $gift->is_active ? 'is-visible' : 'is-hidden' }}">{{ $gift->is_active ? 'Live' : 'Hidden' }}</span></td>
              <td>{{ $gift->created_at?->format('d M Y') }}</td>
              <td>
                <div class="admin-product-actions">
                  <a class="admin-row-action" href="{{ route('admin.gifts.edit', $gift) }}">Edit</a>
                  <form method="post" action="{{ route('admin.gifts.destroy', $gift) }}" onsubmit="return confirm('Remove this gift from the catalogue?')">
                    @csrf @method('DELETE')
                    <button class="admin-delete" type="submit">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="6"><p class="admin-empty">{{ $search !== '' ? 'No products match your search.' : 'No products yet. Add your first gift to get started.' }}</p></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="admin-pagination">
      <span>Showing {{ $gifts->firstItem() ?? 0 }}–{{ $gifts->lastItem() ?? 0 }} of {{ $gifts->total() }} products</span>
      <div>
        @if($gifts->onFirstPage())
          <span class="is-disabled">Previous</span>
        @else
          <a href="{{ $gifts->previousPageUrl() }}">Previous</a>
        @endif
        @if($gifts->hasMorePages())
          <a href="{{ $gifts->nextPageUrl() }}">Next</a>
        @else
          <span class="is-disabled">Next</span>
        @endif
      </div>
    </div>
  </section>
@endsection
