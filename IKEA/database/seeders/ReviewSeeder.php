<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use App\Models\Product;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $user2 = User::where('email', 'user2@mail.com')->firstOrFail();
        $user3 = User::where('email', 'user3@mail.com')->firstOrFail();

        $reviews = [
            // user3 reviews — products they ordered
            [
                'user'    => $user3,
                'slug'    => 'ektorp-3-seat-sofa',
                'rating'  => 5,
                'title'   => 'Absolutely love this sofa!',
                'body'    => 'The EKTORP is everything I hoped for. The cover is easy to remove and wash, and the high back gives great support. Assembly was straightforward. Highly recommend!',
            ],
            [
                'user'    => $user3,
                'slug'    => 'lack-coffee-table',
                'rating'  => 4,
                'title'   => 'Simple and sturdy',
                'body'    => 'Does exactly what you expect from a coffee table. Very light and easy to move around. Only giving 4 stars because the surface scratches a little easily.',
            ],
            [
                'user'    => $user3,
                'slug'    => 'stefan-dining-chair',
                'rating'  => 5,
                'title'   => 'Perfect dining chairs',
                'body'    => 'Bought 4 of these. Solid wood, very sturdy, and they look great around our dining table. Great value for the price.',
            ],
            [
                'user'    => $user3,
                'slug'    => 'sektion-base-cabinet',
                'rating'  => 4,
                'title'   => 'Great kitchen cabinet',
                'body'    => 'Bought two of these for our kitchen renovation. Quality is excellent and the adjustable shelf is very useful. Assembly took a while but instructions were clear.',
            ],
            [
                'user'    => $user3,
                'slug'    => 'ikea-365-cookware-set',
                'rating'  => 5,
                'title'   => 'Best cookware set I have owned',
                'body'    => 'These pots and pans are excellent. Heats evenly, easy to clean, and the lids fit perfectly. Dishwasher safe too which is a bonus.',
            ],
            [
                'user'    => $user3,
                'slug'    => 'ekedalen-extendable-table',
                'rating'  => 5,
                'title'   => 'Stunning dining table',
                'body'    => 'The solid oak finish is beautiful. We use it at the standard size daily and extend it when guests come over. Absolutely worth the price.',
            ],

            // user2 reviews — products they ordered
            [
                'user'    => $user2,
                'slug'    => 'malm-bed-frame-queen',
                'rating'  => 5,
                'title'   => 'Elegant and practical',
                'body'    => 'The MALM bed frame looks stunning in our bedroom. The high headboard is great for reading in bed. Solid construction and storage underneath is a great bonus.',
            ],
            [
                'user'    => $user2,
                'slug'    => 'hasvag-spring-mattress',
                'rating'  => 4,
                'title'   => 'Comfortable medium-firm mattress',
                'body'    => 'Took a few nights to get used to but now I sleep much better. Good support and I wake up without back pain. Solid choice for the price.',
            ],
            [
                'user'    => $user2,
                'slug'    => 'markus-office-chair',
                'rating'  => 5,
                'title'   => 'Best office chair I have ever used',
                'body'    => 'Work from home and sit in this for 8+ hours a day. The lumbar support is excellent and the adjustable height fits my desk perfectly. Zero back pain since switching.',
            ],
            [
                'user'    => $user2,
                'slug'    => 'linnmon-desk',
                'rating'  => 4,
                'title'   => 'Spacious and clean-looking desk',
                'body'    => 'Lots of surface area for my monitors and equipment. Very easy to assemble. Docking a star because it is a bit wobbly without the optional legs — get the ADILS legs with it.',
            ],
            [
                'user'    => $user2,
                'slug'    => 'kallax-shelf-unit',
                'rating'  => 5,
                'title'   => 'Incredibly versatile',
                'body'    => 'Using this as a room divider between living and dining area. Looks great from both sides and the optional inserts make it even more functional. A true IKEA classic.',
            ],
            [
                'user'    => $user2,
                'slug'    => 'ekedalen-upholstered-chair',
                'rating'  => 4,
                'title'   => 'Comfortable dining chairs',
                'body'    => 'Pairs perfectly with the EKEDALEN table. The upholstery is comfortable and cleans up well. Only minor complaint is the fabric shows pet hair easily.',
            ],
        ];

        foreach ($reviews as $data) {
            $product = Product::where('slug', $data['slug'])->first();
            if (!$product) continue;

            Review::firstOrCreate(
                [
                    'user_id'    => $data['user']->id,
                    'product_id' => $product->id,
                ],
                [
                    'rating' => $data['rating'],
                    'title'  => $data['title'],
                    'body'   => $data['body'],
                ]
            );
        }

        $this->command->info('✅ 12 reviews seeded across 12 products');
    }
}