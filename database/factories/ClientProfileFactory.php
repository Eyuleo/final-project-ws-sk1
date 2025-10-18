<?php

namespace Database\Factories;

use App\Models\ClientProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClientProfile>
 */
class ClientProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ClientProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => 'client']),
            'organization' => fake()->optional()->company(),
            'bio' => fake()->optional()->paragraph(2),
            'profile_picture' => null,
            'total_orders' => 0,
            'average_rating' => 0,
            'total_reviews' => 0,
            'stripe_customer_id' => null,
        ];
    }

    /**
     * Indicate that the client has placed orders.
     */
    public function withOrders(int $count = 3): static
    {
        return $this->state(fn (array $attributes) => [
            'total_orders' => $count,
        ]);
    }
}
