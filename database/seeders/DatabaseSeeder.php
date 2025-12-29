<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Models\Variant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();
        Category::factory(5)->create();

        Product::factory(20)->create();
        Variant::factory(80)->create();

        Cart::factory(10)->create();
        CartItem::factory(30)->create();

        Order::factory(15)->create();

        // Order items + total
        Order::all()->each(function ($order) {
            $items = OrderItem::factory(rand(1, 4))->create([
                'order_id' => $order->id,
            ]);

            $order->update([
                'total_amount' => $items->sum(
                    fn($i) => $i->unit_price * $i->quantity
                ),
            ]);
        });

        // Payment chỉ cho order != cancelled
        Order::whereNot('status', 3)->each(function ($order) {
            Payment::factory()->create([
                'order_id' => $order->id,
                'user_id' => $order->created_by,
            ]);
        });

        Order::whereIn('status', [1, 2]) // confirmed, delivered
            ->with(['items.variant.product'])
            ->get()
            ->each(function ($order) {
                foreach ($order->items as $item) {
                    $productId = $item->variant->product_id;

                    // Check user đã review sản phẩm này chưa
                    $exists = Review::where('user_id', $order->created_by)
                        ->where('product_id', $productId)
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    Review::create([
                        'user_id' => $order->created_by,
                        'product_id' => $productId,
                        'rating' => rand(3, 5),
                        'body' => fake()->optional()->sentence(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });
    }
}
