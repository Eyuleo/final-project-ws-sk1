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
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('university');
            $table->string('student_id', 100)->nullable();
            $table->text('bio')->nullable();
            $table->json('skills')->nullable();
            $table->decimal('hourly_rate_min', 10, 2)->nullable();
            $table->decimal('hourly_rate_max', 10, 2)->nullable();
            $table->string('portfolio_url')->nullable();
            $table->string('profile_picture')->nullable();
            $table->decimal('total_earnings', 12, 2)->default(0);
            $table->decimal('available_balance', 12, 2)->default(0);
            $table->decimal('pending_balance', 12, 2)->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->unsignedInteger('total_reviews')->default(0);
            $table->unsignedInteger('total_orders')->default(0);
            $table->string('stripe_connect_id')->nullable();
            $table->timestamps();
            
            $table->index('average_rating');
            $table->index('total_orders');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
