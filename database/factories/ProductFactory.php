<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);

        return [
            'created_by' => User::inRandomOrder()->first()->id,
            'name' => ucfirst($name),
            'slug' => Str::slug($name) . '-' . uniqid(),
            'status' => 1,
            'description' => $this->faker->paragraph(4),
            'detail' => [
                'material' => $this->faker->randomElement(['Cotton', 'Jean', 'Polyester']),
                'fit' => $this->faker->randomElement(['Slim', 'Regular', 'Oversize']),
            ],
        ];
    }
}
