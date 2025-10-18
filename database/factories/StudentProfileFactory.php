<?php

namespace Database\Factories;

use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentProfile>
 */
class StudentProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StudentProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $universities = [
            'Addis Ababa University',
            'Jimma University',
            'Bahir Dar University',
            'Hawassa University',
            'Mekelle University',
            'Haramaya University',
            'Arba Minch University',
            'Adama Science and Technology University',
        ];

        $skills = [
            'PHP', 'Laravel', 'JavaScript', 'React', 'Vue.js', 'Node.js',
            'Python', 'Django', 'Java', 'Spring Boot', 'C#', '.NET',
            'Graphic Design', 'Adobe Photoshop', 'Adobe Illustrator', 'Figma',
            'Video Editing', 'Adobe Premiere', 'After Effects',
            'Content Writing', 'SEO', 'Social Media Marketing',
            'Data Analysis', 'Excel', 'SQL', 'Power BI',
        ];

        return [
            'user_id' => User::factory()->state(['role' => 'student']),
            'university' => fake()->randomElement($universities),
            'student_id' => fake()->optional()->numerify('STU-####-####'),
            'bio' => fake()->optional()->paragraph(3),
            'skills' => fake()->randomElements($skills, fake()->numberBetween(3, 8)),
            'hourly_rate_min' => fake()->optional()->randomFloat(2, 10, 30),
            'hourly_rate_max' => fake()->optional()->randomFloat(2, 40, 100),
            'portfolio_url' => fake()->optional()->url(),
            'profile_picture' => null,
            'total_earnings' => 0,
            'available_balance' => 0,
            'pending_balance' => 0,
            'average_rating' => 0,
            'total_reviews' => 0,
            'total_orders' => 0,
            'stripe_connect_id' => null,
        ];
    }

    /**
     * Indicate that the student has completed orders.
     */
    public function withOrders(int $count = 5): static
    {
        return $this->state(fn (array $attributes) => [
            'total_orders' => $count,
            'total_earnings' => fake()->randomFloat(2, 500, 5000),
            'available_balance' => fake()->randomFloat(2, 100, 2000),
            'average_rating' => fake()->randomFloat(2, 3.5, 5.0),
            'total_reviews' => fake()->numberBetween(1, $count),
        ]);
    }
}
