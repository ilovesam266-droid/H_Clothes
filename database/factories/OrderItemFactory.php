<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $variant = \App\Models\Variant::inRandomOrder()->first();

        return [
            'order_id' => \App\Models\Order::inRandomOrder()->first()->id,
            'variant_id' => $variant->id,
            'product_name' => $variant->product->name,
            'variant_name' => "{$variant->size} - {$variant->color}",
            'unit_price' => $variant->price,
            'quantity' => fake()->numberBetween(1, 3),
        ];
    }
}
