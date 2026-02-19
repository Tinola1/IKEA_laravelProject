<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $admin = Role::create(['name' => 'admin']);
        $staff = Role::create(['name' => 'staff']);
        $customer = Role::create(['name' => 'customer']);

        // Create default admin user
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@ikea.com',
            'password' => Hash::make('password'),
        ]);

        $user->assignRole('admin');
    }
}