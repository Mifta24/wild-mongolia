<?php

use App\Models\Booking;
use App\Models\DispatchAssignment;
use App\Models\User;
use App\Models\Vendor;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::findOrCreate('admin');
});

function makeBooking(array $overrides = []): Booking
{
    return Booking::query()->create(array_merge([
        'user_id' => User::factory()->create()->id,
        'guest_name' => 'Flow Customer',
        'guest_email' => 'flow.customer@example.com',
        'guest_phone' => '0812345678',
        'service_type' => 'car',
        'service_subtype' => 'airport-transfer',
        'destination' => 'Bangkok',
        'experience_type' => null,
        'meeting_point_confirmed' => false,
        'product_name' => 'Airport Transfer Sedan',
        'service_date' => now()->addDay()->toDateString(),
        'service_time' => '09:00',
        'quantity' => 1,
        'total_price' => 1500,
        'add_ons_total' => 0,
        'currency' => 'THB',
        'status' => 'confirmed',
        'payment_status' => 'paid',
        'paid_at' => now(),
    ], $overrides));
}

function makeVendor(array $overrides = []): Vendor
{
    return Vendor::query()->create(array_merge([
        'name' => 'Flow Vendor',
        'vendor_code' => 'VDR-FLOW01',
        'contact_person' => 'Dispatcher One',
        'phone' => '0810000000',
        'service_type' => 'car',
        'default_commission_rate' => 10,
        'is_active' => true,
    ], $overrides));
}

test('end to end paid booking can be assigned and completed', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $booking = makeBooking([
        'payment_status' => 'paid',
        'status' => 'confirmed',
    ]);

    $vendor = makeVendor();

    $dispatchResponse = $this
        ->actingAs($admin)
        ->from(route('admin.bookings.show', $booking->id))
        ->post(route('admin.dispatch-assignments.store'), [
            'booking_id' => $booking->id,
            'vendor_id' => $vendor->id,
            'driver_name' => 'Driver EndToEnd',
            'driver_phone' => '0822222222',
            'vehicle_plate' => '1กก-1234',
            'dispatch_status' => 'completed',
            'dispatch_notes' => 'Flow test completed',
            'commission_type' => 'percentage',
            'commission_rate' => 10,
            'settlement_status' => 'paid',
            'settlement_notes' => 'Settled',
        ]);

    $dispatchResponse->assertRedirect()->assertSessionHas('success');

    $this->assertDatabaseHas('dispatch_assignments', [
        'booking_id' => $booking->id,
        'vendor_id' => $vendor->id,
        'driver_name' => 'Driver EndToEnd',
        'dispatch_status' => 'completed',
        'settlement_status' => 'paid',
        'commission_type' => 'percentage',
    ]);

    $bookingResponse = $this
        ->actingAs($admin)
        ->put(route('admin.bookings.update', $booking->id), [
            'status' => 'completed',
        ]);

    $bookingResponse->assertRedirect()->assertSessionHas('success');

    expect($booking->fresh()->status)->toBe('completed');
});

test('admin cannot complete booking when payment is unpaid', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $booking = makeBooking([
        'payment_status' => 'unpaid',
        'paid_at' => null,
        'status' => 'confirmed',
    ]);

    $response = $this
        ->actingAs($admin)
        ->from(route('admin.bookings.show', $booking->id))
        ->put(route('admin.bookings.update', $booking->id), [
            'status' => 'completed',
        ]);

    $response
        ->assertRedirect(route('admin.bookings.show', $booking->id))
        ->assertSessionHas('error');

    expect($booking->fresh()->status)->toBe('confirmed');
});

test('admin cannot complete dispatch when booking payment is unpaid', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $booking = makeBooking([
        'payment_status' => 'unpaid',
        'paid_at' => null,
    ]);

    $vendor = makeVendor([
        'vendor_code' => 'VDR-FLOW02',
    ]);

    $response = $this
        ->actingAs($admin)
        ->from(route('admin.bookings.show', $booking->id))
        ->post(route('admin.dispatch-assignments.store'), [
            'booking_id' => $booking->id,
            'vendor_id' => $vendor->id,
            'driver_name' => 'Driver Unpaid',
            'dispatch_status' => 'completed',
            'commission_type' => 'percentage',
            'commission_rate' => 10,
            'settlement_status' => 'unpaid',
        ]);

    $response->assertRedirect()->assertSessionHas('error');

    expect(DispatchAssignment::query()->where('booking_id', $booking->id)->exists())->toBeFalse();
});

test('admin cannot set settlement paid before dispatch is completed', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $booking = makeBooking([
        'payment_status' => 'paid',
        'paid_at' => now(),
    ]);

    $vendor = makeVendor([
        'vendor_code' => 'VDR-FLOW03',
    ]);

    $response = $this
        ->actingAs($admin)
        ->from(route('admin.bookings.show', $booking->id))
        ->post(route('admin.dispatch-assignments.store'), [
            'booking_id' => $booking->id,
            'vendor_id' => $vendor->id,
            'driver_name' => 'Driver Settlement',
            'dispatch_status' => 'assigned',
            'commission_type' => 'percentage',
            'commission_rate' => 10,
            'settlement_status' => 'paid',
        ]);

    $response->assertRedirect()->assertSessionHas('error');

    expect(DispatchAssignment::query()->where('booking_id', $booking->id)->exists())->toBeFalse();
});
