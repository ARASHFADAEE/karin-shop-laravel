<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
        return [
            'user_id' => User::factory(),
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'status' => $this->faker->randomElement(['pending', 'processing', 'shipped', 'delivered', 'cancelled']),
            'total_amount' => $this->faker->numberBetween(50000, 1000000),
            'discount_amount' => 0,
            'shipping_amount' => $this->faker->numberBetween(10000, 50000),
            'tax_amount' => 0,
            'payment_status' => $this->faker->randomElement(['pending', 'paid', 'failed', 'refunded']),
            'payment_method' => $this->faker->randomElement(['credit_card', 'bank_transfer', 'cash_on_delivery']),
            'shipping_address' => $this->faker->address,
            'billing_address' => $this->faker->address,
            'notes' => $this->faker->optional()->sentence(),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the order is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);
    }

    /**
     * Indicate that the order is processing.
     */
    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'processing',
            'payment_status' => 'paid',
        ]);
    }

    /**
     * Indicate that the order is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'delivered',
            'payment_status' => 'paid',
        ]);
    }

    /**
     * Indicate that the order is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'payment_status' => 'refunded',
        ]);
    }

    /**
     * Indicate that the order has a discount.
     */
    public function withDiscount(): static
    {
        return $this->state(function (array $attributes) {
            $totalAmount = $attributes['total_amount'] ?? $this->faker->numberBetween(100000, 500000);
            $discountAmount = $this->faker->numberBetween(10000, $totalAmount * 0.3);
            
            return [
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount - $discountAmount,
            ];
        });
    }

    /**
     * Indicate that the order is paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'paid',
        ]);
    }

    /**
     * Indicate that the order payment failed.
     */
    public function paymentFailed(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'failed',
            'status' => 'cancelled',
        ]);
    }
}