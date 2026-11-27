@extends('admin.layout')

@section('content')
  <div class="admin-page-heading">
    <div>
      <p class="eyebrow"><span class="eyebrow-line"></span> YOUR CATALOGUE</p>
      <h1>Edit gift</h1>
      <p>Update product details, pricing, or storefront visibility.</p>
    </div>
    <a class="admin-view-store" href="{{ route('admin.gifts.index') }}">← Back to products</a>
  </div>

  <form method="post" action="{{ route('admin.gifts.update', $gift) }}" class="admin-gift-form admin-product-form" enctype="multipart/form-data">
    @csrf @method('PATCH')
    @include('admin.gifts._form')
    <div class="admin-form-footer">
      <a class="admin-view-store" href="{{ route('admin.gifts.index') }}">Cancel</a>
      <button class="button button-primary" type="submit">Save changes</button>
    </div>
  </form>
@endsection
