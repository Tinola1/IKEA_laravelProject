<x-app-layout>
    <x-slot name="header">
        <div class="admin-page-header">
            <div>
                <h2 class="admin-page-title">Manage Appointment</h2>
                <p class="admin-page-subtitle">
                    <a href="{{ route('admin.appointments.index') }}" style="color:var(--ikea-blue);font-weight:700;">
                        ← All Appointments
                    </a>
                </p>
            </div>
            <form method="POST" action="{{ route('admin.appointments.destroy', $appointment) }}"
                  onsubmit="return confirm('Delete this appointment permanently?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="appt-danger-btn">🗑 Delete</button>
            </form>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="appt-flash">✅ {{ session('success') }}</div>
    @endif

    <div class="appt-manage-page">

        {{-- ══ LEFT COLUMN ══════════════════════════════════════ --}}
        <div class="appt-manage-left">

            {{-- Status banner --}}
            <div class="appt-status-banner appt-status-{{ $appointment->status }}">
                @php
                    $bannerMsg = match($appointment->status) {
                        'pending'   => '🕐 Awaiting confirmation — please review and update the status below.',
                        'confirmed' => '✅ Appointment confirmed. Customer has been notified.',
                        'completed' => '🎉 This appointment has been completed.',
                        'cancelled' => '❌ This appointment has been cancelled.',
                    };
                @endphp
                {{ $bannerMsg }}
            </div>

            {{-- Customer Info Card --}}
            <div class="appt-card">
                <h3 class="appt-card-heading">
                    <span class="appt-card-heading-icon">👤</span>
                    Customer Information
                </h3>

                <div class="appt-info-grid">
                    <div class="appt-info-item">
                        <div class="appt-info-label">Full Name</div>
                        <div class="appt-info-value">{{ $appointment->full_name }}</div>
                    </div>
                    <div class="appt-info-item">
                        <div class="appt-info-label">Phone</div>
                        <div class="appt-info-value">{{ $appointment->phone }}</div>
                    </div>
                    <div class="appt-info-item">
                        <div class="appt-info-label">Email</div>
                        <div class="appt-info-value">{{ $appointment->email }}</div>
                    </div>
                    <div class="appt-info-item">
                        <div class="appt-info-label">Account</div>
                        <div class="appt-info-value">{{ $appointment->user?->name ?? '—' }}</div>
                    </div>
                    <div class="appt-info-item">
                        <div class="appt-info-label">Service</div>
                        <div class="appt-info-value">{{ $appointment->serviceLabel() }}</div>
                    </div>
                    <div class="appt-info-item">
                        <div class="appt-info-label">Room Size</div>
                        <div class="appt-info-value">{{ $appointment->room_size ?? '—' }}</div>
                    </div>
                    <div class="appt-info-item">
                        <div class="appt-info-label">Preferred Date</div>
                        <div class="appt-info-value">{{ $appointment->appointment_date->format('F d, Y') }}</div>
                    </div>
                    <div class="appt-info-item">
                        <div class="appt-info-label">Preferred Time</div>
                        <div class="appt-info-value">
                            {{ \App\Models\Appointment::timeSlots()[$appointment->appointment_time] ?? $appointment->appointment_time }}
                        </div>
                    </div>
                </div>

                @if($appointment->notes)
                    <div class="appt-notes-box">
                        <div class="appt-info-label" style="margin-bottom:6px;">📝 Customer Notes</div>
                        <p style="font-size:var(--text-sm);line-height:1.7;color:var(--ikea-dark);">
                            {{ $appointment->notes }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- Update Form Card --}}
            <div class="appt-card">
                <h3 class="appt-card-heading">
                    <span class="appt-card-heading-icon">⚙️</span>
                    Update Appointment
                </h3>

                <form method="POST" action="{{ route('admin.appointments.update', $appointment) }}">
                    @csrf
                    @method('PATCH')

                    <div class="appt-form-grid">
                        <div class="appt-field">
                            <label class="appt-label">Status</label>
                            <select name="status" class="appt-input" required>
                                @foreach(['pending' => '🕐 Pending', 'confirmed' => '✅ Confirmed', 'completed' => '🎉 Completed', 'cancelled' => '❌ Cancelled'] as $val => $lbl)
                                    <option value="{{ $val }}" {{ $appointment->status === $val ? 'selected' : '' }}>
                                        {{ $lbl }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="appt-field">
                            <label class="appt-label">Assign Planner</label>
                            <select name="staff_id" class="appt-input">
                                <option value="">— Unassigned —</option>
                                @foreach($staffMembers as $staff)
                                    <option value="{{ $staff->id }}"
                                        {{ $appointment->staff_id == $staff->id ? 'selected' : '' }}>
                                        {{ $staff->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="appt-field" style="margin-top:var(--space-sm);">
                        <label class="appt-label">
                            Message to Customer
                            <span style="font-weight:400;color:var(--ikea-gray);"> — optional</span>
                        </label>
                        <textarea name="staff_notes"
                                  class="appt-input appt-textarea"
                                  rows="3"
                                  placeholder="Add a note for the customer — preparation tips, location details, what to bring...">{{ old('staff_notes', $appointment->staff_notes) }}</textarea>
                    </div>

                    <div class="appt-form-actions">
                        <button type="submit" class="appt-save-btn">Save Changes</button>
                        <div class="appt-email-note">
                            <span style="color:var(--ikea-blue);">📧</span>
                            Changing status will automatically email
                            <strong>{{ $appointment->email }}</strong>
                        </div>
                    </div>

                </form>
            </div>

        </div>

        {{-- ══ RIGHT SIDEBAR ════════════════════════════════════ --}}
        <div class="appt-manage-sidebar">

            {{-- Appointment ID card --}}
            <div class="appt-card appt-id-card">
                <div class="appt-id-label">Appointment</div>
                <div class="appt-id-number">#{{ str_pad($appointment->id, 5, '0', STR_PAD_LEFT) }}</div>
                <span class="appt-status-pill appt-status-{{ $appointment->status }}">
                    {{ ucfirst($appointment->status) }}
                </span>
                <div class="appt-booked-at">
                    Booked {{ $appointment->created_at->format('M d, Y \a\t h:i A') }}
                </div>
            </div>

            {{-- Email notice --}}
            <div class="appt-card appt-notice-card">
                <div class="appt-notice-icon">📧</div>
                <div>
                    <div class="appt-notice-title">Auto Email Notice</div>
                    <p class="appt-notice-desc">
                        Saving a status change will automatically email the customer at
                        <strong>{{ $appointment->email }}</strong>.
                    </p>
                </div>
            </div>

            {{-- Assigned planner --}}
            @if($appointment->staff)
                <div class="appt-card">
                    <div class="appt-info-label" style="margin-bottom:8px;">Assigned Planner</div>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:36px;height:36px;background:var(--ikea-blue);color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:15px;flex-shrink:0;">
                            {{ strtoupper(substr($appointment->staff->name, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight:700;color:var(--ikea-dark);">{{ $appointment->staff->name }}</div>
                            <div style="font-size:12px;color:var(--ikea-gray);">IKEA Planner</div>
                        </div>
                    </div>
                </div>
            @endif

        </div>

    </div>

    <script>
    document.getElementById('apptUpdateForm').addEventListener('submit', function(e) {
        const status = this.querySelector('[name="status"]');
        if (!status.value) {
            e.preventDefault();
            status.style.borderColor = '#CC0008';
            const msg = document.createElement('p');
            msg.className = 'js-error';
            msg.style.cssText = 'color:#CC0008;font-size:12px;margin-top:4px;font-weight:600;';
            msg.textContent = 'Please select a status.';
            status.parentNode.appendChild(msg);
        }
    });
    </script>
</x-app-layout>