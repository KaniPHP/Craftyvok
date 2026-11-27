@extends('admin.layout')

@section('content')
  <div class="admin-page-heading">
    <div>
      <p class="eyebrow"><span class="eyebrow-line"></span> YOUR CATALOGUE</p>
      <h1>Add a gift</h1>
      <p>Enter the details customers will see on your storefront.</p>
    </div>
    <a class="admin-view-store" href="{{ route('admin.gifts.index') }}">← Back to products</a>
  </div>

  <form method="post" action="{{ route('admin.gifts.store') }}" class="admin-gift-form admin-product-form" enctype="multipart/form-data">
    @csrf
    @include('admin.gifts._form')
    <div class="admin-form-footer">
      <a class="admin-view-store" href="{{ route('admin.gifts.index') }}">Cancel</a>
      <button class="button button-primary" type="submit">Add gift</button>
    </div>
  </form>
@endsection
