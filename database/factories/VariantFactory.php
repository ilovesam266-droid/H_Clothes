<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Variant>
 */
class VariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sizes  = ['S', 'M', 'L', 'XL'];
        $colors = ['Black', 'White', 'Red', 'Blue', 'Green'];

        $size  = $this->faker->randomElement($sizes);
        $color = $this->faker->randomElement($colors);

        return [
            'product_id' => Product::inRandomOrder()->first()->id,

            'size'  => $size,
            'color' => $color,

            'sku' => strtoupper(
                $size . '-' . substr($color, 0, 2) . '-' . $this->faker->unique()->numberBetween(1000, 9999)
            ),

            'price' => $this->faker->numberBetween(150_000, 1_200_000),
            'stock' => $this->faker->numberBetween(0, 100),
            'sold'  => 0,
        ];
    }
}
