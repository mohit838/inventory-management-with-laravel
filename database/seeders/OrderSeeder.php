<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        $users = User::all();

        if ($products->isEmpty()) {
            $this->call(ProductSeeder::class);
            $products = Product::all();
        }
        
        if ($users->isEmpty()) {
            User::factory(10)->create();
            $users = User::all();
        }

        Order::factory(20)->make()->each(function ($order) use ($users, $products) {
            $order->user_id = $users->random()->id;
            $order->save();

            // Create 1-5 items for each order
            $orderItems = OrderItem::factory(rand(1, 5))->make([
                'order_id' => $order->id,
            ]);

            $totalAmount = 0;

            foreach ($orderItems as $item) {
                $product = $products->random();
                $item->product_id = $product->id;
                $item->unit_price = $product->price;
                $item->total_price = $item->quantity * $item->unit_price;
                $item->save();

                $totalAmount += $item->total_price;
            }

            // Update order total amount
            $order->total_amount = $totalAmount;
            $order->save();
        });
    }
}
