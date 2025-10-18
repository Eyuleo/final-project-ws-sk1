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
        // First, modify the status enum to include new values
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending_payment', 'pending', 'accepted', 'declined', 'in_progress', 'revision_requested', 'completed', 'approved', 'cancelled', 'disputed') DEFAULT 'pending_payment'");
        
        // Modify escrow_status enum to include 'split'
        DB::statement("ALTER TABLE orders MODIFY COLUMN escrow_status ENUM('pending', 'held', 'released', 'refunded', 'split') DEFAULT 'pending'");
        
        Schema::table('orders', function (Blueprint $table) {
            // Add missing timestamp columns
            $table->timestamp('started_at')->nullable()->after('accepted_at');
            $table->timestamp('declined_at')->nullable()->after('approved_at');
            $table->timestamp('disputed_at')->nullable()->after('cancelled_at');
            
            // Add missing text columns
            $table->text('decline_reason')->nullable()->after('declined_at');
            $table->string('cancelled_by')->nullable()->after('cancellation_reason');
            $table->json('attachment_files')->nullable()->after('cancelled_by');
            $table->text('delivery_note')->nullable()->after('deliverable_files');
            $table->text('revision_notes')->nullable()->after('delivery_note');
            $table->text('dispute_reason')->nullable()->after('max_revisions');
            $table->json('dispute_evidence')->nullable()->after('dispute_reason');
            
            // Add Stripe and payment columns
            $table->string('stripe_session_id')->nullable()->after('stripe_payment_intent_id');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending')->after('stripe_session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'started_at',
                'declined_at',
                'disputed_at',
                'decline_reason',
                'cancelled_by',
                'attachment_files',
                'delivery_note',
                'revision_notes',
                'dispute_reason',
                'dispute_evidence',
                'stripe_session_id',
                'payment_status'
            ]);
        });
        
        // Revert enum changes
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'accepted', 'in_progress', 'revision_requested', 'completed', 'approved', 'cancelled', 'disputed') DEFAULT 'pending'");
        DB::statement("ALTER TABLE orders MODIFY COLUMN escrow_status ENUM('pending', 'held', 'released', 'refunded') DEFAULT 'pending'");
    }
};
