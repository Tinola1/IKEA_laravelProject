<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'user_id', 'staff_id', 'service_type',
        'appointment_date', 'appointment_time',
        'full_name', 'phone', 'email',
        'notes', 'room_size', 'status', 'staff_notes',
    ];

    protected $casts = [
        'appointment_date' => 'date',
    ];

    // ── Relationships ─────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    // ── Helpers ───────────────────────────────────────────────────
    public static function serviceTypes(): array
    {
        return [
            'kitchen_planning'     => '🍳 Kitchen Planning',
            'wardrobe_planning'    => '👕 Wardrobe / Closet Planning',
            'bedroom_planning'     => '🛏️ Bedroom Planning',
            'full_room_layout'     => '🏠 Full Room Layout Design',
            'interior_planning'    => '🛋️ Interior Planning',
            'general_consultation' => '📐 General Consultation',
        ];
    }

    public function serviceLabel(): string
    {
        return self::serviceTypes()[$this->service_type] ?? $this->service_type;
    }

    public function statusColor(): string
    {
        return match($this->status) {
            'pending'   => 'status-pending',
            'confirmed' => 'status-processing',
            'completed' => 'status-completed',
            'cancelled' => 'status-cancelled',
            default     => '',
        };
    }

    // Available time slots
    public static function timeSlots(): array
    {
        return [
            '09:00' => '9:00 AM',
            '10:00' => '10:00 AM',
            '11:00' => '11:00 AM',
            '13:00' => '1:00 PM',
            '14:00' => '2:00 PM',
            '15:00' => '3:00 PM',
            '16:00' => '4:00 PM',
        ];
    }
}