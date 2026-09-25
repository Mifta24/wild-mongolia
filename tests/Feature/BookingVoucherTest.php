<?php

use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::findOrCreate('user');
    Role::findOrCreate('admin');
});

function createPaidBooking(array $overrides = []): Booking
{
    return Booking::create(array_merge([
        'user_id' => User::factory()->create()->id,
        'guest_name' => 'Voucher Guest',
        'guest_email' => 'voucher@example.com',
        'guest_phone' => '0812345678',
        'service_type' => 'tour',
        'service_subtype' => 'private_tour',
        'destination' => 'Ulaanbaatar',
        'experience_type' => 'temples',
        'meeting_point_confirmed' => true,
        'product_name' => 'Ulaanbaatar Highlights Tour',
        'service_date' => now()->addDay()->toDateString(),
        'service_time' => '09:00',
        'quantity' => 2,
        'total_price' => 2500,
        'add_ons_total' => 0,
        'currency' => 'THB',
        'status' => 'confirmed',
        'payment_status' => 'paid',
        'paid_at' => now(),
    ], $overrides));
}

test('paid user can open voucher page and token gets generated', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    $booking = createPaidBooking([
        'user_id' => $user->id,
        'voucher_token' => null,
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('booking.voucher', $booking->id));

    $response->assertOk();

    $booking->refresh();

    expect($booking->voucher_token)->not->toBeNull();

    $response->assertSee($booking->booking_code);
});

test('unpaid booking cannot open voucher page', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    $booking = createPaidBooking([
        'user_id' => $user->id,
        'payment_status' => 'unpaid',
        'paid_at' => null,
    ]);

    $response = $this
        ->actingAs($user)
        ->from(route('booking.success', $booking->id))
        ->get(route('booking.voucher', $booking->id));

    $response
        ->assertRedirect(route('booking.success', $booking->id))
        ->assertSessionHas('error');
});

test('admin can process qr check-in by token', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $booking = createPaidBooking([
        'voucher_token' => strtoupper(str()->random(32)),
        'status' => 'pending',
        'checked_in_at' => null,
        'checked_in_by' => null,
    ]);

    $signedCheckInUrl = URL::temporarySignedRoute(
        'admin.bookings.checkin.show',
        now()->addMinutes(30),
        ['token' => $booking->voucher_token]
    );

    $showResponse = $this
        ->actingAs($admin)
        ->get($signedCheckInUrl);

    $showResponse->assertOk()->assertSee($booking->booking_code);

    $processResponse = $this
        ->actingAs($admin)
        ->post(route('admin.bookings.checkin.process', $booking->voucher_token));

    $processResponse->assertRedirect()->assertSessionHas('success');

    $booking->refresh();

    expect($booking->checked_in_at)->not->toBeNull();
    expect($booking->checked_in_by)->toBe($admin->id);
    expect($booking->status)->toBe('confirmed');
});

test('admin check-in show route rejects unsigned url', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $booking = createPaidBooking([
        'voucher_token' => strtoupper(str()->random(32)),
    ]);

    $response = $this
        ->actingAs($admin)
        ->get(route('admin.bookings.checkin.show', $booking->voucher_token));

    $response->assertForbidden();
});

test('admin cannot process qr check-in outside allowed date window', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $booking = createPaidBooking([
        'voucher_token' => strtoupper(str()->random(32)),
        'service_date' => now()->addDays(3)->toDateString(),
        'checked_in_at' => null,
        'checked_in_by' => null,
    ]);

    $response = $this
        ->actingAs($admin)
        ->post(route('admin.bookings.checkin.process', $booking->voucher_token));

    $response->assertRedirect()->assertSessionHas('error');

    $booking->refresh();

    expect($booking->checked_in_at)->toBeNull();
    expect($booking->checked_in_by)->toBeNull();
});

test('admin can process check-in when config window is expanded', function () {
    config()->set('booking.check_in_window.days_before', 3);
    config()->set('booking.check_in_window.days_after', 3);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $booking = createPaidBooking([
        'voucher_token' => strtoupper(str()->random(32)),
        'service_date' => now()->addDays(3)->toDateString(),
        'status' => 'pending',
        'checked_in_at' => null,
        'checked_in_by' => null,
    ]);

    $response = $this
        ->actingAs($admin)
        ->post(route('admin.bookings.checkin.process', $booking->voucher_token));

    $response->assertRedirect()->assertSessionHas('success');

    $booking->refresh();

    expect($booking->checked_in_at)->not->toBeNull();
    expect($booking->checked_in_by)->toBe($admin->id);
});
