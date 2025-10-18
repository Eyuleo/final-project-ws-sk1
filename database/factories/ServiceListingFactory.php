<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\ServiceListing;
use App\Models\StudentProfile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceListing>
 */
class ServiceListingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ServiceListing::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(fake()->numberBetween(3, 6));
        $pricingModel = fake()->randomElement(['fixed', 'hourly']);

        return [
            'student_profile_id' => StudentProfile::factory(),
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'title' => rtrim($title, '.'),
            'slug' => Str::slug($title) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'description' => fake()->paragraphs(3, true),
            'pricing_model' => $pricingModel,
            'price' => $pricingModel === 'fixed' 
                ? fake()->randomFloat(2, 50, 1000) 
                : fake()->randomFloat(2, 10, 50),
            'delivery_days' => fake()->numberBetween(1, 30),
            'requirements' => fake()->optional()->paragraph(),
            'portfolio_files' => null,
            'status' => fake()->randomElement(['draft', 'active', 'paused']),
            'views_count' => fake()->numberBetween(0, 500),
            'orders_count' => 0,
            'average_rating' => 0,
        ];
    }

    /**
     * Indicate that the service is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the service is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    /**
     * Indicate that the service has orders.
     */
    public function withOrders(int $count = 5): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'orders_count' => $count,
            'average_rating' => fake()->randomFloat(2, 3.5, 5.0),
            'views_count' => fake()->numberBetween(50, 1000),
        ]);
    }
}
