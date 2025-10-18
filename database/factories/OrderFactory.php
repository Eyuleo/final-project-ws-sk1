<?php

namespace Database\Factories;

use App\Models\ClientProfile;
use App\Models\Order;
use App\Models\ServiceListing;
use App\Models\StudentProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 5);
        $unitPrice = fake()->randomFloat(2, 50, 500);
        $subtotal = $quantity * $unitPrice;
        $platformFee = $subtotal * 0.15; // 15% platform fee
        $totalAmount = $subtotal + $platformFee;

        return [
            'order_number' => 'ORD-' . fake()->unique()->numerify('######'),
            'client_profile_id' => ClientProfile::factory(),
            'student_profile_id' => StudentProfile::factory(),
            'service_listing_id' => ServiceListing::factory(),
            'requirements' => fake()->paragraphs(2, true),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $subtotal,
            'platform_fee' => $platformFee,
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'deadline' => fake()->dateTimeBetween('now', '+30 days'),
            'accepted_at' => null,
            'completed_at' => null,
            'approved_at' => null,
            'cancelled_at' => null,
            'cancellation_reason' => null,
            'deliverable_files' => null,
            'revision_count' => 0,
            'max_revisions' => 2,
            'escrow_status' => 'pending',
            'stripe_payment_intent_id' => null,
        ];
    }

    /**
     * Indicate that the order is accepted.
     */
    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'accepted',
            'accepted_at' => fake()->dateTimeBetween('-7 days', 'now'),
            'escrow_status' => 'held',
            'stripe_payment_intent_id' => 'pi_' . fake()->numerify('####################'),
        ]);
    }

    /**
     * Indicate that the order is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
            'accepted_at' => fake()->dateTimeBetween('-14 days', '-7 days'),
            'escrow_status' => 'held',
            'stripe_payment_intent_id' => 'pi_' . fake()->numerify('####################'),
        ]);
    }

    /**
     * Indicate that the order is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'accepted_at' => fake()->dateTimeBetween('-21 days', '-14 days'),
            'completed_at' => fake()->dateTimeBetween('-7 days', 'now'),
            'escrow_status' => 'held',
            'stripe_payment_intent_id' => 'pi_' . fake()->numerify('####################'),
        ]);
    }

    /**
     * Indicate that the order is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'accepted_at' => fake()->dateTimeBetween('-30 days', '-21 days'),
            'completed_at' => fake()->dateTimeBetween('-14 days', '-7 days'),
            'approved_at' => fake()->dateTimeBetween('-7 days', 'now'),
            'escrow_status' => 'released',
            'stripe_payment_intent_id' => 'pi_' . fake()->numerify('####################'),
        ]);
    }
}
