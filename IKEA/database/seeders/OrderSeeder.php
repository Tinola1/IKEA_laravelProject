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
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('⚠️  No users found. Run UserSeeder first.');
            return;
        }

        $orders = [

            // ── Order 1: Completed & Paid (COD) ───────────────────────────
            [
                'user_email'     => $users[0]->email,
                'status'         => 'completed',
                'payment_method' => 'cod',
                'payment_status' => 'paid',
                'full_name'      => 'Juan dela Cruz',
                'phone'          => '09171234567',
                'address'        => '123 Rizal Street, Brgy. Poblacion',
                'city'           => 'Makati',
                'province'       => 'Metro Manila',
                'zip_code'       => '1210',
                'notes'          => 'Please call before delivery.',
                'items'          => [
                    ['slug' => 'ektorp-3-seat-sofa',   'quantity' => 1],
                    ['slug' => 'lack-coffee-table',     'quantity' => 1],
                    ['slug' => 'stefan-dining-chair',   'quantity' => 4],
                ],
            ],

            // ── Order 2: Processing & Unpaid (GCash) ──────────────────────
            [
                'user_email'     => $users[1 % $users->count()]->email,
                'status'         => 'processing',
                'payment_method' => 'gcash',
                'payment_status' => 'unpaid',
                'full_name'      => 'Maria Santos',
                'phone'          => '09281112222',
                'address'        => '45 Mabini Ave, Brgy. San Jose',
                'city'           => 'Quezon City',
                'province'       => 'Metro Manila',
                'zip_code'       => '1100',
                'notes'          => null,
                'items'          => [
                    ['slug' => 'malm-bed-frame-queen',    'quantity' => 1],
                    ['slug' => 'hasvag-spring-mattress',  'quantity' => 1],
                    ['slug' => 'markus-office-chair',     'quantity' => 1],
                ],
            ],

            // ── Order 3: Pending & Unpaid (Bank Transfer) ─────────────────
            [
                'user_email'     => $users[2 % $users->count()]->email,
                'status'         => 'pending',
                'payment_method' => 'bank_transfer',
                'payment_status' => 'unpaid',
                'full_name'      => 'Carlo Reyes',
                'phone'          => '09393334444',
                'address'        => '78 Bonifacio Road, Brgy. Wack-Wack',
                'city'           => 'Mandaluyong',
                'province'       => 'Metro Manila',
                'zip_code'       => '1550',
                'notes'          => 'Leave at the gate if no one is home.',
                'items'          => [
                    ['slug' => 'bekant-sit-stand-desk', 'quantity' => 1],
                    ['slug' => 'tobias-chair',          'quantity' => 2],
                ],
            ],

            // ── Order 4: Cancelled & Unpaid (COD) ─────────────────────────
            [
                'user_email'     => $users[0]->email,
                'status'         => 'cancelled',
                'payment_method' => 'cod',
                'payment_status' => 'unpaid',
                'full_name'      => 'Juan dela Cruz',
                'phone'          => '09171234567',
                'address'        => '123 Rizal Street, Brgy. Poblacion',
                'city'           => 'Makati',
                'province'       => 'Metro Manila',
                'zip_code'       => '1210',
                'notes'          => 'Changed my mind — please cancel.',
                'items'          => [
                    ['slug' => 'vimle-corner-sofa', 'quantity' => 1],
                ],
            ],

            // ── Order 5: Completed & Paid (GCash) — kitchen haul ──────────
            [
                'user_email'     => $users[1 % $users->count()]->email,
                'status'         => 'completed',
                'payment_method' => 'gcash',
                'payment_status' => 'paid',
                'full_name'      => 'Maria Santos',
                'phone'          => '09281112222',
                'address'        => '45 Mabini Ave, Brgy. San Jose',
                'city'           => 'Quezon City',
                'province'       => 'Metro Manila',
                'zip_code'       => '1100',
                'notes'          => null,
                'items'          => [
                    ['slug' => 'sektion-base-cabinet',   'quantity' => 2],
                    ['slug' => 'ikea-365-cookware-set',  'quantity' => 1],
                    ['slug' => 'raskog-utility-cart',    'quantity' => 1],
                    ['slug' => 'kallax-shelf-unit',      'quantity' => 1],
                ],
            ],

            // ── Order 6: Processing & Paid (Bank Transfer) — home office ──
            [
                'user_email'     => $users[2 % $users->count()]->email,
                'status'         => 'processing',
                'payment_method' => 'bank_transfer',
                'payment_status' => 'paid',
                'full_name'      => 'Carlo Reyes',
                'phone'          => '09393334444',
                'address'        => '78 Bonifacio Road, Brgy. Wack-Wack',
                'city'           => 'Mandaluyong',
                'province'       => 'Metro Manila',
                'zip_code'       => '1550',
                'notes'          => 'Fragile items — handle with care.',
                'items'          => [
                    ['slug' => 'linnmon-desk',         'quantity' => 1],
                    ['slug' => 'markus-office-chair',  'quantity' => 1],
                    ['slug' => 'trofast-storage-combination', 'quantity' => 1],
                ],
            ],

            // ── Order 7: Pending & Unpaid (COD) — bedroom setup ───────────
            [
                'user_email'     => $users[0]->email,
                'status'         => 'pending',
                'payment_method' => 'cod',
                'payment_status' => 'unpaid',
                'full_name'      => 'Juan dela Cruz',
                'phone'          => '09171234567',
                'address'        => '123 Rizal Street, Brgy. Poblacion',
                'city'           => 'Makati',
                'province'       => 'Metro Manila',
                'zip_code'       => '1210',
                'notes'          => null,
                'items'          => [
                    ['slug' => 'brimnes-bed-frame-storage', 'quantity' => 1],
                    ['slug' => 'morgedal-foam-mattress',    'quantity' => 1],
                    ['slug' => 'hemnes-dressing-table',     'quantity' => 1],
                ],
            ],

            // ── Order 8: Completed & Paid (COD) — dining set ──────────────
            [
                'user_email'     => $users[1 % $users->count()]->email,
                'status'         => 'completed',
                'payment_method' => 'cod',
                'payment_status' => 'paid',
                'full_name'      => 'Maria Santos',
                'phone'          => '09281112222',
                'address'        => '45 Mabini Ave, Brgy. San Jose',
                'city'           => 'Quezon City',
                'province'       => 'Metro Manila',
                'zip_code'       => '1100',
                'notes'          => 'Weekend delivery preferred.',
                'items'          => [
                    ['slug' => 'ekedalen-extendable-table',    'quantity' => 1],
                    ['slug' => 'ekedalen-upholstered-chair',   'quantity' => 4],
                    ['slug' => 'ingolf-bar-stool',             'quantity' => 2],
                ],
            ],

        ];

        foreach ($orders as $orderData) {
            $user = User::where('email', $orderData['user_email'])->firstOrFail();

            // Build order items and compute total
            $lineItems = [];
            $total     = 0;

            foreach ($orderData['items'] as $item) {
                $product     = Product::where('slug', $item['slug'])->firstOrFail();
                $lineTotal   = $product->price * $item['quantity'];
                $total      += $lineTotal;

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

        $this->command->info('✅ 8 orders seeded with order items (totals computed from product prices)');
    }
}