<x-app-layout>
    <x-slot name="header">
        <div class="admin-page-header">
            <div>
                <h2 class="admin-page-title">Appointment Details</h2>
                <p class="admin-page-subtitle">
                    <a href="{{ route('appointments.index') }}" style="color:var(--ikea-blue);font-weight:700;">
                        ← My Appointments
                    </a>
                </p>
            </div>
            @if(in_array($appointment->status, ['pending', 'confirmed']))
                <form method="POST" action="{{ route('appointments.cancel', $appointment) }}"
                      onsubmit="return confirm('Cancel this appointment?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="profile-delete-btn" style="width:auto;padding:8px 20px;">
                        Cancel Appointment
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    @if(session('success'))
        <div class="admin-flash success" style="margin:var(--space-md) var(--space-lg) 0;">
            {{ session('success') }}
        </div>
    @endif

    <div class="admin-dashboard">
        <div style="display:grid;grid-template-columns:1fr 300px;gap:var(--space-md);align-items:start;">

            {{-- Main detail card --}}
            <div style="display:flex;flex-direction:column;gap:var(--space-md);">

                {{-- Status banner --}}
                <div class="appt-status-banner status-{{ $appointment->status }}">
                    @php
                        $bannerMsg = match($appointment->status) {
                            'pending'   => '🕐 Your appointment is pending confirmation. We\'ll email you once confirmed.',
                            'confirmed' => '✅ Your appointment is confirmed! Please arrive on time.',
                            'completed' => '🎉 This appointment has been completed.',
                            'cancelled' => '❌ This appointment has been cancelled.',
                        };
                    @endphp
                    {{ $bannerMsg }}
                </div>

                <div class="admin-card profile-card">
                    <h3 class="profile-section-title">Appointment Information</h3>

                    <div class="appt-detail-grid">
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
                            <div class="appt-detail-label">Name</div>
                            <div class="appt-detail-value">{{ $appointment->full_name }}</div>
                        </div>
                        <div class="appt-detail-item">
                            <div class="appt-detail-label">Phone</div>
                            <div class="appt-detail-value">{{ $appointment->phone }}</div>
                        </div>
                        @if($appointment->room_size)
                        <div class="appt-detail-item">
                            <div class="appt-detail-label">Room Size</div>
                            <div class="appt-detail-value">{{ $appointment->room_size }}</div>
                        </div>
                        @endif
                        @if($appointment->staff)
                        <div class="appt-detail-item">
                            <div class="appt-detail-label">Your Planner</div>
                            <div class="appt-detail-value">{{ $appointment->staff->name }}</div>
                        </div>
                        @endif
                    </div>

                    @if($appointment->notes)
                        <div style="margin-top:var(--space-sm);padding:var(--space-sm);background:var(--ikea-light);border-radius:6px;">
                            <div class="appt-detail-label" style="margin-bottom:4px;">Your Notes</div>
                            <p style="font-size:var(--text-sm);line-height:1.6;">{{ $appointment->notes }}</p>
                        </div>
                    @endif

                    @if($appointment->staff_notes)
                        <div style="margin-top:var(--space-sm);padding:var(--space-sm);background:#e3f2fd;border-radius:6px;border-left:3px solid var(--ikea-blue);">
                            <div class="appt-detail-label" style="margin-bottom:4px;color:var(--ikea-blue);">Message from IKEA Planner</div>
                            <p style="font-size:var(--text-sm);line-height:1.6;">{{ $appointment->staff_notes }}</p>
                        </div>
                    @endif
                </div>

            </div>

            {{-- Sidebar --}}
            <div style="display:flex;flex-direction:column;gap:var(--space-md);">
                <div class="admin-card profile-card">
                    <h3 class="profile-section-title">Appointment #</h3>
                    <div style="font-size:28px;font-weight:900;color:var(--ikea-blue);">
                        #{{ str_pad($appointment->id, 5, '0', STR_PAD_LEFT) }}
                    </div>
                    <div style="font-size:12px;color:var(--ikea-gray);margin-top:6px;">
                        Booked {{ $appointment->created_at->format('M d, Y') }}
                    </div>
                </div>

                <div class="admin-card profile-card">
                    <h3 class="profile-section-title">Quick Links</h3>
                    <div class="profile-quick-links">
                        <a href="{{ route('appointments.create') }}" class="profile-quick-link">
                            <span>📅</span> Book Another
                        </a>
                        <a href="{{ route('shop.index') }}" class="profile-quick-link">
                            <span>🛋️</span> Browse Shop
                        </a>
                        <a href="{{ route('orders.index') }}" class="profile-quick-link">
                            <span>📦</span> My Orders
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <style>
        .appt-status-banner {
            padding: 14px var(--space-md);
            border-radius: 8px;
            font-size: var(--text-sm);
            font-weight: 600;
            line-height: 1.5;
        }
        .appt-status-banner.status-pending    { background:#fff3e0; color:#f57c00; border-left:4px solid #f57c00; }
        .appt-status-banner.status-processing,
        .appt-status-banner.status-confirmed  { background:#e3f2fd; color:#1565c0; border-left:4px solid #1565c0; }
        .appt-status-banner.status-completed  { background:#e8f5e9; color:#2e7d32; border-left:4px solid #4caf50; }
        .appt-status-banner.status-cancelled  { background:#ffebee; color:#CC0008; border-left:4px solid #CC0008; }
        .appt-detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:var(--space-sm); margin-bottom:var(--space-sm); }
        .appt-detail-item { padding:10px 0; border-bottom:1px solid var(--ikea-border); }
        .appt-detail-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--ikea-gray); margin-bottom:3px; }
        .appt-detail-value { font-size:var(--text-base); font-weight:700; color:var(--ikea-dark); }
    </style>

</x-app-layout>