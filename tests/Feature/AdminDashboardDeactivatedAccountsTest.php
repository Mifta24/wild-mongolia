<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::findOrCreate('admin');
    Role::findOrCreate('user');
});

test('dashboard shows deactivated account monitoring when there are deactivated users', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $soon = User::factory()->create([
        'name' => 'Soon Deleted',
        'deactivated_at' => now()->subDays(10),
        'scheduled_for_deletion_at' => now()->addDays(3),
    ]);
    $soon->assignRole('user');

    $later = User::factory()->create([
        'name' => 'Later Deleted',
        'deactivated_at' => now()->subDays(5),
        'scheduled_for_deletion_at' => now()->addDays(20),
    ]);
    $later->assignRole('user');

    $response = $this
        ->actingAs($admin)
        ->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('Deactivated Accounts Monitoring');
    $response->assertSee('Soon Deleted');
    $response->assertDontSee('Later Deleted');
});
