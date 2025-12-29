<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'is_default' => false,
            'province'   => $this->faker->state(),
            'district'   => $this->faker->city(),
            'ward'       => $this->faker->optional()->streetName(),
            'detail'     => $this->faker->streetAddress(),
        ];
    }
}
