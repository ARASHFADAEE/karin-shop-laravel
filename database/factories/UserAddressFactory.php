<?php

namespace Database\Factories;

use App\Models\UserAddress;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserAddress>
 */
class UserAddressFactory extends Factory
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
            'title' => $this->faker->randomElement(['خانه', 'محل کار', 'آدرس دوم', 'آدرس والدین']),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'postal_code' => $this->faker->postcode(),
            'is_default' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that this is the default address.
     */
    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }

    /**
     * Indicate that this is a home address.
     */
    public function home(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => 'خانه',
        ]);
    }

    /**
     * Indicate that this is a work address.
     */
    public function work(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => 'محل کار',
        ]);
    }
}