<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }
        if (!is_null($request->get('active'))) {
            $query->where('is_active', (bool) $request->boolean('active'));
        }

        $products = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:car,tour'],
            'description' => ['nullable', 'string'],
            'itinerary' => ['nullable', 'string'],
            'cancellation_policy' => ['nullable', 'string'],
            'meeting_point_name' => ['nullable', 'string', 'max:255'],
            'meeting_point_address' => ['nullable', 'string', 'max:1000'],
            'meeting_point_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'meeting_point_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'meeting_point_embed_url' => ['nullable', 'url'],
            'gallery_image_urls' => ['nullable', 'string'],
            'gallery_images_upload' => ['nullable', 'array'],
            'gallery_images_upload.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'add_ons_input' => ['nullable', 'string'],
            'car_model' => ['nullable', 'string', 'max:255', 'required_if:type,car'],
            'max_passengers' => ['nullable', 'integer', 'min:1', 'required_if:type,car'],
            'max_luggage' => ['nullable', 'integer', 'min:0', 'required_if:type,car'],
            'destination' => ['nullable', 'string', 'max:255', 'required_if:type,tour'],
            'duration' => ['nullable', 'string', 'max:255', 'required_if:type,tour'],
            'includes_lunch' => ['nullable', 'boolean'],
            'includes_pickup' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'image_url' => ['nullable', 'string', 'max:2048'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'discounted_price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:3'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['currency'] = $validated['currency'] ?? 'THB';
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);
        $validated['is_featured'] = (bool) ($validated['is_featured'] ?? false);
        $validated['includes_lunch'] = (bool) ($validated['includes_lunch'] ?? false);
        $validated['includes_pickup'] = (bool) ($validated['includes_pickup'] ?? false);

        if ($validated['type'] === 'car') {
            $validated['destination'] = null;
            $validated['duration'] = null;
            $validated['includes_lunch'] = false;
            $validated['includes_pickup'] = false;
        }

        if ($validated['type'] === 'tour') {
            $validated['car_model'] = null;
            $validated['max_passengers'] = null;
            $validated['max_luggage'] = null;
        }

        $validated['add_ons'] = $this->parseAddOns($request->input('add_ons_input'));
        $validated['gallery_images'] = $this->collectGalleryImages($request);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image_url'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($validated);

        return redirect()->route('admin.products.index', $product)->with('status', 'Product created');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function show(Product $product)
    {
        return redirect()->route('admin.products.edit', $product);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:car,tour'],
            'description' => ['nullable', 'string'],
            'itinerary' => ['nullable', 'string'],
            'cancellation_policy' => ['nullable', 'string'],
            'meeting_point_name' => ['nullable', 'string', 'max:255'],
            'meeting_point_address' => ['nullable', 'string', 'max:1000'],
            'meeting_point_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'meeting_point_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'meeting_point_embed_url' => ['nullable', 'url'],
            'gallery_image_urls' => ['nullable', 'string'],
            'gallery_images_upload' => ['nullable', 'array'],
            'gallery_images_upload.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'add_ons_input' => ['nullable', 'string'],
            'car_model' => ['nullable', 'string', 'max:255', 'required_if:type,car'],
            'max_passengers' => ['nullable', 'integer', 'min:1', 'required_if:type,car'],
            'max_luggage' => ['nullable', 'integer', 'min:0', 'required_if:type,car'],
            'destination' => ['nullable', 'string', 'max:255', 'required_if:type,tour'],
            'duration' => ['nullable', 'string', 'max:255', 'required_if:type,tour'],
            'includes_lunch' => ['nullable', 'boolean'],
            'includes_pickup' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'image_url' => ['nullable', 'string', 'max:2048'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'discounted_price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:3'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['currency'] = $validated['currency'] ?? $product->currency ?? 'THB';
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);
        $validated['is_featured'] = (bool) ($validated['is_featured'] ?? false);
        $validated['includes_lunch'] = (bool) ($validated['includes_lunch'] ?? false);
        $validated['includes_pickup'] = (bool) ($validated['includes_pickup'] ?? false);

        if ($validated['type'] === 'car') {
            $validated['destination'] = null;
            $validated['duration'] = null;
            $validated['includes_lunch'] = false;
            $validated['includes_pickup'] = false;
        }

        if ($validated['type'] === 'tour') {
            $validated['car_model'] = null;
            $validated['max_passengers'] = null;
            $validated['max_luggage'] = null;
        }

        $validated['add_ons'] = $this->parseAddOns($request->input('add_ons_input'));
        $validated['gallery_images'] = $this->collectGalleryImages($request);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image_url && Storage::disk('public')->exists($product->image_url)) {
                Storage::disk('public')->delete($product->image_url);
            }
            $validated['image_url'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('admin.products.edit', $product)->with('status', 'Product updated');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('status', 'Product deleted');
    }

    private function collectGalleryImages(Request $request): array
    {
        $images = [];

        $urlLines = preg_split('/\r\n|\r|\n/', (string) $request->input('gallery_image_urls', ''));
        foreach ($urlLines as $line) {
            $line = trim((string) $line);
            if ($line !== '') {
                $images[] = $line;
            }
        }

        foreach ($request->file('gallery_images_upload', []) as $file) {
            if ($file) {
                $images[] = $file->store('products/gallery', 'public');
            }
        }

        return array_values(array_unique($images));
    }

    private function parseAddOns(?string $rawInput): array
    {
        if (!$rawInput) {
            return [];
        }

        $lines = preg_split('/\r\n|\r|\n/', $rawInput);
        $addOns = [];

        foreach ($lines as $line) {
            $line = trim((string) $line);
            if ($line === '') {
                continue;
            }

            $parts = array_map('trim', explode('|', $line));

            if (count($parts) === 1) {
                $addOns[] = [
                    'name' => $parts[0],
                    'price' => 0,
                    'description' => null,
                ];
                continue;
            }

            $addOns[] = [
                'name' => $parts[0],
                'price' => is_numeric($parts[1] ?? null) ? (float) $parts[1] : 0,
                'description' => $parts[2] ?? null,
            ];
        }

        return $addOns;
    }
}
