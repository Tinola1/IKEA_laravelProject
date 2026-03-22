<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AppointmentStatusUpdated;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with('user', 'staff')
            ->latest()
            ->paginate(20);

        $pendingCount   = Appointment::where('status', 'pending')->count();
        $confirmedCount = Appointment::where('status', 'confirmed')->count();
        $todayCount     = Appointment::whereDate('appointment_date', today())->count();

        return view('admin.appointments.index', compact(
            'appointments', 'pendingCount', 'confirmedCount', 'todayCount'
        ));
    }

    public function show(Appointment $appointment)
    {
        $appointment->load('user', 'staff');
        $staffMembers = User::whereHas('roles', fn($q) => $q->whereIn('name', ['admin', 'staff']))
            ->get(['id', 'name']);

        return view('admin.appointments.show', compact('appointment', 'staffMembers'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status'      => 'required|in:pending,confirmed,completed,cancelled',
            'staff_id'    => 'nullable|exists:users,id',
            'staff_notes' => 'nullable|string|max:1000',
        ]);

        $previousStatus = $appointment->status;

        $appointment->update([
            'status'      => $request->status,
            'staff_id'    => $request->staff_id,
            'staff_notes' => $request->staff_notes,
        ]);

        // Email customer when status changes
        if ($previousStatus !== $request->status) {
            try {
                $appointment->load('user');
                Mail::to($appointment->email)->send(new AppointmentStatusUpdated($appointment));
            } catch (\Exception $e) {
                \Log::error('Appointment status email failed: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Appointment updated successfully.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('admin.appointments.index')
            ->with('success', 'Appointment deleted.');
    }
}