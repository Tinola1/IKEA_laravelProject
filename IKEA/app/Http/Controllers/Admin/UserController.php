<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount('orders')->latest()->paginate(20);

        $customerCount = User::whereHas('roles', fn($q) => $q->where('name', 'customer'))->count();
        $staffCount    = User::whereHas('roles', fn($q) => $q->whereIn('name', ['admin', 'staff']))->count();
        $inactiveCount = User::where('is_active', false)->count();

        return view('admin.users.index', compact('users', 'customerCount', 'staffCount', 'inactiveCount'));
    }
    
    public function store(Request $request)
{
    $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|max:255|unique:users,email',
        'password' => 'required|string|min:8',
        'role'     => 'required|in:admin,staff,customer',
    ]);

    $user = User::create([
        'name'              => $request->name,
        'email'             => $request->email,
        'password'          => Hash::make($request->password),
        'email_verified_at' => now(),
        'is_active'         => true,
    ]);

    $user->syncRoles([$request->role]);

    return back()->with('success', "{$user->name} has been created successfully.");
}

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "{$user->name} has been {$status}.");
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,staff,customer',
        ]);

        $user->syncRoles([$request->role]);

        return back()->with('success', "{$user->name}'s role updated to {$request->role}.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }
}