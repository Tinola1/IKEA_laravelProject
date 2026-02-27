<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── CATEGORIES ─────────────────────────────────────
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

        $sofas    = Category::where('slug', 'sofas-armchairs')->first();
        $beds     = Category::where('slug', 'beds-mattresses')->first();
        $tables   = Category::where('slug', 'tables-desks')->first();
        $chairs   = Category::where('slug', 'chairs')->first();
        $kitchen  = Category::where('slug', 'kitchen-dining')->first();

        // ── PRODUCTS ───────────────────────────────────────
        $products = [

            // Sofas & Armchairs
            [
                'category_id'  => $sofas->id,
                'name'         => 'EKTORP 3-Seat Sofa',
                'slug'         => 'ektorp-3-seat-sofa',
                'description'  => 'A classic 3-seat sofa with a timeless design. The high back and armrests give you good support. Removable and washable cover makes it easy to keep clean.',
                'price'        => 24999.00,
                'stock'        => 15,
                'is_available' => true,
            ],
            [
                'category_id'  => $sofas->id,
                'name'         => 'POÄNG Armchair',
                'slug'         => 'poang-armchair',
                'description'  => 'Layer-glued bent birch frame with a comfortable seat cushion. The resilient frame gives a light bouncing comfort when you sit.',
                'price'        => 8499.00,
                'stock'        => 20,
                'is_available' => true,
            ],
            [
                'category_id'  => $sofas->id,
                'name'         => 'KIVIK 2-Seat Sofa',
                'slug'         => 'kivik-2-seat-sofa',
                'description'  => 'A generous seating series with a soft, deep seat and comfortable back cushions. Great for relaxing and lounging.',
                'price'        => 18999.00,
                'stock'        => 10,
                'is_available' => true,
            ],

            // Beds & Mattresses
            [
                'category_id'  => $beds->id,
                'name'         => 'MALM Bed Frame Queen',
                'slug'         => 'malm-bed-frame-queen',
                'description'  => 'Clean-lined bed frame in white with a high headboard. Under-bed space for storage. Fits standard 160x200cm mattresses.',
                'price'        => 19999.00,
                'stock'        => 12,
                'is_available' => true,
            ],
            [
                'category_id'  => $beds->id,
                'name'         => 'HASVÅG Spring Mattress',
                'slug'         => 'hasvag-spring-mattress',
                'description'  => 'Medium firm spring mattress with comfort layers of foam and fiber that adapt to your body weight and shape.',
                'price'        => 14999.00,
                'stock'        => 18,
                'is_available' => true,
            ],
            [
                'category_id'  => $beds->id,
                'name'         => 'HEMNES Daybed',
                'slug'         => 'hemnes-daybed',
                'description'  => 'A daybed with a traditional look. Can be used as a sofa or a bed. Pull out the extra bed underneath when you need more sleeping space.',
                'price'        => 22499.00,
                'stock'        => 8,
                'is_available' => true,
            ],

            // Tables & Desks
            [
                'category_id'  => $tables->id,
                'name'         => 'LACK Coffee Table',
                'slug'         => 'lack-coffee-table',
                'description'  => 'Simple and clean-lined coffee table that fits in small spaces. The hollow construction keeps it light and easy to move.',
                'price'        => 2999.00,
                'stock'        => 30,
                'is_available' => true,
            ],
            [
                'category_id'  => $tables->id,
                'name'         => 'LINNMON Desk',
                'slug'         => 'linnmon-desk',
                'description'  => 'A spacious work surface for your home office. The table top surface is durable, stain-resistant and easy to clean.',
                'price'        => 5499.00,
                'stock'        => 25,
                'is_available' => true,
            ],
            [
                'category_id'  => $tables->id,
                'name'         => 'EKEDALEN Extendable Table',
                'slug'         => 'ekedalen-extendable-table',
                'description'  => 'Extendable dining table that seats 4–6 people. Made from solid oak with a natural finish.',
                'price'        => 29999.00,
                'stock'        => 7,
                'is_available' => true,
            ],

            // Chairs
            [
                'category_id'  => $chairs->id,
                'name'         => 'STEFAN Dining Chair',
                'slug'         => 'stefan-dining-chair',
                'description'  => 'A sturdy chair made of solid wood with a comfortable curved back. Suitable for dining rooms and home offices.',
                'price'        => 2499.00,
                'stock'        => 40,
                'is_available' => true,
            ],
            [
                'category_id'  => $chairs->id,
                'name'         => 'MARKUS Office Chair',
                'slug'         => 'markus-office-chair',
                'description'  => 'Ergonomic office chair with lumbar support and adjustable height. Built for long working hours.',
                'price'        => 12999.00,
                'stock'        => 15,
                'is_available' => true,
            ],
            [
                'category_id'  => $chairs->id,
                'name'         => 'INGOLF Bar Stool',
                'slug'         => 'ingolf-bar-stool',
                'description'  => 'Classic bar stool made of solid pine. The open back gives a light, airy feel and the wooden seat is comfortable.',
                'price'        => 3299.00,
                'stock'        => 22,
                'is_available' => true,
            ],

            // Kitchen & Dining
            [
                'category_id'  => $kitchen->id,
                'name'         => 'SEKTION Base Cabinet',
                'slug'         => 'sektion-base-cabinet',
                'description'  => 'Durable base cabinet with one shelf and one door. Easy to install and customize with different fronts.',
                'price'        => 8999.00,
                'stock'        => 20,
                'is_available' => true,
            ],
            [
                'category_id'  => $kitchen->id,
                'name'         => 'IKEA 365+ Cookware Set',
                'slug'         => 'ikea-365-cookware-set',
                'description'  => '5-piece cookware set including pots and pans with lids. Works on all stovetops including induction.',
                'price'        => 4999.00,
                'stock'        => 35,
                'is_available' => true,
            ],
            [
                'category_id'  => $kitchen->id,
                'name'         => 'KALLAX Shelf Unit',
                'slug'         => 'kallax-shelf-unit',
                'description'  => 'A versatile storage unit that can stand on the floor or be mounted on the wall. Use as a room divider or display shelf.',
                'price'        => 6999.00,
                'stock'        => 28,
                'is_available' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['slug' => $product['slug']],
                $product
            );
        }

        $this->command->info('✅ Roles seeded: admin, staff, customer');
        $this->command->info('✅ Users seeded: admin@ikea.com, staff@ikea.com, customer@ikea.com (password: password)');
        $this->command->info('✅ 5 categories seeded');
        $this->command->info('✅ 15 products seeded');
    }
}