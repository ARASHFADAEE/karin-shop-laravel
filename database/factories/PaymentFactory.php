<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
        return [
            'order_id' => Order::factory(),
            'transaction_id' => 'TXN-' . strtoupper(Str::random(10)),
            'amount' => $this->faker->numberBetween(50000, 1000000),
            'payment_method' => $this->faker->randomElement(['credit_card', 'bank_transfer', 'cash_on_delivery', 'wallet']),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed', 'refunded']),
            'gateway' => $this->faker->randomElement(['zarinpal', 'mellat', 'parsian', 'saderat']),
            'gateway_transaction_id' => $this->faker->optional()->numerify('##########'),
            'paid_at' => $this->faker->optional()->dateTimeBetween('-6 months', 'now'),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the payment is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'paid_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ]);
    }

    /**
     * Indicate that the payment is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'paid_at' => null,
        ]);
    }

    /**
     * Indicate that the payment failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'paid_at' => null,
        ]);
    }

    /**
     * Indicate that the payment is refunded.
     */
    public function refunded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'refunded',
            'paid_at' => $this->faker->dateTimeBetween('-6 months', '-1 month'),
        ]);
    }
}