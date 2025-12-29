<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement([0,1,2,3,4]);

        $data = [
            'created_by' => User::inRandomOrder()->first()->id,
            'status' => $status,
            'total_amount' => 0,
            'admin_note' => null,
            'customer_note' => null,
            'cancel_reason' => null,
            'failed_reason' => null,
            'cancelled_at' => null,
            'failed_at' => null,
            'confirmed_at' => null,
            'delivered_at' => null,
        ];

        switch ($status) {
            case 1: // confirmed
                $data['confirmed_at'] = fake()->dateTimeBetween('-5 days', '-2 days');
                break;

            case 2: // delivered
                $data['confirmed_at'] = fake()->dateTimeBetween('-7 days', '-5 days');
                $data['delivered_at'] = fake()->dateTimeBetween('-2 days', 'now');
                break;

            case 3: // cancelled
                $data['cancel_reason'] = fake()->randomElement([
                    'Customer requested cancellation',
                    'Out of stock',
                    'Unable to contact customer',
                ]);
                $data['cancelled_at'] = fake()->dateTimeBetween('-3 days', 'now');
                break;

            case 4: // failed
                $data['failed_reason'] = fake()->randomElement([
                    'Payment failed',
                    'Fraud detected',
                    'Gateway timeout',
                ]);
                $data['failed_at'] = fake()->dateTimeBetween('-3 days', 'now');
                break;
        }

        return $data;
    }
}
