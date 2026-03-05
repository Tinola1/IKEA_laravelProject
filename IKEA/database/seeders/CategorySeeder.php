<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Sofas & Armchairs',
                'slug'        => 'sofas-armchairs',
                'description' => 'Comfortable sofas, loveseats, and armchairs for your living room.',
            ],
            [
                'name'        => 'Beds & Mattresses',
                'slug'        => 'beds-mattresses',
                'description' => 'Bed frames, mattresses, and bedroom storage solutions.',
            ],
            [
                'name'        => 'Tables & Desks',
                'slug'        => 'tables-desks',
                'description' => 'Dining tables, coffee tables, and work desks for every space.',
            ],
            [
                'name'        => 'Chairs',
                'slug'        => 'chairs',
                'description' => 'Dining chairs, office chairs, and accent chairs.',
            ],
            [
                'name'        => 'Kitchen & Dining',
                'slug'        => 'kitchen-dining',
                'description' => 'Kitchen cabinets, dining sets, and kitchen accessories.',
            ],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        $this->command->info('✅ 5 categories seeded');
    }
}
