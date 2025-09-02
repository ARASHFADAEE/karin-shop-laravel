<?php

namespace Database\Factories;

use App\Models\Product;
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
        $name = $this->faker->words(3, true);
        
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->numberBetween(10000, 1000000),
            'original_price' => null,
            'discount_percentage' => null,
            'discount_amount' => null,
            'has_discount' => false,
            'discount_starts_at' => null,
            'discount_ends_at' => null,
            'stock' => $this->faker->numberBetween(0, 100),
            'sku' => 'PRD-' . strtoupper(Str::random(8)),
            'status' => $this->faker->randomElement(['active', 'draft', 'out_of_stock']),
            'meta_title' => $name,
            'meta_description' => $this->faker->sentence(),
            'meta_keywords' => implode(', ', $this->faker->words(5)),
            'og_title' => $name,
            'og_description' => $this->faker->sentence(),
            'og_image' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the product is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the product is draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    /**
     * Indicate that the product is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'out_of_stock',
            'stock' => 0,
        ]);
    }

    /**
     * Indicate that the product has a discount.
     */
    public function withDiscount(): static
    {
        return $this->state(function (array $attributes) {
            $originalPrice = $this->faker->numberBetween(100000, 1000000);
            $discountPercentage = $this->faker->numberBetween(10, 50);
            $discountAmount = $originalPrice * ($discountPercentage / 100);
            $finalPrice = $originalPrice - $discountAmount;
            
            return [
                'original_price' => $originalPrice,
                'price' => $finalPrice,
                'discount_percentage' => $discountPercentage,
                'discount_amount' => $discountAmount,
                'has_discount' => true,
                'discount_starts_at' => now(),
                'discount_ends_at' => now()->addDays(30),
            ];
        });
    }

    /**
     * Indicate that the product has high stock.
     */
    public function highStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => $this->faker->numberBetween(100, 500),
        ]);
    }

    /**
     * Indicate that the product has low stock.
     */
    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => $this->faker->numberBetween(1, 5),
        ]);
    }
}