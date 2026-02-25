<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventorySlot;
use App\Models\Product;
use Illuminate\Http\Request;

class InventorySlotController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->orderBy('name')
            ->get(['id', 'name', 'type']);

        $selectedMonth = $request->input('month', now()->format('Y-m'));
        $selectedProductId = $request->input('product_id');

        $slotQuery = InventorySlot::query()
            ->with('product')
            ->where('slot_date', '>=', now()->startOfMonth()->subMonths(2)->toDateString())
            ->orderBy('slot_date')
            ->orderBy('start_time');

        if ($selectedProductId) {
            $slotQuery->where('product_id', $selectedProductId);
        }

        if (preg_match('/^\d{4}-\d{2}$/', (string) $selectedMonth)) {
            [$year, $month] = explode('-', $selectedMonth);
            $slotQuery->whereYear('slot_date', (int) $year)->whereMonth('slot_date', (int) $month);
        }

        $slots = $slotQuery->paginate(30)->withQueryString();

        $calendarSummary = InventorySlot::query()
            ->selectRaw('slot_date, SUM(capacity - booked_quantity) as total_remaining')
            ->when($selectedProductId, fn ($query) => $query->where('product_id', $selectedProductId))
            ->when(
                preg_match('/^\d{4}-\d{2}$/', (string) $selectedMonth),
                function ($query) use ($selectedMonth) {
                    [$year, $month] = explode('-', $selectedMonth);
                    $query->whereYear('slot_date', (int) $year)->whereMonth('slot_date', (int) $month);
                }
            )
            ->groupBy('slot_date')
            ->pluck('total_remaining', 'slot_date');

        return view('admin.inventory-slots.index', [
            'products' => $products,
            'slots' => $slots,
            'selectedMonth' => $selectedMonth,
            'selectedProductId' => $selectedProductId,
            'calendarSummary' => $calendarSummary,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'slot_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'capacity' => ['required', 'integer', 'min:1'],
            'cutoff_minutes' => ['required', 'integer', 'min:0', 'max:10080'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? true);
        $validated['booked_quantity'] = 0;

        InventorySlot::create($validated);

        return back()->with('success', 'Inventory slot created successfully.');
    }

    public function update(Request $request, InventorySlot $inventorySlot)
    {
        $validated = $request->validate([
            'slot_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'capacity' => ['required', 'integer', 'min:1'],
            'booked_quantity' => ['nullable', 'integer', 'min:0'],
            'cutoff_minutes' => ['required', 'integer', 'min:0', 'max:10080'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        $bookedQuantity = array_key_exists('booked_quantity', $validated)
            ? (int) $validated['booked_quantity']
            : (int) $inventorySlot->booked_quantity;

        if ($bookedQuantity > (int) $validated['capacity']) {
            return back()->withErrors([
                'booked_quantity' => 'Booked quantity cannot exceed total capacity.',
            ])->withInput();
        }

        $validated['booked_quantity'] = $bookedQuantity;
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        $inventorySlot->update($validated);

        return back()->with('success', 'Inventory slot updated successfully.');
    }

    public function destroy(InventorySlot $inventorySlot)
    {
        if ((int) $inventorySlot->booked_quantity > 0) {
            return back()->with('error', 'Cannot delete slot with existing booked quantity.');
        }

        $inventorySlot->delete();

        return back()->with('success', 'Inventory slot deleted successfully.');
    }
}
