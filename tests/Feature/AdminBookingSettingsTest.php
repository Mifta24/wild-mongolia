<?php

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::findOrCreate('admin');

    Cache::forget('booking.check_in_window.days_before');
    Cache::forget('booking.check_in_window.days_after');
});

test('admin can view booking check-in window settings page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this
        ->actingAs($admin)
        ->get(route('admin.settings.checkin-window.edit'));

    $response->assertOk()->assertSee('Check-In Window Settings');
});

test('admin can update booking check-in window settings', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this
        ->actingAs($admin)
        ->put(route('admin.settings.checkin-window.update'), [
            'days_before' => 2,
            'days_after' => 4,
        ]);

    $response->assertRedirect()->assertSessionHas('success');

    expect(Cache::get('booking.check_in_window.days_before'))->toBe(2);
    expect(Cache::get('booking.check_in_window.days_after'))->toBe(4);
});

test('admin can reset booking check-in window settings to defaults', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Cache::forever('booking.check_in_window.days_before', 5);
    Cache::forever('booking.check_in_window.days_after', 6);

    $response = $this
        ->actingAs($admin)
        ->delete(route('admin.settings.checkin-window.reset'));

    $response->assertRedirect()->assertSessionHas('success');

    expect(Cache::get('booking.check_in_window.days_before'))->toBeNull();
    expect(Cache::get('booking.check_in_window.days_after'))->toBeNull();
});
