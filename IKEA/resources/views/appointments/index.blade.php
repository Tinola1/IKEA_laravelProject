<x-app-layout>
    <x-slot name="header">
        <div class="admin-page-header">
            <div>
                <h2 class="admin-page-title">My Appointments</h2>
                <p class="admin-page-subtitle">Your showroom planning sessions with IKEA.</p>
            </div>
            <a href="{{ route('appointments.create') }}" class="admin-btn-primary">+ Book Appointment</a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="admin-flash success" style="margin:var(--space-md) var(--space-lg) 0;">
            {{ session('success') }}
        </div>
    @endif

    <div class="admin-dashboard">
        <div class="admin-card" style="padding:var(--space-md);">

            @if($appointments->isEmpty())
                <div class="admin-empty" style="padding:var(--space-2xl);">
                    <span style="font-size:48px;">📅</span>
                    <p style="font-size:var(--text-md);font-weight:700;color:var(--ikea-dark);margin-top:8px;">
                        No appointments yet
                    </p>
                    <p>Book a showroom appointment with an IKEA planner to design your perfect space.</p>
                    <a href="{{ route('appointments.create') }}" class="cta-main" style="display:inline-block;margin-top:16px;">
                        Book Now
                    </a>
                </div>
            @else
                <table class="admin-table" style="width:100%;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Service</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                            <th>Booked</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appointments as $appt)
                            <tr>
                                <td class="order-id">#{{ str_pad($appt->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td>
                                    <div class="table-product-name">{{ $appt->serviceLabel() }}</div>
                                </td>
                                <td>
                                    <div style="font-weight:700;">{{ $appt->appointment_date->format('M d, Y') }}</div>
                                    <div style="font-size:12px;color:var(--ikea-gray);">
                                        {{ \App\Models\Appointment::timeSlots()[$appt->appointment_time] ?? $appt->appointment_time }}
                                    </div>
                                </td>
                                <td>
                                    <span class="order-status-badge {{ $appt->statusColor() }}">
                                        {{ ucfirst($appt->status) }}
                                    </span>
                                </td>
                                <td class="order-date">{{ $appt->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('appointments.show', $appt) }}" class="table-action-link">View</a>
                                        @if(in_array($appt->status, ['pending', 'confirmed']))
                                            <form method="POST" action="{{ route('appointments.cancel', $appt) }}"
                                                  onsubmit="return confirm('Cancel this appointment?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="table-action-delete">Cancel</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($appointments->hasPages())
                    <div class="shop-pagination" style="padding:var(--space-md) 0 0;">
                        {{ $appointments->links() }}
                    </div>
                @endif
            @endif

        </div>
    </div>

</x-app-layout>