<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'IKEA Admin',
                'email'    => 'admin@ikea.ph',
                'password' => 'admin1234',
                'role'     => 'admin',
            ],
            [
                'name'     => 'IKEA Staff',
                'email'    => 'staff@ikea.ph',
                'password' => 'staff1234',
                'role'     => 'staff',
            ],
            [
                'name'     => 'Test Customer',
                'email'    => 'customer@ikea.ph',
                'password' => 'customer1234',
                'role'     => 'customer',
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => Hash::make($data['password']),
                ]
            );

            // Sync role — replaces any existing role assignment
            $user->syncRoles([$data['role']]);
        }

        // ── Print credentials table to console ──────────────
        $this->command->newLine();
        $this->command->line('┌─────────────────────────────────────────────────────────────┐');
        $this->command->line('│                     SEEDED ACCOUNTS                         │');
        $this->command->line('├──────────┬───────────────────────┬──────────────┬───────────┤');
        $this->command->line('│  Role    │  Email                │  Password    │  Access   │');
        $this->command->line('├──────────┼───────────────────────┼──────────────┼───────────┤');
        $this->command->line('│  admin   │  admin@ikea.ph        │  admin1234   │  Full     │');
        $this->command->line('│  staff   │  staff@ikea.ph        │  staff1234   │  Orders   │');
        $this->command->line('│  customer│  customer@ikea.ph     │  customer1234│  Shop     │');
        $this->command->line('└──────────┴───────────────────────┴──────────────┴───────────┘');
        $this->command->newLine();
        $this->command->info('✅ 3 users seeded');
    }
}
