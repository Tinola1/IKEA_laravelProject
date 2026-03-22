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

    <style>
        /* ── PAGE ──────────────────────────────────────────── */
        .appt-manage-page {
            display: grid;
            grid-template-columns: 1fr 280px;
            gap: var(--space-md);
            align-items: start;
            padding: var(--space-lg);
            max-width: 1200px;
            margin: 0 auto;
        }
        .appt-manage-left    { display:flex; flex-direction:column; gap:var(--space-md); }
        .appt-manage-sidebar { display:flex; flex-direction:column; gap:var(--space-md); }

        /* Flash */
        .appt-flash {
            margin: var(--space-md) var(--space-lg) 0;
            padding: 12px var(--space-md);
            background: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #4caf50;
            border-radius: 6px;
            font-size: var(--text-sm);
            font-weight: 600;
        }

        /* Header buttons */
        .appt-danger-btn {
            padding: 8px 18px;
            background: #ffebee;
            color: #CC0008;
            border: 1.5px solid #ffcdd2;
            border-radius: 40px;
            font-size: var(--text-sm);
            font-weight: 700;
            font-family: 'Noto Sans', sans-serif;
            cursor: pointer;
            transition: all var(--transition-fast);
        }
        .appt-danger-btn:hover { background: #CC0008; color: white; border-color: #CC0008; }

        /* Status banner */
        .appt-status-banner {
            padding: 13px var(--space-md);
            border-radius: 8px;
            font-size: var(--text-sm);
            font-weight: 600;
            line-height: 1.5;
        }
        .appt-status-pending   { background:#fff3e0; color:#e65100; border-left:4px solid #f57c00; }
        .appt-status-confirmed { background:#e3f2fd; color:#1565c0; border-left:4px solid #1565c0; }
        .appt-status-completed { background:#e8f5e9; color:#2e7d32; border-left:4px solid #4caf50; }
        .appt-status-cancelled { background:#ffebee; color:#CC0008; border-left:4px solid #CC0008; }

        /* Card */
        .appt-card {
            background: white;
            border: 1px solid var(--ikea-border);
            border-radius: 10px;
            padding: var(--space-md);
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }
        .appt-card-heading {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: var(--text-base);
            font-weight: 800;
            color: var(--ikea-dark);
            padding-bottom: var(--space-sm);
            margin-bottom: var(--space-sm);
            border-bottom: 2px solid var(--ikea-border);
        }
        .appt-card-heading-icon { font-size: 18px; }

        /* Info grid */
        .appt-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            margin-bottom: var(--space-sm);
        }
        .appt-info-item {
            padding: 12px 0;
            border-bottom: 1px solid var(--ikea-border);
        }
        .appt-info-item:nth-last-child(-n+2) { border-bottom: none; }
        .appt-info-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--ikea-gray);
            margin-bottom: 4px;
        }
        .appt-info-value {
            font-size: var(--text-base);
            font-weight: 700;
            color: var(--ikea-dark);
        }
        .appt-notes-box {
            background: var(--ikea-light);
            border-radius: 8px;
            padding: var(--space-sm);
            margin-top: var(--space-xs);
        }

        /* Form */
        .appt-form-grid { display:grid; grid-template-columns:1fr 1fr; gap:var(--space-sm); }
        .appt-field     { display:flex; flex-direction:column; gap:6px; }
        .appt-label     { font-size:var(--text-sm); font-weight:700; color:var(--ikea-dark); }
        .appt-input {
            width: 100%;
            height: 44px;
            padding: 0 14px;
            border: 1.5px solid var(--ikea-border);
            border-radius: 8px;
            font-size: var(--text-base);
            font-family: 'Noto Sans', sans-serif;
            color: var(--ikea-dark);
            background: white;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .appt-input:focus { outline:none; border-color:var(--ikea-blue); box-shadow:0 0 0 3px rgba(0,88,163,0.1); }
        .appt-textarea   { height:auto; padding:12px 14px; resize:vertical; }

        .appt-form-actions {
            display: flex;
            align-items: center;
            gap: var(--space-md);
            margin-top: var(--space-md);
            flex-wrap: wrap;
        }
        .appt-save-btn {
            height: 46px;
            padding: 0 28px;
            background: var(--ikea-yellow);
            color: var(--ikea-dark);
            border: none;
            border-radius: 40px;
            font-size: var(--text-base);
            font-weight: 800;
            font-family: 'Noto Sans', sans-serif;
            cursor: pointer;
            transition: background 0.15s, transform 0.15s;
            flex-shrink: 0;
        }
        .appt-save-btn:hover { background: #f0cc00; transform: translateY(-1px); }
        .appt-email-note {
            font-size: 12px;
            color: var(--ikea-gray);
            line-height: 1.5;
        }

        /* Sidebar cards */
        .appt-id-card { text-align:center; }
        .appt-id-label  { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:var(--ikea-gray); margin-bottom:6px; }
        .appt-id-number { font-size:36px; font-weight:900; color:var(--ikea-blue); letter-spacing:-1px; margin-bottom:10px; }
        .appt-booked-at { font-size:11px; color:var(--ikea-gray); margin-top:10px; }

        /* Status pill */
        .appt-status-pill {
            display: inline-block;
            padding: 5px 16px;
            border-radius: 40px;
            font-size: 12px;
            font-weight: 700;
            text-transform: capitalize;
        }
        .appt-status-pill.appt-status-pending   { background:#fff3e0; color:#f57c00; }
        .appt-status-pill.appt-status-confirmed { background:#e3f2fd; color:#1565c0; }
        .appt-status-pill.appt-status-completed { background:#e8f5e9; color:#2e7d32; }
        .appt-status-pill.appt-status-cancelled { background:#ffebee; color:#CC0008; }

        /* Notice card */
        .appt-notice-card {
            background: #e3f2fd;
            border-color: #90caf9;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }
        .appt-notice-icon  { font-size:20px; flex-shrink:0; }
        .appt-notice-title { font-size:var(--text-sm); font-weight:800; color:#1565c0; margin-bottom:4px; }
        .appt-notice-desc  { font-size:12px; color:#1565c0; line-height:1.6; }

        /* Responsive */
        @media (max-width:900px) {
            .appt-manage-page { grid-template-columns:1fr; padding:var(--space-md); }
            .appt-manage-sidebar { order:-1; }
            .appt-form-grid { grid-template-columns:1fr; }
            .appt-info-grid { grid-template-columns:1fr; }
        }
    </style>

</x-app-layout>