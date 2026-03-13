<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount('orders')
            ->latest()
            ->paginate(20);

        $customerCount = User::whereHas('roles', fn($q) => $q->where('name', 'customer'))->count();
        $staffCount    = User::whereHas('roles', fn($q) => $q->whereIn('name', ['admin', 'staff']))->count();

        return view('admin.users.index', compact('users', 'customerCount', 'staffCount'));
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