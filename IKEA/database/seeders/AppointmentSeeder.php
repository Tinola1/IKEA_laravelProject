<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\User;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $user4 = User::where('email', 'user4@mail.com')->firstOrFail();
        $user1 = User::where('email', 'user1@mail.com')->firstOrFail();
        $staff = User::where('email', 'staff@ikea.ph')->firstOrFail();

        $appointments = [
            [
                'user_id'          => $user4->id,
                'staff_id'         => $staff->id,
                'service_type'     => 'bedroom_planning',
                'appointment_date' => now()->addDays(5)->format('Y-m-d'),
                'appointment_time' => '10:00',
                'full_name'        => 'Appointments Demo User',
                'phone'            => '09504444444',
                'email'            => 'user4@mail.com',
                'notes'            => 'Looking to redesign the master bedroom with built-in storage.',
                'room_size'        => '4m x 5m',
                'status'           => 'confirmed',
                'staff_notes'      => 'Bring bedroom catalogue and storage solutions brochure.',
            ],
            [
                'user_id'          => $user4->id,
                'staff_id'         => null,
                'service_type'     => 'kitchen_planning',
                'appointment_date' => now()->addDays(12)->format('Y-m-d'),
                'appointment_time' => '14:00',
                'full_name'        => 'Appointments Demo User',
                'phone'            => '09504444444',
                'email'            => 'user4@mail.com',
                'notes'            => 'Full kitchen remodel — need help with cabinet layout and countertop selection.',
                'room_size'        => '3m x 4m',
                'status'           => 'pending',
                'staff_notes'      => null,
            ],
            [
                'user_id'          => $user1->id,
                'staff_id'         => $staff->id,
                'service_type'     => 'full_room_layout',
                'appointment_date' => now()->subDays(10)->format('Y-m-d'),
                'appointment_time' => '13:00',
                'full_name'        => 'Shop Demo User',
                'phone'            => '09171111111',
                'email'            => 'user1@mail.com',
                'notes'            => 'New apartment, starting from scratch.',
                'room_size'        => '6m x 7m',
                'status'           => 'completed',
                'staff_notes'      => 'Customer decided on EKTORP sofa and LACK coffee table combination.',
            ],
            [
                'user_id'          => $user1->id,
                'staff_id'         => null,
                'service_type'     => 'wardrobe_planning',
                'appointment_date' => now()->subDays(3)->format('Y-m-d'),
                'appointment_time' => '11:00',
                'full_name'        => 'Shop Demo User',
                'phone'            => '09171111111',
                'email'            => 'user1@mail.com',
                'notes'            => null,
                'room_size'        => null,
                'status'           => 'cancelled',
                'staff_notes'      => null,
            ],
        ];

        foreach ($appointments as $data) {
            Appointment::create($data);
        }

        $this->command->info('✅ 4 appointments seeded');
    }
}