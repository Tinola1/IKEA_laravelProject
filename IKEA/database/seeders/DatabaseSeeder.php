<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── CATEGORIES ─────────────────────────────────────
        $sofas   = Category::firstOrCreate(['slug' => 'sofas-armchairs'],    ['name' => 'Sofas & Armchairs',  'description' => 'Comfortable sofas and armchairs for your living room.']);
        $beds    = Category::firstOrCreate(['slug' => 'beds-mattresses'],    ['name' => 'Beds & Mattresses',  'description' => 'Bed frames, mattresses, and bedroom storage.']);
        $tables  = Category::firstOrCreate(['slug' => 'tables-desks'],       ['name' => 'Tables & Desks',     'description' => 'Dining tables, coffee tables, and work desks.']);
        $chairs  = Category::firstOrCreate(['slug' => 'chairs'],             ['name' => 'Chairs',             'description' => 'Dining chairs, office chairs, and accent chairs.']);
        $kitchen = Category::firstOrCreate(['slug' => 'kitchen-dining'],     ['name' => 'Kitchen & Dining',   'description' => 'Kitchen cabinets, dining sets, and accessories.']);

        // ── PRODUCTS ───────────────────────────────────────
        $products = [
            // Sofas & Armchairs
            ['category_id' => $sofas->id,   'name' => 'EKTORP 3-Seat Sofa',        'slug' => 'ektorp-3-seat-sofa',        'description' => 'Classic 3-seat sofa with removable washable cover.',          'price' => 24999, 'stock' => 15, 'is_available' => true],
            ['category_id' => $sofas->id,   'name' => 'POÄNG Armchair',            'slug' => 'poang-armchair',            'description' => 'Layer-glued bent birch frame with comfortable seat cushion.',  'price' => 8499,  'stock' => 20, 'is_available' => true],
            ['category_id' => $sofas->id,   'name' => 'KIVIK 2-Seat Sofa',         'slug' => 'kivik-2-seat-sofa',         'description' => 'Generous seating with soft deep seat and back cushions.',       'price' => 18999, 'stock' => 10, 'is_available' => true],

            // Beds & Mattresses
            ['category_id' => $beds->id,    'name' => 'MALM Bed Frame Queen',      'slug' => 'malm-bed-frame-queen',      'description' => 'Clean-lined bed frame with high headboard in white.',          'price' => 19999, 'stock' => 12, 'is_available' => true],
            ['category_id' => $beds->id,    'name' => 'HASVÅG Spring Mattress',    'slug' => 'hasvag-spring-mattress',    'description' => 'Medium firm spring mattress that adapts to your body.',         'price' => 14999, 'stock' => 18, 'is_available' => true],
            ['category_id' => $beds->id,    'name' => 'HEMNES Daybed',             'slug' => 'hemnes-daybed',             'description' => 'Can be used as sofa or bed with pull-out extra bed.',           'price' => 22499, 'stock' => 8,  'is_available' => true],

            // Tables & Desks
            ['category_id' => $tables->id,  'name' => 'LACK Coffee Table',         'slug' => 'lack-coffee-table',         'description' => 'Simple clean-lined coffee table, light and easy to move.',     'price' => 2999,  'stock' => 30, 'is_available' => true],
            ['category_id' => $tables->id,  'name' => 'LINNMON Desk',              'slug' => 'linnmon-desk',              'description' => 'Spacious work surface, stain-resistant and easy to clean.',     'price' => 5499,  'stock' => 25, 'is_available' => true],
            ['category_id' => $tables->id,  'name' => 'EKEDALEN Extendable Table', 'slug' => 'ekedalen-extendable-table', 'description' => 'Extendable dining table in solid oak, seats 4–6 people.',      'price' => 29999, 'stock' => 7,  'is_available' => true],

            // Chairs
            ['category_id' => $chairs->id,  'name' => 'STEFAN Dining Chair',       'slug' => 'stefan-dining-chair',       'description' => 'Sturdy solid wood chair with comfortable curved back.',         'price' => 2499,  'stock' => 40, 'is_available' => true],
            ['category_id' => $chairs->id,  'name' => 'MARKUS Office Chair',       'slug' => 'markus-office-chair',       'description' => 'Ergonomic office chair with lumbar support.',                   'price' => 12999, 'stock' => 15, 'is_available' => true],
            ['category_id' => $chairs->id,  'name' => 'INGOLF Bar Stool',          'slug' => 'ingolf-bar-stool',          'description' => 'Classic bar stool made of solid pine with open back.',          'price' => 3299,  'stock' => 22, 'is_available' => true],

            // Kitchen & Dining
            ['category_id' => $kitchen->id, 'name' => 'SEKTION Base Cabinet',      'slug' => 'sektion-base-cabinet',      'description' => 'Durable base cabinet with one shelf and one door.',            'price' => 8999,  'stock' => 20, 'is_available' => true],
            ['category_id' => $kitchen->id, 'name' => 'IKEA 365+ Cookware Set',    'slug' => 'ikea-365-cookware-set',     'description' => '5-piece cookware set, works on all stovetops.',                'price' => 4999,  'stock' => 35, 'is_available' => true],
            ['category_id' => $kitchen->id, 'name' => 'KALLAX Shelf Unit',         'slug' => 'kallax-shelf-unit',         'description' => 'Versatile storage unit, can stand or be wall mounted.',         'price' => 6999,  'stock' => 28, 'is_available' => true],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(['slug' => $product['slug']], $product);
        }

        $this->command->info('✅ 5 categories seeded');
        $this->command->info('✅ 15 products seeded');
    }
}