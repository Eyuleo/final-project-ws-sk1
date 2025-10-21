<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Make user_id nullable for platform transactions
            $table->foreignId('user_id')->nullable()->change();
            
            // Drop and recreate the type enum to include new types
            DB::statement("ALTER TABLE transactions MODIFY COLUMN type ENUM('payment', 'escrow_hold', 'escrow_release', 'withdrawal', 'refund', 'commission', 'platform_fee', 'earnings') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Revert user_id to non-nullable
            $table->foreignId('user_id')->nullable(false)->change();
            
            // Revert type enum to original values
            DB::statement("ALTER TABLE transactions MODIFY COLUMN type ENUM('payment', 'escrow_hold', 'escrow_release', 'withdrawal', 'refund', 'commission') NOT NULL");
        });
    }
};
