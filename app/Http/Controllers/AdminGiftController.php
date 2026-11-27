<?php

namespace App\Http\Controllers;

use App\Models\Gift;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class AdminGiftController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:160'],
        ]);
        $search = trim($validated['search'] ?? '');
        $normalizedSearch = mb_strtolower($search);
        $categoryKeys = collect(Gift::CATEGORIES)
            ->filter(function (string $label, string $category) use ($normalizedSearch): bool {
                return str_contains(mb_strtolower($label), $normalizedSearch)
                    || str_contains(mb_strtolower($category), $normalizedSearch);
            })
            ->keys()
            ->all();

        $gifts = Gift::query()
            ->when($search !== '', function (Builder $query) use ($search, $categoryKeys): void {
                $query->where(function (Builder $query) use ($search, $categoryKeys): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%")
                        ->orWhereIn('category', $categoryKeys);
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.gifts.index', [
            'pageTitle' => 'Products',
            'activePage' => 'products',
            'categories' => Gift::CATEGORIES,
            'gifts' => $gifts,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.gifts.create', [
            'pageTitle' => 'Add gift',
            'activePage' => 'products',
            'categories' => Gift::CATEGORIES,
            'gift' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedGift($request);
        $data = $this->withImageGallery($data);
        $data['is_active'] = $request->boolean('is_active');
        Gift::create($data);

        return redirect()->route('admin.gifts.index')->with('status', 'Gift added to the storefront.');
    }

    public function edit(Gift $gift): View
    {
        return view('admin.gifts.edit', [
            'pageTitle' => 'Edit gift',
            'activePage' => 'products',
            'categories' => Gift::CATEGORIES,
            'gift' => $gift,
        ]);
    }

    public function update(Request $request, Gift $gift): RedirectResponse
    {
        $data = $this->validatedGift($request);
        $removedImages = array_values(array_intersect(
            $gift->image_gallery,
            $data['remove_images'] ?? [],
        ));
        $data = $this->withImageGallery($data, $gift);
        $data['is_active'] = $request->boolean('is_active');
        $gift->update($data);
        $this->deleteStoredImages($removedImages);

        return redirect()->route('admin.gifts.index')->with('status', 'Gift updated.');
    }

    public function destroy(Gift $gift): RedirectResponse
    {
        $this->deleteStoredImages($gift->image_gallery);
        $gift->delete();

        return redirect()->route('admin.gifts.index')->with('status', 'Gift removed from the storefront.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedGift(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'category' => ['required', Rule::in(array_keys(Gift::CATEGORIES))],
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
            'images' => ['nullable', 'array', 'max:8'],
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['required', 'string', 'max:2048', 'distinct'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function withImageGallery(array $validated, ?Gift $gift = null): array
    {
        $imagesToRemove = $validated['remove_images'] ?? [];
        $images = collect($gift?->image_gallery ?? [])
            ->reject(fn (string $image): bool => in_array($image, $imagesToRemove, true))
            ->values();

        $imageUrl = $validated['image_url'] ?? null;
        if (is_string($imageUrl) && $imageUrl !== '' && ! $images->contains($imageUrl)) {
            $images->push($imageUrl);
        }

        foreach ($validated['images'] ?? [] as $uploadedImage) {
            $path = $uploadedImage->storePublicly('gifts', 'public');
            if (! is_string($path)) {
                throw new RuntimeException('Unable to store the uploaded gift image.');
            }

            $images->push('/storage/'.$path);
        }

        unset($validated['remove_images']);
        $validated['images'] = $images->values()->all();
        $validated['image_url'] = $images->first();

        return $validated;
    }

    /**
     * @param  array<int, string>  $images
     */
    private function deleteStoredImages(array $images): void
    {
        $disk = Storage::disk('public');

        foreach ($images as $image) {
            if (! str_starts_with($image, '/storage/gifts/')) {
                continue;
            }

            $storagePath = rawurldecode(substr($image, strlen('/storage/')));
            if (! preg_match('/\Agifts\/[A-Za-z0-9._-]+\z/', $storagePath)) {
                continue;
            }

            if ($disk->exists($storagePath) && ! $disk->delete($storagePath)) {
                throw new RuntimeException('Unable to remove a gift image from public storage.');
            }
        }
    }
}
