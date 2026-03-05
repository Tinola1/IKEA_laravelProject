<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $sofas   = Category::where('slug', 'sofas-armchairs')->firstOrFail();
        $beds    = Category::where('slug', 'beds-mattresses')->firstOrFail();
        $tables  = Category::where('slug', 'tables-desks')->firstOrFail();
        $chairs  = Category::where('slug', 'chairs')->firstOrFail();
        $kitchen = Category::where('slug', 'kitchen-dining')->firstOrFail();

        $products = [

            // ── Sofas & Armchairs ──────────────────────────
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
                'description'  => 'Layer-glued bent birch frame with a comfortable seat cushion. The resilient frame gives a light bouncing comfort when you sit down.',
                'price'        => 8499.00,
                'stock'        => 20,
                'is_available' => true,
            ],
            [
                'category_id'  => $sofas->id,
                'name'         => 'KIVIK 2-Seat Sofa',
                'slug'         => 'kivik-2-seat-sofa',
                'description'  => 'A generous seating series with a soft, deep seat and comfortable back cushions. Perfect for relaxing and lounging after a long day.',
                'price'        => 18999.00,
                'stock'        => 10,
                'is_available' => true,
            ],
            [
                'category_id'  => $sofas->id,
                'name'         => 'VIMLE Corner Sofa',
                'slug'         => 'vimle-corner-sofa',
                'description'  => 'A large corner sofa with deep seats and a high back for excellent support. Modular design lets you configure it however you like.',
                'price'        => 39999.00,
                'stock'        => 6,
                'is_available' => true,
            ],
            [
                'category_id'  => $sofas->id,
                'name'         => 'STOCKHOLM 2017 Sofa',
                'slug'         => 'stockholm-2017-sofa',
                'description'  => 'A sofa with a unique herringbone weave pattern on the seat cushion. Handcrafted and made to last for years.',
                'price'        => 54999.00,
                'stock'        => 4,
                'is_available' => true,
            ],

            // ── Beds & Mattresses ──────────────────────────
            [
                'category_id'  => $beds->id,
                'name'         => 'MALM Bed Frame Queen',
                'slug'         => 'malm-bed-frame-queen',
                'description'  => 'Clean-lined bed frame in white with a high headboard. Space under the bed for extra storage. Fits standard 160×200 cm mattresses.',
                'price'        => 19999.00,
                'stock'        => 12,
                'is_available' => true,
            ],
            [
                'category_id'  => $beds->id,
                'name'         => 'HASVÅG Spring Mattress',
                'slug'         => 'hasvag-spring-mattress',
                'description'  => 'Medium firm spring mattress with comfort layers of foam and fiber that adapt to your body weight and shape for a good night\'s sleep.',
                'price'        => 14999.00,
                'stock'        => 18,
                'is_available' => true,
            ],
            [
                'category_id'  => $beds->id,
                'name'         => 'HEMNES Daybed',
                'slug'         => 'hemnes-daybed',
                'description'  => 'A daybed with a traditional look that can be used as a sofa or a bed. Pull out the extra bed when you need more sleeping space for guests.',
                'price'        => 22499.00,
                'stock'        => 8,
                'is_available' => true,
            ],
            [
                'category_id'  => $beds->id,
                'name'         => 'BRIMNES Bed Frame with Storage',
                'slug'         => 'brimnes-bed-frame-storage',
                'description'  => 'Bed frame with two large drawers for storage. Headboard with shelf lets you keep books and other small items within reach.',
                'price'        => 17499.00,
                'stock'        => 9,
                'is_available' => true,
            ],
            [
                'category_id'  => $beds->id,
                'name'         => 'MORGEDAL Foam Mattress',
                'slug'         => 'morgedal-foam-mattress',
                'description'  => 'A firm foam mattress that provides good pressure point relief. Suitable for those who prefer a firmer sleeping surface.',
                'price'        => 9999.00,
                'stock'        => 22,
                'is_available' => true,
            ],

            // ── Tables & Desks ─────────────────────────────
            [
                'category_id'  => $tables->id,
                'name'         => 'LACK Coffee Table',
                'slug'         => 'lack-coffee-table',
                'description'  => 'Simple and clean-lined coffee table that fits in small spaces. Hollow construction keeps it light and easy to move around.',
                'price'        => 2999.00,
                'stock'        => 30,
                'is_available' => true,
            ],
            [
                'category_id'  => $tables->id,
                'name'         => 'LINNMON Desk',
                'slug'         => 'linnmon-desk',
                'description'  => 'A spacious work surface for your home office. Durable, stain-resistant tabletop that is easy to wipe clean.',
                'price'        => 5499.00,
                'stock'        => 25,
                'is_available' => true,
            ],
            [
                'category_id'  => $tables->id,
                'name'         => 'EKEDALEN Extendable Table',
                'slug'         => 'ekedalen-extendable-table',
                'description'  => 'Extendable dining table made from solid oak with a natural finish. Seats 4 normally, extends to seat 6 for gatherings.',
                'price'        => 29999.00,
                'stock'        => 7,
                'is_available' => true,
            ],
            [
                'category_id'  => $tables->id,
                'name'         => 'BEKANT Sit/Stand Desk',
                'slug'         => 'bekant-sit-stand-desk',
                'description'  => 'Electric height-adjustable desk that lets you alternate between sitting and standing with the touch of a button. Better for your back and posture.',
                'price'        => 34999.00,
                'stock'        => 5,
                'is_available' => true,
            ],
            [
                'category_id'  => $tables->id,
                'name'         => 'HEMNES Dressing Table',
                'slug'         => 'hemnes-dressing-table',
                'description'  => 'Dressing table with three drawers for storing cosmetics and accessories. Solid pine gives it a natural, traditional look.',
                'price'        => 12999.00,
                'stock'        => 11,
                'is_available' => true,
            ],

            // ── Chairs ─────────────────────────────────────
            [
                'category_id'  => $chairs->id,
                'name'         => 'STEFAN Dining Chair',
                'slug'         => 'stefan-dining-chair',
                'description'  => 'A sturdy chair made of solid wood with a comfortable curved back. Suitable for dining rooms and works well as a home office chair too.',
                'price'        => 2499.00,
                'stock'        => 40,
                'is_available' => true,
            ],
            [
                'category_id'  => $chairs->id,
                'name'         => 'MARKUS Office Chair',
                'slug'         => 'markus-office-chair',
                'description'  => 'Ergonomic office chair with built-in lumbar support and adjustable seat height. Designed for long working hours and maximum comfort.',
                'price'        => 12999.00,
                'stock'        => 15,
                'is_available' => true,
            ],
            [
                'category_id'  => $chairs->id,
                'name'         => 'INGOLF Bar Stool',
                'slug'         => 'ingolf-bar-stool',
                'description'  => 'Classic bar stool made from solid pine. Open back gives a light and airy feel. Works well at kitchen islands or high tables.',
                'price'        => 3299.00,
                'stock'        => 22,
                'is_available' => true,
            ],
            [
                'category_id'  => $chairs->id,
                'name'         => 'TOBIAS Chair',
                'slug'         => 'tobias-chair',
                'description'  => 'A sleek, transparent chair made of polycarbonate. Sturdy and stackable — easy to store when not in use. Looks great in any space.',
                'price'        => 4999.00,
                'stock'        => 28,
                'is_available' => true,
            ],
            [
                'category_id'  => $chairs->id,
                'name'         => 'EKEDALEN Upholstered Chair',
                'slug'         => 'ekedalen-upholstered-chair',
                'description'  => 'Upholstered dining chair with foam padding for extra comfort. Pairs perfectly with the EKEDALEN extendable dining table.',
                'price'        => 5999.00,
                'stock'        => 16,
                'is_available' => true,
            ],

            // ── Kitchen & Dining ───────────────────────────
            [
                'category_id'  => $kitchen->id,
                'name'         => 'SEKTION Base Cabinet',
                'slug'         => 'sektion-base-cabinet',
                'description'  => 'Durable base cabinet with one adjustable shelf and one door. Customize with a range of fronts, handles, and interior fittings.',
                'price'        => 8999.00,
                'stock'        => 20,
                'is_available' => true,
            ],
            [
                'category_id'  => $kitchen->id,
                'name'         => 'IKEA 365+ Cookware Set',
                'slug'         => 'ikea-365-cookware-set',
                'description'  => '5-piece cookware set including pots and pans with lids. Compatible with all stovetops including induction. Dishwasher safe.',
                'price'        => 4999.00,
                'stock'        => 35,
                'is_available' => true,
            ],
            [
                'category_id'  => $kitchen->id,
                'name'         => 'KALLAX Shelf Unit',
                'slug'         => 'kallax-shelf-unit',
                'description'  => 'A versatile shelf unit that can stand on the floor or be wall-mounted. Use as a room divider, bookcase, or display unit with optional inserts.',
                'price'        => 6999.00,
                'stock'        => 28,
                'is_available' => true,
            ],
            [
                'category_id'  => $kitchen->id,
                'name'         => 'RÅSKOG Utility Cart',
                'slug'         => 'raskog-utility-cart',
                'description'  => 'Sturdy rolling cart with three shelves. Great for storing kitchen supplies, craft materials, or bathroom accessories. Easy to move wherever you need it.',
                'price'        => 3499.00,
                'stock'        => 32,
                'is_available' => true,
            ],
            [
                'category_id'  => $kitchen->id,
                'name'         => 'TROFAST Storage Combination',
                'slug'         => 'trofast-storage-combination',
                'description'  => 'A smart storage system with colourful bins for organising toys, craft supplies, or anything else that tends to pile up.',
                'price'        => 5499.00,
                'stock'        => 14,
                'is_available' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['slug' => $product['slug']],
                $product
            );
        }

        $this->command->info('✅ 25 products seeded across 5 categories');
    }
}
