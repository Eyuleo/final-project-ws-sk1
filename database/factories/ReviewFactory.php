<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Review::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $rating = fake()->numberBetween(1, 5);
        
        $positiveReviews = [
            'Excellent work! Very professional and delivered on time.',
            'Great communication and quality work. Highly recommended!',
            'Exceeded my expectations. Will definitely work with again.',
            'Very talented and easy to work with. Perfect results!',
            'Outstanding service! Delivered exactly what I needed.',
        ];

        $neutralReviews = [
            'Good work overall. Met the basic requirements.',
            'Decent service. Could improve on communication.',
            'Satisfactory results. Took a bit longer than expected.',
        ];

        $negativeReviews = [
            'Not what I expected. Had to request multiple revisions.',
            'Poor communication. Delivery was late.',
        ];

        $reviewText = match(true) {
            $rating >= 4 => fake()->randomElement($positiveReviews),
            $rating === 3 => fake()->randomElement($neutralReviews),
            default => fake()->randomElement($negativeReviews),
        };

        $availableTags = ['professional', 'responsive', 'quality', 'communication', 'timely'];
        $tags = $rating >= 4 
            ? fake()->randomElements($availableTags, fake()->numberBetween(2, 4))
            : fake()->optional()->randomElements($availableTags, fake()->numberBetween(0, 2));

        return [
            'order_id' => Order::factory(),
            'reviewer_id' => User::factory()->state(['role' => 'client']),
            'reviewed_id' => User::factory()->state(['role' => 'student']),
            'rating' => $rating,
            'review_text' => $reviewText,
            'tags' => $tags,
            'is_visible' => true,
        ];
    }

    /**
     * Indicate that the review has a high rating.
     */
    public function positive(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => fake()->numberBetween(4, 5),
            'tags' => fake()->randomElements(
                ['professional', 'responsive', 'quality', 'communication', 'timely'],
                fake()->numberBetween(3, 5)
            ),
        ]);
    }

    /**
     * Indicate that the review has a low rating.
     */
    public function negative(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => fake()->numberBetween(1, 2),
            'tags' => null,
        ]);
    }
}
