<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Appointment Update — IKEA Philippines</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Helvetica Neue',Arial,sans-serif; background:#f5f5f0; color:#111; padding:32px 16px; }
        .wrapper { max-width:600px; margin:0 auto; }
        .email-header { border-radius:10px 10px 0 0; padding:32px; text-align:center; }
        .email-header.pending   { background:#f57c00; }
        .email-header.confirmed { background:#0058A3; }
        .email-header.completed { background:#2e7d32; }
        .email-header.cancelled { background:#CC0008; }
        .logo-box { display:inline-block; background:#FFDB00; color:#0058A3; font-weight:900; font-size:28px; letter-spacing:2px; padding:6px 18px; border-radius:4px; margin-bottom:20px; }
        .status-icon { font-size:48px; display:block; margin-bottom:12px; }
        .email-header h1 { color:white; font-size:24px; font-weight:800; }
        .email-header p  { color:rgba(255,255,255,.85); font-size:15px; margin-top:8px; }
        .email-body { background:white; padding:32px; }
        .appt-meta { display:table; width:100%; background:#f5f5f0; border-radius:8px; padding:16px 20px; margin-bottom:24px; }
        .meta-item { display:table-cell; text-align:center; }
        .meta-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#767676; }
        .meta-value { font-size:15px; font-weight:800; color:#111; margin-top:3px; }
        .status-badge { display:inline-block; padding:4px 14px; border-radius:40px; font-size:12px; font-weight:700; text-transform:uppercase; }
        .status-badge.pending   { background:#fff3e0; color:#f57c00; }
        .status-badge.confirmed { background:#e3f2fd; color:#1565c0; }
        .status-badge.completed { background:#e8f5e9; color:#2e7d32; }
        .status-badge.cancelled { background:#ffebee; color:#CC0008; }
        .message-box { border-radius:8px; padding:16px 20px; margin-bottom:28px; font-size:14px; line-height:1.7; }
        .message-box.confirmed { background:#e3f2fd; color:#1565c0; border-left:4px solid #0058A3; }
        .message-box.completed { background:#e8f5e9; color:#2e7d32; border-left:4px solid #4caf50; }
        .message-box.cancelled { background:#ffebee; color:#CC0008; border-left:4px solid #CC0008; }
        .message-box.pending   { background:#fff3e0; color:#f57c00; border-left:4px solid #f57c00; }
        .staff-notes { background:#f5f5f0; border-radius:8px; padding:14px 18px; margin-bottom:24px; font-size:14px; color:#444; line-height:1.6; }
        .section-title { font-size:13px; font-weight:800; text-transform:uppercase; letter-spacing:.5px; color:#767676; border-bottom:2px solid #e5e5e5; padding-bottom:8px; margin-bottom:16px; }
        .detail-row { display:table; width:100%; padding:8px 0; border-bottom:1px solid #f0f0f0; font-size:14px; }
        .detail-row:last-child { border-bottom:none; }
        .detail-label { display:table-cell; width:40%; color:#767676; font-weight:600; }
        .detail-value { display:table-cell; font-weight:700; color:#111; }
        .cta-wrap { text-align:center; margin:28px 0; }
        .cta-btn { display:inline-block; background:#FFDB00; color:#111; font-weight:800; font-size:15px; padding:14px 36px; border-radius:40px; text-decoration:none; }
        .email-footer { background:#111; border-radius:0 0 10px 10px; padding:24px 32px; text-align:center; }
        .email-footer p { color:rgba(255,255,255,.5); font-size:12px; line-height:1.7; }
        .email-footer a { color:#FFDB00; text-decoration:none; }
    </style>
</head>
<body>
<div class="wrapper">

    @php
        $icons = ['pending'=>'🕐','confirmed'=>'✅','completed'=>'🎉','cancelled'=>'❌'];
        $messages = [
            'pending'   => 'Your appointment is back to pending. An IKEA planner will review and confirm your schedule soon.',
            'confirmed' => 'Great news! Your showroom appointment has been confirmed. Please arrive on time and bring your room measurements or photos.',
            'completed' => 'Your appointment has been marked as completed. We hope our IKEA planner helped you design your perfect space!',
            'cancelled' => 'Unfortunately your appointment has been cancelled. Please book a new appointment if you\'d still like to meet with an IKEA planner.',
        ];
        $icon    = $icons[$appointment->status]    ?? '📅';
        $message = $messages[$appointment->status] ?? 'Your appointment status has been updated.';
    @endphp

    <div class="email-header {{ $appointment->status }}">
        <div class="logo-box">IKEA</div>
        <span class="status-icon">{{ $icon }}</span>
        <h1>Appointment {{ ucfirst($appointment->status) }}</h1>
        <p>Hi {{ $appointment->full_name }}, your appointment status has been updated.</p>
    </div>

    <div class="email-body">

        <div class="appt-meta">
            <div class="meta-item">
                <div class="meta-label">Appointment #</div>
                <div class="meta-value">#{{ str_pad($appointment->id, 5, '0', STR_PAD_LEFT) }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Date</div>
                <div class="meta-value">{{ $appointment->appointment_date->format('M d, Y') }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Time</div>
                <div class="meta-value">
                    {{ \App\Models\Appointment::timeSlots()[$appointment->appointment_time] ?? $appointment->appointment_time }}
                </div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Status</div>
                <div class="meta-value">
                    <span class="status-badge {{ $appointment->status }}">{{ ucfirst($appointment->status) }}</span>
                </div>
            </div>
        </div>

        <div class="message-box {{ $appointment->status }}">
            {{ $message }}
        </div>

        @if($appointment->staff_notes)
            <div class="staff-notes">
                <strong>Message from IKEA Planner:</strong><br>
                {{ $appointment->staff_notes }}
            </div>
        @endif

        <div class="section-title">Appointment Summary</div>
        <div class="detail-row">
            <div class="detail-label">Service</div>
            <div class="detail-value">{{ $appointment->serviceLabel() }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Date & Time</div>
            <div class="detail-value">
                {{ $appointment->appointment_date->format('F d, Y') }} at
                {{ \App\Models\Appointment::timeSlots()[$appointment->appointment_time] ?? $appointment->appointment_time }}
            </div>
        </div>
        @if($appointment->staff)
        <div class="detail-row">
            <div class="detail-label">Your Planner</div>
            <div class="detail-value">{{ $appointment->staff->name }}</div>
        </div>
        @endif

        <div class="cta-wrap">
            <a href="{{ url('/appointments/' . $appointment->id) }}" class="cta-btn">
                View Appointment →
            </a>
        </div>

    </div>

    <div class="email-footer">
        <p>
            © {{ date('Y') }} IKEA Philippines. All rights reserved.<br>
            <a href="{{ url('/') }}">ikea.ph</a> &nbsp;·&nbsp;
            <a href="{{ url('/appointments') }}">My Appointments</a>
        </p>
    </div>

</div>
</body>
</html>