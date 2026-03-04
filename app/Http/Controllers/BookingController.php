<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\InventorySlot;
use App\Models\Product;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Services\StripePaymentService;
use BaconQrCode\Renderer\GDLibRenderer;
use BaconQrCode\Writer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $product = null;

        $serviceType = $request->query('type', 'car');
        if (!in_array($serviceType, ['car', 'tour'], true)) {
            $serviceType = 'car';
        }

        $serviceSubtype = $request->query(
            'service_subtype',
            $request->query(
                'service_type',
                $serviceType === 'car' ? 'airport_transfer' : 'private_tour'
            )
        );

        if ($serviceType === 'car' && !in_array($serviceSubtype, ['airport_transfer', 'city_rental_hourly'], true)) {
            $serviceSubtype = 'airport_transfer';
        }

        if ($serviceType === 'tour') {
            $serviceSubtype = 'private_tour';
        }

        $destination = $request->query('destination', 'Bangkok');
        $experienceType = $request->query('experience_type', 'temples');
        $productId = $request->query('product_id');
        $productName = $request->query('product', $serviceType === 'car' ? 'Standard Sedan' : 'Bangkok Highlights Tour');
        $basePrice = $request->query('price', $serviceType === 'car' ? 1000 : 2500);

        if ($productId) {
            $product = Product::query()->find($productId);
            if ($product) {
                $productName = $product->name;
                $basePrice = $product->final_price;
            }
        }

        $availableSlots = collect();
        $slotsByDate = [];
        $availabilityCalendar = [];

        if ($product) {
            $availableSlots = $product->availableInventorySlots(now(), now()->addDays(45));

            $slotsByDate = $availableSlots
                ->groupBy(fn (InventorySlot $slot) => $slot->slot_date->toDateString())
                ->map(fn ($group) => $group->map(fn (InventorySlot $slot) => [
                    'id' => $slot->id,
                    'time' => substr((string) $slot->start_time, 0, 5),
                    'remaining_capacity' => $slot->remaining_capacity,
                    'cutoff_at' => $slot->cutoff_date_time->format('d M Y H:i'),
                ])->values()->all())
                ->all();

            $availabilityCalendar = $availableSlots
                ->groupBy(fn (InventorySlot $slot) => $slot->slot_date->toDateString())
                ->map(fn ($group) => $group->sum('remaining_capacity'))
                ->all();
        }

        return view('bookings.create', compact(
            'serviceType',
            'serviceSubtype',
            'destination',
            'experienceType',
            'productId',
            'product',
            'productName',
            'basePrice',
            'slotsByDate',
            'availabilityCalendar'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookingRequest $request)
    {
        $request->validate([
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email',
            'guest_phone' => 'required|string',
            'service_type' => 'required|in:car,tour',
            'service_subtype' => 'required|string|max:100',
            'service_date' => 'required|date|after_or_equal:today',
            'service_time' => 'required',
            'base_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'inventory_slot_id' => 'nullable|exists:inventory_slots,id',
            'flight_number' => 'nullable|required_if:service_subtype,airport_transfer|string|max:50',
            'destination' => 'nullable|required_if:service_type,tour|string|max:100',
            'experience_type' => 'nullable|required_if:service_type,tour|string|max:100',
            'pickup_location' => 'nullable|string|max:255',
            'adult_pax' => 'nullable|integer',
            'product_id' => 'nullable|exists:products,id',
            'meeting_point_confirmed' => 'nullable|boolean',
            'selected_add_ons' => 'nullable|array',
            'selected_add_ons.*' => 'integer|min:0',
        ]);

        if ($request->service_type === 'tour') {
            $request->validate([
                'meeting_point_confirmed' => 'accepted',
            ], [
                'meeting_point_confirmed.accepted' => 'Please confirm the meeting point before continuing.',
            ]);
        }

        $product = null;
        $selectedAddOns = [];
        $addOnsTotal = 0;

        if ($request->filled('product_id')) {
            $product = Product::query()->find($request->product_id);
        }

        $inventorySlot = null;
        if ($request->filled('inventory_slot_id')) {
            $inventorySlot = InventorySlot::query()
                ->with('product')
                ->find($request->integer('inventory_slot_id'));
        }

        if ($product && $product->inventorySlots()->where('is_active', true)->exists() && !$inventorySlot) {
            throw ValidationException::withMessages([
                'inventory_slot_id' => 'Please select an available slot before continuing.',
            ]);
        }

        if ($inventorySlot) {
            if (!$product || $inventorySlot->product_id !== $product->id) {
                throw ValidationException::withMessages([
                    'inventory_slot_id' => 'Selected slot does not match the chosen product.',
                ]);
            }

            if (!$inventorySlot->is_active) {
                throw ValidationException::withMessages([
                    'inventory_slot_id' => 'Selected slot is no longer active.',
                ]);
            }
        }

        $selectedAddOnIndexes = collect($request->input('selected_add_ons', []))
            ->map(fn ($index) => (int) $index)
            ->unique()
            ->values();

        if ($product && is_array($product->add_ons)) {
            foreach ($selectedAddOnIndexes as $index) {
                $option = $product->add_ons[$index] ?? null;
                if (!is_array($option) || empty($option['name'])) {
                    continue;
                }

                $price = max(0, (float) ($option['price'] ?? 0));

                $selectedAddOns[] = [
                    'name' => (string) $option['name'],
                    'price' => round($price, 2),
                    'description' => $option['description'] ?? null,
                ];

                $addOnsTotal += $price;
            }
        }

        $totalPrice = ($request->base_price * $request->quantity) + $addOnsTotal;

        $serviceDate = $request->service_date;
        $serviceTime = $request->service_time;

        $bookingPayload = [
            'user_id' => Auth::id(),
            'guest_name' => $request->guest_name,
            'guest_email' => $request->guest_email,
            'guest_phone' => $request->guest_phone,
            'service_type' => $request->service_type,
            'service_subtype' => $request->service_subtype,
            'destination' => $request->destination,
            'experience_type' => $request->experience_type,
            'meeting_point_confirmed' => $request->boolean('meeting_point_confirmed'),
            'selected_add_ons' => $selectedAddOns,
            'inventory_slot_id' => $inventorySlot?->id,
            'product_name' => $request->product_name,
            'product_id' => $request->product_id,
            'pickup_location' => $request->pickup_location,
            'flight_number' => $request->flight_number,
            'quantity' => $request->quantity,
            'total_price' => $totalPrice,
            'add_ons_total' => $addOnsTotal,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'special_request' => $request->special_request,
        ];

        $booking = DB::transaction(function () use ($inventorySlot, $request, $serviceDate, $serviceTime, $bookingPayload) {
            if ($inventorySlot) {
                $lockedSlot = InventorySlot::query()->lockForUpdate()->findOrFail($inventorySlot->id);

                if (!$lockedSlot->is_active || $lockedSlot->isPastCutoff()) {
                    throw ValidationException::withMessages([
                        'inventory_slot_id' => 'The selected slot is already past cutoff time.',
                    ]);
                }

                if ($lockedSlot->remaining_capacity < (int) $request->quantity) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Requested quantity exceeds remaining slot capacity.',
                    ]);
                }

                $lockedSlot->increment('booked_quantity', (int) $request->quantity);

                $bookingPayload['service_date'] = $lockedSlot->slot_date->toDateString();
                $bookingPayload['service_time'] = substr((string) $lockedSlot->start_time, 0, 5);
            } else {
                $bookingPayload['service_date'] = $serviceDate;
                $bookingPayload['service_time'] = $serviceTime;
            }

            return Booking::create($bookingPayload);
        });

        return redirect()->route('booking.payment', $booking->id);
    }

    /**
     * Halaman Review & Payment
     */
    public function payment($id)
    {
        $booking = Booking::findOrFail($id);

        // Security check: Pastikan user hanya bisa melihat booking miliknya (jika login)
        if (Auth::check() && $booking->user_id !== Auth::id()) {
            abort(403);
        }

        return view('bookings.payment', compact('booking'));
    }

    /**
     * Proses Pembayaran
     */
    public function processPayment(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // Security check
        if (Auth::check() && $booking->user_id !== Auth::id()) {
            abort(403);
        }

        if ($booking->payment_status === 'paid') {
            return redirect()->route('booking.success', $booking->id);
        }

        $request->validate([
            'payment_method' => 'required|in:credit_card',
        ]);

        try {
            $stripeService = app(StripePaymentService::class);
            $session = $stripeService->createCheckoutSession($booking);

            $booking->update([
                'stripe_checkout_session_id' => $session->id,
            ]);

            return redirect($session->url);
        } catch (\Throwable $exception) {
            report($exception);

            return back()->with('error', 'Unable to initiate payment. Please try again.');
        }
    }

    /**
     * Halaman Sukses / Voucher
     */
    public function success($id)
    {
        $booking = Booking::findOrFail($id);

        // Security check
        if (Auth::check() && $booking->user_id !== Auth::id()) {
            abort(403);
        }

        $sessionId = request()->query('session_id');

        if ($sessionId && $booking->payment_status !== 'paid') {
            try {
                $stripeService = app(StripePaymentService::class);
                $session = $stripeService->retrieveCheckoutSession($sessionId);

                if (
                    $session->id === $booking->stripe_checkout_session_id
                    && $session->payment_status === 'paid'
                ) {
                    $booking->update([
                        'stripe_payment_intent_id' => $session->payment_intent,
                        'payment_status' => 'paid',
                        'status' => $booking->status === 'pending' ? 'confirmed' : $booking->status,
                        'paid_at' => $booking->paid_at ?? now(),
                    ]);
                }
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        if (blank($booking->invoice_number) && $booking->payment_status === 'paid') {
            $booking->update([
                'invoice_number' => 'INV-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6)),
            ]);
        }

        if (blank($booking->voucher_token) && $booking->payment_status === 'paid') {
            $booking->update([
                'voucher_token' => Str::upper(Str::random(32)),
            ]);
        }

        $booking->refresh();

        // Calculate points earned
        $pointsEarned = 0;
        if ($booking->user_id && $booking->payment_status === 'paid') {
            $pointsEarned = floor($booking->total_price * 0.02);
        }

        return view('bookings.success', [
            'booking' => $booking,
            'pointsEarned' => $pointsEarned,
            ...$this->prepareVoucherData($booking),
        ]);
    }

    public function downloadInvoice($id)
    {
        $booking = Booking::findOrFail($id);

        if (Auth::check() && $booking->user_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($booking->payment_status, ['paid', 'refunded'], true)) {
            return back()->with('error', 'Invoice is available after payment is completed.');
        }

        if (blank($booking->invoice_number)) {
            $booking->update([
                'invoice_number' => 'INV-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6)),
            ]);
            $booking->refresh();
        }

        $pdf = Pdf::loadView('bookings.invoice', [
            'booking' => $booking,
        ]);

        return $pdf->download($booking->invoice_number . '.pdf');
    }

    public function voucher($id)
    {
        $booking = Booking::findOrFail($id);

        if (Auth::check() && $booking->user_id !== Auth::id()) {
            abort(403);
        }

        if ($booking->payment_status !== 'paid') {
            return back()->with('error', 'Voucher is available after payment is completed.');
        }

        if (blank($booking->voucher_token)) {
            $booking->update([
                'voucher_token' => Str::upper(Str::random(32)),
            ]);
            $booking->refresh();
        }

        return view('bookings.voucher', [
            'booking' => $booking,
            ...$this->prepareVoucherData($booking),
        ]);
    }

    public function downloadVoucher($id)
    {
        $booking = Booking::findOrFail($id);

        if (Auth::check() && $booking->user_id !== Auth::id()) {
            abort(403);
        }

        if ($booking->payment_status !== 'paid') {
            return back()->with('error', 'Voucher is available after payment is completed.');
        }

        if (blank($booking->voucher_token)) {
            $booking->update([
                'voucher_token' => Str::upper(Str::random(32)),
            ]);
            $booking->refresh();
        }

        $pdf = Pdf::loadView('bookings.voucher-pdf', [
            'booking' => $booking,
            ...$this->prepareVoucherData($booking),
        ]);

        return $pdf->download('VOUCHER-' . $booking->booking_code . '.pdf');
    }

    private function prepareVoucherData(Booking $booking): array
    {
        $checkInUrl = URL::temporarySignedRoute(
            'admin.bookings.checkin.show',
            now()->addDays(30),
            ['token' => $booking->voucher_token]
        );

        $renderer = new GDLibRenderer(320);

        $writer = new Writer($renderer);
        $qrPngBinary = $writer->writeString($checkInUrl);
        $qrImageDataUri = 'data:image/png;base64,' . base64_encode($qrPngBinary);

        return [
            'checkInUrl' => $checkInUrl,
            'qrImageUrl' => $qrImageDataUri,
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        //
    }
}
