<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'created_by' => User::query()->inRandomOrder()->value('id') ?? User::factory(),
            'url' => fakeImage('images', 'product', 640, 480),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
