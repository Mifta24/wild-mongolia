<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Search for cars based on criteria
     */
    public function searchCars(Request $request)
    {
        $serviceType = $request->input('service_type', 'airport_transfer');
        $pickupLocation = $request->input('pickup_location', '');
        $serviceDate = $request->input('service_date', date('Y-m-d'));
        $distanceKm = max(0, (float) $request->input('distance_km', 0));

        // Filter parameters
        $vehicleType = $request->input('vehicle_type', '');
        $minPrice = $request->input('min_price', null);
        $maxPrice = $request->input('max_price', null);
        $sortBy = $request->input('sort_by', 'featured');

        // Build query
        $query = Product::cars();

        // Apply filters
        if ($vehicleType) {
            $query->byVehicleType($vehicleType);
        }

        if ($minPrice !== null || $maxPrice !== null) {
            $query->byPriceRange($minPrice, $maxPrice);
        }

        // Apply sorting
        $query->sortBy($sortBy);

        $cars = $query->get();

        // Get available vehicle types for filter dropdown
        $vehicleTypes = Product::cars()->whereNotNull('vehicle_type')->distinct()->pluck('vehicle_type')->toArray();

        return view('search.cars', compact(
            'cars',
            'serviceType',
            'pickupLocation',
            'serviceDate',
            'distanceKm',
            'vehicleType',
            'minPrice',
            'maxPrice',
            'sortBy',
            'vehicleTypes'
        ));
    }

    /**
     * Search for tours based on criteria
     */
    public function searchTours(Request $request)
    {
        $destination = $request->input('destination', 'bangkok');
        $experienceType = $request->input('experience_type', '');

        // Filter parameters
        $category = $request->input('category', '');
        $duration = $request->input('duration', '');
        $language = $request->input('language', '');
        $minPrice = $request->input('min_price', null);
        $maxPrice = $request->input('max_price', null);
        $sortBy = $request->input('sort_by', 'featured');

        // Build query
        $query = Product::tours();

        if ($destination && $destination !== 'all') {
            $query->where('destination', 'like', '%' . ucfirst(str_replace('_', ' ', $destination)) . '%');
        }

        if ($experienceType) {
            $query->where(function ($q) use ($experienceType) {
                $q->where('name', 'like', '%' . $experienceType . '%')
                    ->orWhere('description', 'like', '%' . $experienceType . '%');
            });
        }

        // Apply filters
        if ($category) {
            $query->byCategory($category);
        }

        if ($duration) {
            $query->byDuration($duration);
        }

        if ($language) {
            $query->byLanguage($language);
        }

        if ($minPrice !== null || $maxPrice !== null) {
            $query->byPriceRange($minPrice, $maxPrice);
        }

        // Apply sorting
        $query->sortBy($sortBy);

        $tours = $query->get();

        // Get available categories, durations, and languages for filter dropdowns
        $categories = Product::tours()->whereNotNull('category')->distinct()->pluck('category')->toArray();
        $durations = [
            '2 Hours' => '2 Hours',
            '4 Hours' => '4 Hours',
            '8 Hours' => '8 Hours',
            'Full Day' => 'Full Day',
            '2 Days' => '2 Days',
        ];
        $languages = Product::tours()->whereNotNull('language')->distinct()->pluck('language')->toArray();

        return view('search.tours', compact(
            'tours',
            'destination',
            'experienceType',
            'category',
            'duration',
            'language',
            'minPrice',
            'maxPrice',
            'sortBy',
            'categories',
            'durations',
            'languages'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }

        $product->load([
            'reviews' => function ($query) {
                $query->where('status', 'approved')->latest()->take(8);
            },
        ]);

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
