<?php

namespace App\Http\Controllers;

use App\Mail\AppointmentConfirmation;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('appointments.index', compact('appointments'));
    }

    public function create()
    {
        $serviceTypes = Appointment::serviceTypes();
        $timeSlots    = Appointment::timeSlots();
        $user         = Auth::user();

        return view('appointments.create', compact('serviceTypes', 'timeSlots', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_type'     => 'required|in:' . implode(',', array_keys(Appointment::serviceTypes())),
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required|in:' . implode(',', array_keys(Appointment::timeSlots())),
            'full_name'        => 'required|string|max:255',
            'phone'            => 'required|string|max:20',
            'email'            => 'required|email|max:255',
            'notes'            => 'nullable|string|max:1000',
            'room_size'        => 'nullable|string|max:50',
        ]);

        $appointment = Appointment::create([
            'user_id'          => Auth::id(),
            'service_type'     => $request->service_type,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'full_name'        => $request->full_name,
            'phone'            => $request->phone,
            'email'            => $request->email,
            'notes'            => $request->notes,
            'room_size'        => $request->room_size,
            'status'           => 'pending',
        ]);

        // Send confirmation email
        try {
            Mail::to($request->email)->send(new AppointmentConfirmation($appointment));
        } catch (\Exception $e) {
            \Log::error('Appointment confirmation email failed: ' . $e->getMessage());
        }

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Appointment booked! We will confirm your schedule shortly.');
    }

    public function show(Appointment $appointment)
    {
        if ($appointment->user_id !== Auth::id()) abort(403);
        return view('appointments.show', compact('appointment'));
    }

    public function cancel(Appointment $appointment)
    {
        if ($appointment->user_id !== Auth::id()) abort(403);

        if (!in_array($appointment->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'This appointment cannot be cancelled.');
        }

        $appointment->update(['status' => 'cancelled']);

        return back()->with('success', 'Appointment cancelled.');
    }
}