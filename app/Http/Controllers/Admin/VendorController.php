<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::query();

        if ($search = $request->string('search')->toString()) {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('vendor_code', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && in_array($request->status, ['active', 'inactive'], true)) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($serviceType = $request->string('service_type')->toString()) {
            $query->where('service_type', $serviceType);
        }

        $vendors = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('admin.vendors.index', compact('vendors'));
    }

    public function create()
    {
        return view('admin.vendors.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateVendor($request);
        $validated['is_active'] = $request->boolean('is_active');

        Vendor::create($validated);

        return redirect()->route('admin.vendors.index')->with('success', 'Vendor created successfully.');
    }

    public function edit(Vendor $vendor)
    {
        return view('admin.vendors.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $validated = $this->validateVendor($request, $vendor->id);
        $validated['is_active'] = $request->boolean('is_active');

        $vendor->update($validated);

        return redirect()->route('admin.vendors.edit', $vendor)->with('success', 'Vendor updated successfully.');
    }

    public function destroy(Vendor $vendor)
    {
        if ($vendor->dispatchAssignments()->exists()) {
            return redirect()->route('admin.vendors.index')
                ->with('error', 'Vendor cannot be deleted because it already has dispatch assignments.');
        }

        $vendor->delete();

        return redirect()->route('admin.vendors.index')->with('success', 'Vendor deleted successfully.');
    }

    private function validateVendor(Request $request, ?int $vendorId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'vendor_code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('vendors', 'vendor_code')->ignore($vendorId),
            ],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'line_id' => ['nullable', 'string', 'max:255'],
            'whatsapp_number' => ['nullable', 'string', 'max:50'],
            'service_type' => ['nullable', Rule::in(['car', 'tour', 'both'])],
            'address' => ['nullable', 'string'],
            'default_commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
