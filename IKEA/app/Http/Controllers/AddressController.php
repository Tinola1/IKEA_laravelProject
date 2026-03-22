<?php

namespace App\Http\Controllers;

use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'label'     => 'required|string|max:50',
            'full_name' => 'required|string|max:255',
            'phone'     => 'required|string|max:20',
            'address'   => 'required|string|max:500',
            'city'      => 'required|string|max:100',
            'province'  => 'required|string|max:100',
            'zip_code'  => 'required|string|max:10',
        ]);

        $user = Auth::user();

        // If this is their first address, make it default automatically
        $isFirst = $user->addresses()->count() === 0;

        if ($request->boolean('is_default') || $isFirst) {
            $user->addresses()->update(['is_default' => false]);
        }

        $user->addresses()->create([
            'label'      => $request->label,
            'full_name'  => $request->full_name,
            'phone'      => $request->phone,
            'address'    => $request->address,
            'city'       => $request->city,
            'province'   => $request->province,
            'zip_code'   => $request->zip_code,
            'is_default' => $request->boolean('is_default') || $isFirst,
        ]);

        return back()->with('status', 'address-added');
    }

    public function update(Request $request, UserAddress $address)
    {
        if ($address->user_id !== Auth::id()) abort(403);

        $request->validate([
            'label'     => 'required|string|max:50',
            'full_name' => 'required|string|max:255',
            'phone'     => 'required|string|max:20',
            'address'   => 'required|string|max:500',
            'city'      => 'required|string|max:100',
            'province'  => 'required|string|max:100',
            'zip_code'  => 'required|string|max:10',
        ]);

        if ($request->boolean('is_default')) {
            Auth::user()->addresses()->update(['is_default' => false]);
        }

        $address->update([
            'label'      => $request->label,
            'full_name'  => $request->full_name,
            'phone'      => $request->phone,
            'address'    => $request->address,
            'city'       => $request->city,
            'province'   => $request->province,
            'zip_code'   => $request->zip_code,
            'is_default' => $request->boolean('is_default'),
        ]);

        return back()->with('status', 'address-updated');
    }

    public function destroy(UserAddress $address)
    {
        if ($address->user_id !== Auth::id()) abort(403);

        $wasDefault = $address->is_default;
        $address->delete();

        // Promote oldest remaining address to default if needed
        if ($wasDefault) {
            Auth::user()->addresses()->oldest()->first()?->update(['is_default' => true]);
        }

        return back()->with('status', 'address-deleted');
    }

    public function setDefault(UserAddress $address)
    {
        if ($address->user_id !== Auth::id()) abort(403);

        Auth::user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return back()->with('status', 'address-updated');
    }
}