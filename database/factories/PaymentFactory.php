<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $order = Order::inRandomOrder()->first();

        return [
            'order_id' => $order->id,
            'user_id' => $order->created_by,
            'payment_method' => fake()->randomElement([0, 1]),
            'payment_transaction' => fake()->uuid(),
            'total_amount' => $order->total_amount,
            'status' => match ($order->status) {
                4 => 2, // failed
                1, 2 => 1, // success
                default => 0, // pending
            },
            'meta_data' => [
                'gateway' => 'stripe',
                'order_status' => $order->status,
            ],
        ];
    }
}
