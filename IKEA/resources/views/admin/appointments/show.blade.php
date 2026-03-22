<x-admin-layout>
    <x-slot name="title">Appointment #{{ str_pad($appointment->id, 5, '0', STR_PAD_LEFT) }}</x-slot>
    <x-slot name="header">
        <div class="admin-page-header">
            <div>
                <h2 class="admin-page-title">Appointment #{{ str_pad($appointment->id, 5, '0', STR_PAD_LEFT) }}</h2>
                <p class="admin-page-subtitle">
                    <a href="{{ route('admin.appointments.index') }}" style="color:var(--ikea-blue);font-weight:700;">← Back to Appointments</a>
                </p>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="admin-flash success" style="margin:var(--space-md) var(--space-lg) 0;">
            {{ session('success') }}
        </div>
    @endif

    <div class="admin-content">
        <div style="display:grid;grid-template-columns:1fr 340px;gap:var(--space-md);align-items:start;">

            {{-- LEFT --}}
            <div style="display:flex;flex-direction:column;gap:var(--space-md);">

                {{-- Appointment details --}}
                <div class="admin-card" style="padding:var(--space-md);">
                    <h3 style="font-size:14px;font-weight:700;margin-bottom:14px;">Appointment Details</h3>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div class="appt-detail-item">
                            <div class="appt-detail-label">Service</div>
                            <div class="appt-detail-value">{{ $appointment->serviceLabel() }}</div>
                        </div>
                        <div class="appt-detail-item">
                            <div class="appt-detail-label">Status</div>
                            <div class="appt-detail-value">
                                <span class="order-status-badge {{ $appointment->statusColor() }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="appt-detail-item">
                            <div class="appt-detail-label">Date</div>
                            <div class="appt-detail-value">{{ $appointment->appointment_date->format('F d, Y') }}</div>
                        </div>
                        <div class="appt-detail-item">
                            <div class="appt-detail-label">Time</div>
                            <div class="appt-detail-value">
                                {{ \App\Models\Appointment::timeSlots()[$appointment->appointment_time] ?? $appointment->appointment_time }}
                            </div>
                        </div>
                        <div class="appt-detail-item">
                            <div class="appt-detail-label">Full Name</div>
                            <div class="appt-detail-value">{{ $appointment->full_name }}</div>
                        </div>
                        <div class="appt-detail-item">
                            <div class="appt-detail-label">Phone</div>
                            <div class="appt-detail-value">{{ $appointment->phone }}</div>
                        </div>
                        <div class="appt-detail-item">
                            <div class="appt-detail-label">Email</div>
                            <div class="appt-detail-value">{{ $appointment->email }}</div>
                        </div>
                        @if($appointment->room_size)
                        <div class="appt-detail-item">
                            <div class="appt-detail-label">Room Size</div>
                            <div class="appt-detail-value">{{ $appointment->room_size }}</div>
                        </div>
                        @endif
                        <div class="appt-detail-item">
                            <div class="appt-detail-label">Customer Account</div>
                            <div class="appt-detail-value" style="font-size:13px;">{{ $appointment->user->name }}</div>
                        </div>
                        <div class="appt-detail-item">
                            <div class="appt-detail-label">Booked On</div>
                            <div class="appt-detail-value" style="font-size:13px;">{{ $appointment->created_at->format('M d, Y g:i A') }}</div>
                        </div>
                    </div>

                    @if($appointment->notes)
                        <div style="margin-top:var(--space-sm);padding:var(--space-sm);background:var(--ikea-light);border-radius:6px;">
                            <div class="appt-detail-label" style="margin-bottom:4px;">Customer Notes</div>
                            <p style="font-size:var(--text-sm);line-height:1.6;">{{ $appointment->notes }}</p>
                        </div>
                    @endif
                </div>

                {{-- Update form --}}
                <div class="admin-card" style="padding:var(--space-md);">
                    <h3 style="font-size:14px;font-weight:700;margin-bottom:14px;">Update Appointment</h3>
                    <form method="POST" action="{{ route('admin.appointments.update', $appointment) }}">
                        @csrf @method('PATCH')

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-input">
                                    @foreach(['pending','confirmed','completed','cancelled'] as $s)
                                        <option value="{{ $s }}" {{ $appointment->status === $s ? 'selected' : '' }}>
                                            {{ ucfirst($s) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Assign Staff</label>
                                <select name="staff_id" class="form-input">
                                    <option value="">— Unassigned —</option>
                                    @foreach($staffMembers as $staff)
                                        <option value="{{ $staff->id }}" {{ $appointment->staff_id == $staff->id ? 'selected' : '' }}>
                                            {{ $staff->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Staff Notes (visible to customer)</label>
                            <textarea name="staff_notes" class="form-input" rows="3"
                                      placeholder="e.g. Please bring floor measurements. Ask for Juan at the planning desk.">{{ old('staff_notes', $appointment->staff_notes) }}</textarea>
                        </div>

                        <button type="submit" class="admin-btn-primary">Save Changes</button>
                    </form>
                </div>

            </div>

            {{-- RIGHT SIDEBAR --}}
            <div style="display:flex;flex-direction:column;gap:var(--space-md);position:sticky;top:80px;">

                <div class="admin-card" style="padding:var(--space-md);">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--ikea-gray);margin-bottom:6px;">Appointment</div>
                    <div style="font-size:28px;font-weight:900;color:var(--ikea-blue);">
                        #{{ str_pad($appointment->id, 5, '0', STR_PAD_LEFT) }}
                    </div>
                    <div style="font-size:12px;color:var(--ikea-gray);margin-top:6px;">
                        Booked {{ $appointment->created_at->diffForHumans() }}
                    </div>
                </div>

                @if($appointment->staff_notes)
                    <div class="admin-card" style="padding:var(--space-md);background:#e3f2fd;border-color:#90caf9;">
                        <div class="appt-detail-label" style="color:var(--ikea-blue);margin-bottom:6px;">Staff Notes</div>
                        <p style="font-size:13px;line-height:1.6;color:#1565c0;">{{ $appointment->staff_notes }}</p>
                    </div>
                @endif

                <div class="admin-card" style="padding:var(--space-md);">
                    <form method="POST" action="{{ route('admin.appointments.destroy', $appointment) }}"
                          onsubmit="return confirm('Permanently delete this appointment?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-delete" style="width:100%;padding:10px;">Delete Appointment</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <style>
        .appt-detail-item  { padding:10px 0; border-bottom:1px solid var(--ikea-border); }
        .appt-detail-label { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--ikea-gray);margin-bottom:3px; }
        .appt-detail-value { font-size:var(--text-base);font-weight:700;color:var(--ikea-dark); }
    </style>

</x-admin-layout>