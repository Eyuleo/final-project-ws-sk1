<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique();
            $table->foreignId('client_profile_id')->constrained()->onDelete('restrict');
            $table->foreignId('student_profile_id')->constrained()->onDelete('restrict');
            $table->foreignId('service_listing_id')->constrained()->onDelete('restrict');
            $table->text('requirements');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('platform_fee', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', [
                'pending',
                'accepted',
                'in_progress',
                'revision_requested',
                'completed',
                'approved',
                'cancelled',
                'disputed'
            ])->default('pending');
            $table->timestamp('deadline');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->json('deliverable_files')->nullable();
            $table->unsignedInteger('revision_count')->default(0);
            $table->unsignedInteger('max_revisions')->default(2);
            $table->enum('escrow_status', ['pending', 'held', 'released', 'refunded'])->default('pending');
            $table->string('stripe_payment_intent_id')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index('escrow_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
