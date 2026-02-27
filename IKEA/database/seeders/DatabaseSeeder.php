<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminRole    = Role::firstOrCreate(['name' => 'admin']);
    $staffRole    = Role::firstOrCreate(['name' => 'staff']);
    $customerRole = Role::firstOrCreate(['name' => 'customer']);
    }
}
