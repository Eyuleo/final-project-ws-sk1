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
        Schema::create('service_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->enum('pricing_model', ['fixed', 'hourly'])->default('fixed');
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('delivery_days');
            $table->text('requirements')->nullable();
            $table->json('portfolio_files')->nullable();
            $table->enum('status', ['draft', 'active', 'paused'])->default('draft');
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('orders_count')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->timestamps();
            
            $table->index(['status', 'category_id']);
            $table->index('average_rating');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_listings');
    }
};
