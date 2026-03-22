<x-app-layout>
    <x-slot name="header">
        <div class="appt-page-header">
            <div>
                <h2 class="appt-page-title">Book a Showroom Appointment</h2>
                <p class="appt-page-subtitle">Reserve time with an IKEA planner to design your perfect space.</p>
            </div>
            <a href="{{ route('appointments.index') }}" class="appt-btn-outline">My Appointments</a>
        </div>
    </x-slot>

    <div class="appt-page">
    <form method="POST" action="{{ route('appointments.store') }}" class="appt-layout" id="appointmentForm" novalidate>
            @csrf
            {{-- ── LEFT COLUMN ─────────────────────────────────── --}}
            <div class="appt-main">

                {{-- Service Type --}}
                <div class="appt-card">
                    <div class="appt-card-label">01</div>
                    <h3 class="appt-card-title">What do you need help with?</h3>
                    @error('service_type')
                        <p class="appt-error">{{ $message }}</p>
                    @enderror

                    <div class="appt-service-grid">
                        @php
                            $services = [
                                'kitchen_planning'     => ['icon' => '🍳', 'label' => 'Kitchen Planning'],
                                'wardrobe_planning'    => ['icon' => '👕', 'label' => 'Wardrobe Planning'],
                                'bedroom_planning'     => ['icon' => '🛏️', 'label' => 'Bedroom Planning'],
                                'full_room_layout'     => ['icon' => '🏠', 'label' => 'Full Room Layout'],
                                'interior_planning'    => ['icon' => '🛋️', 'label' => 'Interior Planning'],
                                'general_consultation' => ['icon' => '📐', 'label' => 'General Consultation'],
                            ];
                        @endphp
                        @foreach($services as $value => $svc)
                            <label class="appt-svc {{ old('service_type') === $value ? 'is-selected' : '' }}">
                                <input type="radio" name="service_type" value="{{ $value }}"
                                       {{ old('service_type') === $value ? 'checked' : '' }}
                                       onchange="selectService(this)">
                                <div class="appt-svc-icon">{{ $svc['icon'] }}</div>
                                <div class="appt-svc-label">{{ $svc['label'] }}</div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Date & Time --}}
                <div class="appt-card">
                    <div class="appt-card-label">02</div>
                    <h3 class="appt-card-title">Pick a Date & Time</h3>

                    <div class="appt-two-col">
                        <div class="appt-field">
                            <label class="appt-label">Preferred Date</label>
                            <input type="date"
                                   name="appointment_date"
                                   class="appt-input {{ $errors->has('appointment_date') ? 'is-error' : '' }}"
                                   value="{{ old('appointment_date') }}"
                                   min="{{ now()->addDay()->format('Y-m-d') }}"
                                   required>
                            @error('appointment_date')
                                <span class="appt-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="appt-field">
                            <label class="appt-label">Preferred Time</label>
                            <select name="appointment_time"
                                    class="appt-input {{ $errors->has('appointment_time') ? 'is-error' : '' }}"
                                    required>
                                <option value="">Select a time slot...</option>
                                @foreach($timeSlots as $value => $label)
                                    <option value="{{ $value }}" {{ old('appointment_time') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('appointment_time')
                                <span class="appt-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="appt-location-hint">
                        📍 IKEA Philippines Showroom &nbsp;·&nbsp; Mon–Sun, 9:00 AM – 5:00 PM
                    </div>
                </div>

                {{-- Contact Info --}}
                <div class="appt-card">
                    <div class="appt-card-label">03</div>
                    <h3 class="appt-card-title">Your Details</h3>

                    <div class="appt-two-col">
                        <div class="appt-field">
                            <label class="appt-label">Full Name</label>
                            <input type="text" name="full_name"
                                   class="appt-input {{ $errors->has('full_name') ? 'is-error' : '' }}"
                                   value="{{ old('full_name', $user->name) }}"
                                   placeholder="Your full name"
                                   required>
                            @error('full_name')<span class="appt-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="appt-field">
                            <label class="appt-label">Phone Number</label>
                            <input type="text" name="phone"
                                   class="appt-input {{ $errors->has('phone') ? 'is-error' : '' }}"
                                   value="{{ old('phone', $user->phone) }}"
                                   placeholder="+63 912 345 6789"
                                   required>
                            @error('phone')<span class="appt-error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="appt-field">
                        <label class="appt-label">Email Address</label>
                        <input type="email" name="email"
                               class="appt-input {{ $errors->has('email') ? 'is-error' : '' }}"
                               value="{{ old('email', $user->email) }}"
                               placeholder="your@email.com"
                               required>
                        @error('email')<span class="appt-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="appt-two-col">
                        <div class="appt-field">
                            <label class="appt-label">Room Size <span class="appt-optional">(optional)</span></label>
                            <input type="text" name="room_size"
                                   class="appt-input"
                                   value="{{ old('room_size') }}"
                                   placeholder="e.g. 4m × 5m">
                        </div>
                        <div class="appt-field" style="display:flex;align-items:flex-end;">
                            <p class="appt-hint">Helps our planner prepare the right furniture suggestions for your space.</p>
                        </div>
                    </div>

                    <div class="appt-field">
                        <label class="appt-label">Additional Notes <span class="appt-optional">(optional)</span></label>
                        <textarea name="notes"
                                  class="appt-input appt-textarea"
                                  rows="3"
                                  placeholder="Style preferences, budget range, specific furniture you have in mind...">{{ old('notes') }}</textarea>
                    </div>
                </div>

            </div>

            {{-- ── RIGHT SIDEBAR ─────────────────────────────── --}}
            <div class="appt-sidebar">

                {{-- Submit --}}
                <div class="appt-card appt-card-submit">
                    <button type="submit" class="appt-submit-btn">
                        📅 Book Appointment
                    </button>
                    <a href="{{ route('shop.index') }}" class="appt-cancel-link">Cancel</a>
                </div>

                {{-- What to expect --}}
                <div class="appt-card">
                    <h4 class="appt-sidebar-title">What to expect</h4>
                    <div class="appt-steps">
                        <div class="appt-step">
                            <div class="appt-step-num">1</div>
                            <div class="appt-step-text">Book your preferred date & time</div>
                        </div>
                        <div class="appt-step">
                            <div class="appt-step-num">2</div>
                            <div class="appt-step-text">Receive email confirmation</div>
                        </div>
                        <div class="appt-step">
                            <div class="appt-step-num">3</div>
                            <div class="appt-step-text">IKEA planner reviews your request</div>
                        </div>
                        <div class="appt-step">
                            <div class="appt-step-num">4</div>
                            <div class="appt-step-text">Get confirmed appointment details</div>
                        </div>
                        <div class="appt-step">
                            <div class="appt-step-num">5</div>
                            <div class="appt-step-text">Meet your planner at the showroom</div>
                        </div>
                    </div>
                </div>

                {{-- Tip --}}
                <div class="appt-card appt-card-tip">
                    <div class="appt-tip-icon">💡</div>
                    <div>
                        <div class="appt-sidebar-title" style="margin-bottom:6px;">Pro Tip</div>
                        <p class="appt-hint">Bring room measurements and photos of your current space to get the most out of your planning session.</p>
                    </div>
                </div>

            </div>

        </form>
    </div>

    <style>
        /* ── PAGE WRAPPER ─────────────────────────────────────── */
        .appt-page {
            padding: var(--space-lg);
            max-width: 1100px;
            margin: 0 auto;
        }

        /* Page header */
        .appt-page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: var(--space-md);
            flex-wrap: wrap;
        }
        .appt-page-title {
            font-size: var(--text-2xl);
            font-weight: 900;
            color: var(--ikea-dark);
            letter-spacing: -0.5px;
        }
        .appt-page-subtitle {
            font-size: var(--text-sm);
            color: var(--ikea-gray);
            margin-top: 2px;
        }
        .appt-btn-outline {
            padding: 9px 20px;
            border: 2px solid var(--ikea-blue);
            border-radius: 40px;
            color: var(--ikea-blue);
            font-size: var(--text-sm);
            font-weight: 700;
            text-decoration: none;
            transition: all var(--transition-fast);
            white-space: nowrap;
        }
        .appt-btn-outline:hover { background: var(--ikea-blue); color: white; }

        /* ── TWO-COLUMN LAYOUT ───────────────────────────────── */
        .appt-layout {
            display: grid;
            grid-template-columns: 1fr 280px;
            gap: var(--space-md);
            align-items: start;
            margin-top: var(--space-md);
        }
        .appt-main    { display: flex; flex-direction: column; gap: var(--space-md); }
        .appt-sidebar { display: flex; flex-direction: column; gap: var(--space-md); }

        /* ── CARD ────────────────────────────────────────────── */
        .appt-card {
            background: white;
            border: 1px solid var(--ikea-border);
            border-radius: 10px;
            padding: var(--space-md);
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            position: relative;
        }
        .appt-card-label {
            position: absolute;
            top: -12px;
            left: var(--space-md);
            background: var(--ikea-blue);
            color: white;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 1px;
            padding: 3px 10px;
            border-radius: 40px;
        }
        .appt-card-title {
            font-size: var(--text-lg);
            font-weight: 800;
            color: var(--ikea-dark);
            margin-bottom: var(--space-sm);
            padding-top: 6px;
        }
        .appt-card-submit { border: 2px solid var(--ikea-yellow); }
        .appt-card-tip {
            background: #fffde7;
            border-color: #ffe082;
            display: flex;
            gap: var(--space-sm);
            align-items: flex-start;
        }
        .appt-tip-icon { font-size: 22px; flex-shrink: 0; }

        /* ── SERVICE CARDS ───────────────────────────────────── */
        .appt-service-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        .appt-svc {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: var(--space-sm) 10px;
            border: 2px solid var(--ikea-border);
            border-radius: 10px;
            cursor: pointer;
            text-align: center;
            transition: all 0.15s ease;
            background: white;
        }
        .appt-svc:hover {
            border-color: var(--ikea-blue);
            background: #f0f7ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,88,163,0.12);
        }
        .appt-svc.is-selected {
            border-color: var(--ikea-blue);
            background: #e3f2fd;
            box-shadow: 0 0 0 3px rgba(0,88,163,0.15);
        }
        .appt-svc input[type="radio"] { display: none; }
        .appt-svc-icon  { font-size: 32px; line-height: 1; }
        .appt-svc-label { font-size: 12px; font-weight: 700; color: var(--ikea-dark); line-height: 1.3; }

        /* ── FORM FIELDS ─────────────────────────────────────── */
        .appt-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-sm); }
        .appt-field   { display: flex; flex-direction: column; gap: 6px; margin-bottom: var(--space-sm); }
        .appt-field:last-child { margin-bottom: 0; }
        .appt-label   { font-size: var(--text-sm); font-weight: 700; color: var(--ikea-dark); }
        .appt-optional { font-weight: 400; color: var(--ikea-gray); }

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
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .appt-input:focus {
            outline: none;
            border-color: var(--ikea-blue);
            box-shadow: 0 0 0 3px rgba(0,88,163,0.1);
        }
        .appt-input.is-error { border-color: #CC0008; }
        .appt-textarea { height: auto; padding: 12px 14px; resize: vertical; }
        .appt-error    { font-size: var(--text-sm); color: #CC0008; font-weight: 600; }
        .appt-hint     { font-size: 12px; color: var(--ikea-gray); line-height: 1.5; }

        .appt-location-hint {
            margin-top: var(--space-xs);
            font-size: 12px;
            color: var(--ikea-gray);
            font-weight: 600;
            background: var(--ikea-light);
            padding: 8px 12px;
            border-radius: 6px;
        }

        /* ── SUBMIT ──────────────────────────────────────────── */
        .appt-submit-btn {
            width: 100%;
            height: 52px;
            background: var(--ikea-yellow);
            color: var(--ikea-dark);
            border: none;
            border-radius: 40px;
            font-size: var(--text-md);
            font-weight: 800;
            font-family: 'Noto Sans', sans-serif;
            cursor: pointer;
            transition: background 0.15s ease, transform 0.15s ease;
            margin-bottom: var(--space-xs);
        }
        .appt-submit-btn:hover { background: #f0cc00; transform: translateY(-1px); }
        .appt-cancel-link {
            display: block;
            text-align: center;
            font-size: var(--text-sm);
            font-weight: 700;
            color: var(--ikea-gray);
            text-decoration: none;
            padding: 8px;
        }
        .appt-cancel-link:hover { color: var(--ikea-dark); }

        /* ── SIDEBAR ─────────────────────────────────────────── */
        .appt-sidebar-title {
            font-size: var(--text-base);
            font-weight: 800;
            color: var(--ikea-dark);
            margin-bottom: var(--space-sm);
        }
        .appt-steps { display: flex; flex-direction: column; gap: 10px; }
        .appt-step  { display: flex; align-items: flex-start; gap: 10px; }
        .appt-step-num {
            width: 22px;
            height: 22px;
            background: var(--ikea-blue);
            color: white;
            border-radius: 50%;
            font-size: 11px;
            font-weight: 900;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 1px;
        }
        .appt-step-text { font-size: 13px; color: var(--ikea-dark); font-weight: 600; line-height: 1.4; }

        /* ── RESPONSIVE ──────────────────────────────────────── */
        @media (max-width: 900px) {
            .appt-layout  { grid-template-columns: 1fr; }
            .appt-sidebar { order: -1; }
        }
        @media (max-width: 600px) {
            .appt-page        { padding: var(--space-md); }
            .appt-service-grid { grid-template-columns: repeat(2, 1fr); }
            .appt-two-col     { grid-template-columns: 1fr; }
            .appt-page-header { flex-direction: column; align-items: flex-start; }
        }
    </style>

    @push('scripts')
    <script>
        function selectService(radio) {
            document.querySelectorAll('.appt-svc').forEach(c => c.classList.remove('is-selected'));
            radio.closest('.appt-svc').classList.add('is-selected');
        }
    document.getElementById('appointmentForm').addEventListener('submit', function(e) {
        let valid = true;
        document.querySelectorAll('.js-error').forEach(el => el.remove());
        document.querySelectorAll('.appt-input').forEach(el => el.style.borderColor = '');

        const rules = [
            { name: 'service_type',     label: 'Service type',    select: true },
            { name: 'appointment_date', label: 'Date',            required: true, futureDate: true },
            { name: 'appointment_time', label: 'Time slot',       select: true },
            { name: 'full_name',        label: 'Full name',       required: true, minLength: 2 },
            { name: 'phone',            label: 'Phone number',    required: true, minLength: 7 },
            { name: 'email',            label: 'Email address',   required: true, email: true },
        ];

        rules.forEach(rule => {
            const input = document.querySelector('[name="' + rule.name + '"]');
            if (!input) return;
            const val = input.value.trim();
            let error = null;

            if ((rule.required || rule.select) && !val) {
                error = rule.label + ' is required.';
            } else if (rule.minLength && val.length < rule.minLength) {
                error = rule.label + ' is too short.';
            } else if (rule.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
                error = 'Please enter a valid email address.';
            } else if (rule.futureDate && val) {
                const selected = new Date(val);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                if (selected <= today) error = 'Please select a future date.';
            }

            if (error) {
                valid = false;
                input.style.borderColor = '#CC0008';
                const msg = document.createElement('p');
                msg.className = 'js-error';
                msg.style.cssText = 'color:#CC0008;font-size:12px;margin-top:4px;font-weight:600;';
                msg.textContent = error;
                input.parentNode.appendChild(msg);
            }
        });

        if (!valid) e.preventDefault();
    });

    document.querySelectorAll('.appt-input').forEach(input => {
        ['input', 'change'].forEach(evt => {
            input.addEventListener(evt, function() {
                this.style.borderColor = '';
                const err = this.parentNode.querySelector('.js-error');
                if (err) err.remove();
            });
        });
    });
    </script>
    @endpush

</x-app-layout>