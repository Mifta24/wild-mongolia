<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role('user');
        $status = $request->get('status', 'active');

        if ($status === 'deactivated') {
            $query->whereNotNull('deactivated_at');
        } elseif ($status === 'all') {
            // Keep all user accounts.
        } else {
            $query->whereNull('deactivated_at');
            $status = 'active';
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        $counts = [
            'active' => User::role('user')->whereNull('deactivated_at')->count(),
            'deactivated' => User::role('user')->whereNotNull('deactivated_at')->count(),
            'all' => User::role('user')->count(),
        ];

        return view('admin.customers.index', compact('customers', 'status', 'counts'));
    }

    public function reactivate(User $user)
    {
        if (!$user->hasRole('user')) {
            return redirect()->route('admin.customers.index')->with('status', 'Cannot reactivate non-customer user');
        }

        $user->forceFill([
            'deactivated_at' => null,
            'scheduled_for_deletion_at' => null,
        ])->save();

        return back()->with('status', 'Customer account reactivated');
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'verified' => ['nullable', 'boolean'],
        ]);

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        if (!empty($validated['verified'])) {
            $user->email_verified_at = now();
        }
        $user->save();
        $user->assignRole('user');

        return redirect()->route('admin.customers.edit', $user)->with('status', 'Customer created');
    }

    public function edit(User $user)
    {
        return view('admin.customers.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'verified' => ['nullable', 'boolean'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->email_verified_at = !empty($validated['verified']) ? ($user->email_verified_at ?? now()) : null;
        $user->save();

        // Ensure role stays 'user' for customers
        if (!$user->hasRole('user')) {
            $user->syncRoles(['user']);
        }

        return redirect()->route('admin.customers.edit', $user)->with('status', 'Customer updated');
    }

    public function destroy(User $user)
    {
        // Prevent deleting non-user roles
        if (!$user->hasRole('user')) {
            return redirect()->route('admin.customers.index')->with('status', 'Cannot delete non-customer user');
        }

        $user->delete();
        return redirect()->route('admin.customers.index')->with('status', 'Customer deleted');
    }

    public function show(User $user)
    {
        return redirect()->route('admin.customers.edit', $user);
    }
}
