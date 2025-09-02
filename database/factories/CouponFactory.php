<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(Str::random(10)),
            'description' => $this->faker->sentence(),
            'type' => $this->faker->randomElement(['percentage', 'fixed']),
            'value' => $this->faker->numberBetween(5, 50),
            'minimum_amount' => $this->faker->numberBetween(50000, 200000),
            'usage_limit' => $this->faker->numberBetween(10, 100),
            'usage_count' => 0,
            'expires_at' => Carbon::now()->addDays($this->faker->numberBetween(7, 90)),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the coupon is a percentage type.
     */
    public function percentage(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'percentage',
            'value' => $this->faker->numberBetween(5, 50), // 5% to 50%
        ]);
    }

    /**
     * Indicate that the coupon is a fixed amount type.
     */
    public function fixed(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'fixed',
            'value' => $this->faker->numberBetween(10000, 100000), // 10,000 to 100,000 Toman
        ]);
    }

    /**
     * Indicate that the coupon is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => Carbon::now()->subDays($this->faker->numberBetween(1, 30)),
        ]);
    }

    /**
     * Indicate that the coupon is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the coupon has reached its usage limit.
     */
    public function limitReached(): static
    {
        return $this->state(function (array $attributes) {
            $limit = $this->faker->numberBetween(10, 50);
            return [
                'usage_limit' => $limit,
                'usage_count' => $limit,
            ];
        });
    }

    /**
     * Indicate that the coupon has high minimum amount.
     */
    public function highMinimum(): static
    {
        return $this->state(fn (array $attributes) => [
            'minimum_amount' => $this->faker->numberBetween(500000, 1000000),
        ]);
    }
}