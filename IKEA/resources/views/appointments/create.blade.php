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
        <form method="POST" action="{{ route('appointments.store') }}" class="appt-layout">
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

    @push('scripts')
    <script>
        function selectService(radio) {
            document.querySelectorAll('.appt-svc').forEach(c => c.classList.remove('is-selected'));
            radio.closest('.appt-svc').classList.add('is-selected');
        }
    </script>
    @endpush

</x-app-layout>