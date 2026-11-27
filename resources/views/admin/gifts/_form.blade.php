@php($giftImages = $gift?->image_gallery ?? [])
<div class="admin-form-grid">
  <label>Gift name<input name="name" value="{{ old('name', $gift?->name ?? '') }}" maxlength="160" required></label>
  <label>Menu category<select name="category" required>
    @foreach($categories as $key => $label)
      <option value="{{ $key }}" @selected(old('category', $gift?->category ?? 'occasions') === $key)>{{ $label }}</option>
    @endforeach
  </select></label>
  <label>Price (₹)<input name="price" type="number" min="0" step="0.01" value="{{ old('price', $gift?->price ?? '') }}" placeholder="Leave blank to quote in chat"></label>
  <label class="admin-wide">Description<textarea name="description" rows="3" maxlength="2000">{{ old('description', $gift?->description ?? '') }}</textarea></label>
  <label class="admin-wide">Upload product photos
    <input name="images[]" type="file" accept="image/jpeg,image/png,image/webp" multiple>
    <span class="admin-help">Choose up to 8 JPG, PNG, or WebP images, up to 5 MB each.</span>
  </label>
  @if($giftImages !== [])
    <div class="admin-wide admin-image-gallery">
      <span class="admin-gallery-label">Current photos — select any to remove when you save</span>
      <div class="admin-image-gallery-grid">
        @foreach($giftImages as $image)
          <label class="admin-image-choice">
            <img src="{{ filter_var($image, FILTER_VALIDATE_URL) ? $image : asset(ltrim($image, '/')) }}" alt="{{ $gift?->name }} photo {{ $loop->iteration }}">
            <span><input type="checkbox" name="remove_images[]" value="{{ $image }}" @checked(in_array($image, old('remove_images', []), true))> Remove</span>
          </label>
        @endforeach
      </div>
    </div>
  @endif
  <label class="admin-wide">Add an image URL (optional)<input name="image_url" type="text" inputmode="url" value="{{ old('image_url') }}" placeholder="https://… or /img/photo.jpg"></label>
  <label class="admin-checkbox"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $gift?->is_active ?? true))> Show on website</label>
</div>
