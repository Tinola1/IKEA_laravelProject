<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Appointment Received — IKEA Philippines</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Helvetica Neue', Arial, sans-serif; background: #f5f5f0; color: #111; padding: 32px 16px; }
        .wrapper { max-width: 600px; margin: 0 auto; }

        .email-header { background: #0058A3; border-radius: 10px 10px 0 0; padding: 32px; text-align: center; }
        .logo-box { display: inline-block; background: #FFDB00; color: #0058A3; font-weight: 900; font-size: 28px; letter-spacing: 2px; padding: 6px 18px; border-radius: 4px; margin-bottom: 20px; }
        .header-icon { font-size: 48px; display: block; margin-bottom: 12px; }
        .email-header h1 { color: white; font-size: 24px; font-weight: 800; }
        .email-header p  { color: rgba(255,255,255,.85); font-size: 15px; margin-top: 8px; }

        .email-body { background: white; padding: 32px; }

        .appt-meta { display: table; width: 100%; background: #f5f5f0; border-radius: 8px; padding: 16px 20px; margin-bottom: 24px; }
        .meta-item  { display: table-cell; text-align: center; }
        .meta-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #767676; }
        .meta-value { font-size: 15px; font-weight: 800; color: #111; margin-top: 3px; }

        .message-box { background: #e3f2fd; color: #1565c0; border-left: 4px solid #0058A3; border-radius: 8px; padding: 16px 20px; margin-bottom: 28px; font-size: 14px; line-height: 1.7; }

        .section-title { font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; color: #767676; border-bottom: 2px solid #0058A3; padding-bottom: 8px; margin-bottom: 16px; }

        .detail-row { display: table; width: 100%; padding: 8px 0; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { display: table-cell; width: 40%; color: #767676; font-weight: 600; }
        .detail-value { display: table-cell; font-weight: 700; color: #111; }

        .notes-box { background: #fffde7; border-left: 3px solid #FFDB00; border-radius: 4px; padding: 12px 16px; margin: 20px 0; font-size: 13px; color: #555; line-height: 1.6; }

        .cta-wrap { text-align: center; margin: 28px 0; }
        .cta-btn  { display: inline-block; background: #FFDB00; color: #111; font-weight: 800; font-size: 15px; padding: 14px 36px; border-radius: 40px; text-decoration: none; letter-spacing: .3px; }

        .email-footer { background: #111; border-radius: 0 0 10px 10px; padding: 24px 32px; text-align: center; }
        .email-footer p { color: rgba(255,255,255,.5); font-size: 12px; line-height: 1.7; }
        .email-footer a { color: #FFDB00; text-decoration: none; }
    </style>
</head>
<body>
<div class="wrapper">

    <div class="email-header">
        <div class="logo-box">IKEA</div>
        <span class="header-icon">📅</span>
        <h1>Appointment Received!</h1>
        <p>Hi {{ $appointment->full_name }}, we've received your showroom appointment request.</p>
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
                <div class="meta-value">{{ \App\Models\Appointment::timeSlots()[$appointment->appointment_time] ?? $appointment->appointment_time }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Status</div>
                <div class="meta-value" style="color:#f57c00;">Pending</div>
            </div>
        </div>

        <div class="message-box">
            Your appointment request has been received and is pending confirmation from an IKEA planner.
            We'll send you another email once your schedule has been confirmed. In the meantime, feel free
            to browse our shop and prepare any room measurements or photos you'd like to bring.
        </div>

        <div class="section-title">Appointment Details</div>

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
        <div class="detail-row">
            <div class="detail-label">Name</div>
            <div class="detail-value">{{ $appointment->full_name }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Phone</div>
            <div class="detail-value">{{ $appointment->phone }}</div>
        </div>
        @if($appointment->room_size)
        <div class="detail-row">
            <div class="detail-label">Room Size</div>
            <div class="detail-value">{{ $appointment->room_size }}</div>
        </div>
        @endif

        @if($appointment->notes)
            <div class="notes-box">
                <strong>Your Notes:</strong> {{ $appointment->notes }}
            </div>
        @endif

        <div class="cta-wrap">
            <a href="{{ url('/appointments/' . $appointment->id) }}" class="cta-btn">
                View Appointment →
            </a>
        </div>

        <p style="font-size:13px;color:#767676;text-align:center;line-height:1.6;">
            Need to cancel or have questions? Visit your
            <a href="{{ url('/appointments') }}" style="color:#0058A3;font-weight:700;">Appointments page</a>
            or reply to this email.
        </p>

    </div>

    <div class="email-footer">
        <p>
            © {{ date('Y') }} IKEA Philippines. All rights reserved.<br>
            <a href="{{ url('/') }}">ikea.ph</a> &nbsp;·&nbsp;
            <a href="{{ url('/appointments') }}">My Appointments</a> &nbsp;·&nbsp;
            <a href="{{ url('/shop') }}">Shop</a>
        </p>
    </div>

</div>
</body>
</html>