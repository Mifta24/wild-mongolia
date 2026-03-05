<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::findOrCreate('admin');
    Role::findOrCreate('user');
});

test('admin can view deactivated customers list', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $activeCustomer = User::factory()->create(['name' => 'Active Customer']);
    $activeCustomer->assignRole('user');

    $deactivatedCustomer = User::factory()->create([
        'name' => 'Deactivated Customer',
        'deactivated_at' => now()->subDay(),
        'scheduled_for_deletion_at' => now()->addMonths(3),
    ]);
    $deactivatedCustomer->assignRole('user');

    $response = $this
        ->actingAs($admin)
        ->get(route('admin.customers.index', ['status' => 'deactivated']));

    $response->assertOk();
    $response->assertSee('Deactivated Customer');
    $response->assertDontSee('Active Customer');
});

test('admin can reactivate deactivated customer', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $customer = User::factory()->create([
        'deactivated_at' => now()->subDay(),
        'scheduled_for_deletion_at' => now()->addMonths(3),
    ]);
    $customer->assignRole('user');

    $response = $this
        ->actingAs($admin)
        ->patch(route('admin.customers.reactivate', $customer));

    $response->assertRedirect();

    $customer->refresh();
    expect($customer->deactivated_at)->toBeNull();
    expect($customer->scheduled_for_deletion_at)->toBeNull();
});
