<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $orders = [

            // ── Order 1: Completed & Paid — user3 (reviews ektorp, lack, stefan) ──
            [
                'user_email'     => 'user3@mail.com',
                'status'         => 'completed',
                'payment_method' => 'cod',
                'payment_status' => 'paid',
                'full_name'      => 'Reviews Demo User',
                'phone'          => '09393333333',
                'address'        => '3 Taft Avenue, Brgy. Malate',
                'city'           => 'Manila',
                'province'       => 'Metro Manila',
                'zip_code'       => '1004',
                'notes'          => 'Please call before delivery.',
                'items'          => [
                    ['slug' => 'ektorp-3-seat-sofa', 'quantity' => 1],
                    ['slug' => 'lack-coffee-table',  'quantity' => 1],
                    ['slug' => 'stefan-dining-chair','quantity' => 4],
                ],
            ],

            // ── Order 2: Completed & Paid — user2 (reviews malm, hasvag, markus) ──
            [
                'user_email'     => 'user2@mail.com',
                'status'         => 'completed',
                'payment_method' => 'gcash',
                'payment_status' => 'paid',
                'full_name'      => 'Orders Demo User',
                'phone'          => '09282222222',
                'address'        => '2 EDSA, Brgy. Wack-Wack',
                'city'           => 'Mandaluyong',
                'province'       => 'Metro Manila',
                'zip_code'       => '1550',
                'notes'          => null,
                'items'          => [
                    ['slug' => 'malm-bed-frame-queen',   'quantity' => 1],
                    ['slug' => 'hasvag-spring-mattress', 'quantity' => 1],
                    ['slug' => 'markus-office-chair',    'quantity' => 1],
                ],
            ],

            // ── Order 3: Pending & Unpaid — user2 ─────────────────────────────────
            [
                'user_email'     => 'user2@mail.com',
                'status'         => 'pending',
                'payment_method' => 'bank_transfer',
                'payment_status' => 'unpaid',
                'full_name'      => 'Orders Demo User',
                'phone'          => '09282222222',
                'address'        => '2 EDSA, Brgy. Wack-Wack',
                'city'           => 'Mandaluyong',
                'province'       => 'Metro Manila',
                'zip_code'       => '1550',
                'notes'          => 'Leave at the gate if no one is home.',
                'items'          => [
                    ['slug' => 'bekant-sit-stand-desk', 'quantity' => 1],
                    ['slug' => 'tobias-chair',          'quantity' => 2],
                ],
            ],

            // ── Order 4: Cancelled — user3 ─────────────────────────────────────────
            [
                'user_email'     => 'user3@mail.com',
                'status'         => 'cancelled',
                'payment_method' => 'cod',
                'payment_status' => 'unpaid',
                'full_name'      => 'Reviews Demo User',
                'phone'          => '09393333333',
                'address'        => '3 Taft Avenue, Brgy. Malate',
                'city'           => 'Manila',
                'province'       => 'Metro Manila',
                'zip_code'       => '1004',
                'notes'          => 'Changed my mind — please cancel.',
                'items'          => [
                    ['slug' => 'vimle-corner-sofa', 'quantity' => 1],
                ],
            ],

            // ── Order 5: Completed & Paid — user3 (reviews sektion, ikea-365, kallax) ──
            [
                'user_email'     => 'user3@mail.com',
                'status'         => 'completed',
                'payment_method' => 'gcash',
                'payment_status' => 'paid',
                'full_name'      => 'Reviews Demo User',
                'phone'          => '09393333333',
                'address'        => '3 Taft Avenue, Brgy. Malate',
                'city'           => 'Manila',
                'province'       => 'Metro Manila',
                'zip_code'       => '1004',
                'notes'          => null,
                'items'          => [
                    ['slug' => 'sektion-base-cabinet',  'quantity' => 2],
                    ['slug' => 'ikea-365-cookware-set', 'quantity' => 1],
                    ['slug' => 'raskog-utility-cart',   'quantity' => 1],
                    ['slug' => 'kallax-shelf-unit',     'quantity' => 1],
                ],
            ],

            // ── Order 6: Completed & Paid — user2 (reviews linnmon, kallax, ekedalen-chair) ──
            [
                'user_email'     => 'user2@mail.com',
                'status'         => 'completed',
                'payment_method' => 'bank_transfer',
                'payment_status' => 'paid',
                'full_name'      => 'Orders Demo User',
                'phone'          => '09282222222',
                'address'        => '2 EDSA, Brgy. Wack-Wack',
                'city'           => 'Mandaluyong',
                'province'       => 'Metro Manila',
                'zip_code'       => '1550',
                'notes'          => 'Fragile items — handle with care.',
                'items'          => [
                    ['slug' => 'linnmon-desk',               'quantity' => 1],
                    ['slug' => 'markus-office-chair',        'quantity' => 1],
                    ['slug' => 'trofast-storage-combination','quantity' => 1],
                ],
            ],

            // ── Order 7: Processing — user2 ────────────────────────────────────────
            [
                'user_email'     => 'user2@mail.com',
                'status'         => 'processing',
                'payment_method' => 'cod',
                'payment_status' => 'unpaid',
                'full_name'      => 'Orders Demo User',
                'phone'          => '09282222222',
                'address'        => '2 EDSA, Brgy. Wack-Wack',
                'city'           => 'Mandaluyong',
                'province'       => 'Metro Manila',
                'zip_code'       => '1550',
                'notes'          => null,
                'items'          => [
                    ['slug' => 'brimnes-bed-frame-storage', 'quantity' => 1],
                    ['slug' => 'morgedal-foam-mattress',    'quantity' => 1],
                    ['slug' => 'hemnes-dressing-table',     'quantity' => 1],
                ],
            ],

            // ── Order 8: Completed & Paid — user3 (reviews ekedalen-table, ekedalen-chair) ──
            [
                'user_email'     => 'user3@mail.com',
                'status'         => 'completed',
                'payment_method' => 'cod',
                'payment_status' => 'paid',
                'full_name'      => 'Reviews Demo User',
                'phone'          => '09393333333',
                'address'        => '3 Taft Avenue, Brgy. Malate',
                'city'           => 'Manila',
                'province'       => 'Metro Manila',
                'zip_code'       => '1004',
                'notes'          => 'Weekend delivery preferred.',
                'items'          => [
                    ['slug' => 'ekedalen-extendable-table',  'quantity' => 1],
                    ['slug' => 'ekedalen-upholstered-chair', 'quantity' => 4],
                    ['slug' => 'ingolf-bar-stool',           'quantity' => 2],
                ],
            ],

            // ── Order 9: Completed & Paid — user2 (reviews ekedalen-chair, kallax) ──
            [
                'user_email'     => 'user2@mail.com',
                'status'         => 'completed',
                'payment_method' => 'gcash',
                'payment_status' => 'paid',
                'full_name'      => 'Orders Demo User',
                'phone'          => '09282222222',
                'address'        => '2 EDSA, Brgy. Wack-Wack',
                'city'           => 'Mandaluyong',
                'province'       => 'Metro Manila',
                'zip_code'       => '1550',
                'notes'          => null,
                'items'          => [
                    ['slug' => 'ekedalen-upholstered-chair', 'quantity' => 2],
                    ['slug' => 'kallax-shelf-unit',          'quantity' => 1],
                ],
            ],

        ];

        foreach ($orders as $orderData) {
            $user = User::where('email', $orderData['user_email'])->firstOrFail();

            $lineItems = [];
            $total     = 0;

            foreach ($orderData['items'] as $item) {
                $product   = Product::where('slug', $item['slug'])->firstOrFail();
                $total    += $product->price * $item['quantity'];

                $lineItems[] = [
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'price'      => $product->price,
                ];
            }

            $order = Order::create([
                'user_id'        => $user->id,
                'status'         => $orderData['status'],
                'payment_method' => $orderData['payment_method'],
                'payment_status' => $orderData['payment_status'],
                'total'          => $total,
                'full_name'      => $orderData['full_name'],
                'phone'          => $orderData['phone'],
                'address'        => $orderData['address'],
                'city'           => $orderData['city'],
                'province'       => $orderData['province'],
                'zip_code'       => $orderData['zip_code'],
                'notes'          => $orderData['notes'],
            ]);

            foreach ($lineItems as $lineItem) {
                OrderItem::create(array_merge($lineItem, ['order_id' => $order->id]));
            }
        }

        $this->command->info('✅ 9 orders seeded across user2 and user3');
    }
}