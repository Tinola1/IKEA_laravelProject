<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UserAddress;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'              => 'IKEA Admin',
                'email'             => 'admin@ikea.ph',
                'password'          => 'admin1234',
                'role'              => 'admin',
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'IKEA Staff',
                'email'             => 'staff@ikea.ph',
                'password'          => 'staff1234',
                'role'              => 'staff',
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Shop Demo User',
                'email'             => 'user1@mail.com',
                'password'          => 'password123',
                'role'              => 'customer',
                'email_verified_at' => now(),
                'phone'             => '09171111111',
                'payment_method'    => 'gcash',
                'address'           => [
                    'label'     => 'Home',
                    'full_name' => 'Shop Demo User',
                    'phone'     => '09171111111',
                    'address'   => '1 Ayala Avenue, Brgy. San Lorenzo',
                    'city'      => 'Makati',
                    'province'  => 'Metro Manila',
                    'zip_code'  => '1226',
                ],
            ],
            [
                'name'              => 'Orders Demo User',
                'email'             => 'user2@mail.com',
                'password'          => 'password123',
                'role'              => 'customer',
                'email_verified_at' => now(),
                'phone'             => '09282222222',
                'payment_method'    => 'cod',
                'address'           => [
                    'label'     => 'Home',
                    'full_name' => 'Orders Demo User',
                    'phone'     => '09282222222',
                    'address'   => '2 EDSA, Brgy. Wack-Wack',
                    'city'      => 'Mandaluyong',
                    'province'  => 'Metro Manila',
                    'zip_code'  => '1550',
                ],
            ],
            [
                'name'              => 'Reviews Demo User',
                'email'             => 'user3@mail.com',
                'password'          => 'password123',
                'role'              => 'customer',
                'email_verified_at' => now(),
                'phone'             => '09393333333',
                'payment_method'    => 'bank_transfer',
                'address'           => [
                    'label'     => 'Home',
                    'full_name' => 'Reviews Demo User',
                    'phone'     => '09393333333',
                    'address'   => '3 Taft Avenue, Brgy. Malate',
                    'city'      => 'Manila',
                    'province'  => 'Metro Manila',
                    'zip_code'  => '1004',
                ],
            ],
            [
                'name'              => 'Appointments Demo User',
                'email'             => 'user4@mail.com',
                'password'          => 'password123',
                'role'              => 'customer',
                'email_verified_at' => now(),
                'phone'             => '09504444444',
                'payment_method'    => 'gcash',
                'address'           => [
                    'label'     => 'Home',
                    'full_name' => 'Appointments Demo User',
                    'phone'     => '09504444444',
                    'address'   => '4 Katipunan Avenue, Brgy. Loyola Heights',
                    'city'      => 'Quezon City',
                    'province'  => 'Metro Manila',
                    'zip_code'  => '1108',
                ],
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'password'          => Hash::make($data['password']),
                    'email_verified_at' => $data['email_verified_at'],
                    'phone'             => $data['phone']          ?? null,
                    'payment_method'    => $data['payment_method'] ?? null,
                ]
            );

            $user->syncRoles([$data['role']]);

            // Seed default address for customers
            if (isset($data['address'])) {
                UserAddress::firstOrCreate(
                    ['user_id' => $user->id, 'label' => $data['address']['label']],
                    array_merge($data['address'], [
                        'user_id'    => $user->id,
                        'is_default' => true,
                    ])
                );
            }
        }

        $this->command->newLine();
        $this->command->line('┌──────────────────────┬────────────────────────┬───────────────┬───────────────────────────┐');
        $this->command->line('│  Role                │  Email                 │  Password     │  Demo Purpose             │');
        $this->command->line('├──────────────────────┼────────────────────────┼───────────────┼───────────────────────────┤');
        $this->command->line('│  admin               │  admin@ikea.ph         │  admin1234    │  Full admin panel         │');
        $this->command->line('│  staff               │  staff@ikea.ph         │  staff1234    │  Orders & appointments    │');
        $this->command->line('│  customer (user1)    │  user1@mail.com        │  password123  │  Shop & Cart              │');
        $this->command->line('│  customer (user2)    │  user2@mail.com        │  password123  │  Orders & Checkout        │');
        $this->command->line('│  customer (user3)    │  user3@mail.com        │  password123  │  Reviews & Products       │');
        $this->command->line('│  customer (user4)    │  user4@mail.com        │  password123  │  Appointments & Profile   │');
        $this->command->line('└──────────────────────┴────────────────────────┴───────────────┴───────────────────────────┘');
        $this->command->newLine();
        $this->command->info('✅ 6 users seeded with addresses');
    }
}