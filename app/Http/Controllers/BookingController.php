<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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
        $serviceType = $request->query('type', 'car');
        $productName = $request->query('product', 'Standard Sedan');
        $basePrice   = $request->query('price', 1000);

        return view('bookings.create', compact('serviceType', 'productName', 'basePrice'));
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
            'service_date' => 'required|date|after_or_equal:today',
            'quantity' => 'required|integer|min:1',
            // Validasi kondisional (Car vs Tour)
            'flight_number' => 'nullable|required_if:service_type,car',
            'adult_pax' => 'nullable|integer',
        ]);

        $totalPrice = $request->base_price * $request->quantity;

        $booking = Booking::create([
            'user_id' => Auth::id(), // Null jika Guest
            'guest_name' => $request->guest_name,
            'guest_email' => $request->guest_email,
            'guest_phone' => $request->guest_phone,

            'service_type' => $request->service_type,
            'product_name' => $request->product_name,
            'product_id' => 0, // Placeholder jika belum ada tabel Product

            'service_date' => $request->service_date,
            'service_time' => $request->service_time,
            'pickup_location' => $request->pickup_location,
            'flight_number' => $request->flight_number,

            'quantity' => $request->quantity,
            'total_price' => $totalPrice,

            'status' => 'pending',
            'payment_status' => 'unpaid',
            'special_request' => $request->special_request,
        ]);

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

        // TODO: Integrate dengan Stripe atau payment gateway lainnya
        // Untuk sekarang masih simulasi

        $booking->update([
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        // Award points jika user login (2% dari total price)
        if ($booking->user_id && $booking->user) {
            $pointsEarned = floor($booking->total_price * 0.02); // 2% earning rate

            if ($pointsEarned > 0) {
                $booking->user->addPoints(
                    $pointsEarned,
                    'booking',
                    $booking->id,
                    "Earned from booking {$booking->booking_code}"
                );
            }
        }

        return redirect()->route('booking.success', $booking->id);
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

        // Calculate points earned
        $pointsEarned = 0;
        if ($booking->user_id && $booking->payment_status === 'paid') {
            $pointsEarned = floor($booking->total_price * 0.02);
        }

        return view('bookings.success', compact('booking', 'pointsEarned'));
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
