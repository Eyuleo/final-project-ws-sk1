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
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('dispute_resolved_at')->nullable()->after('disputed_at');
            $table->string('dispute_resolution')->nullable()->after('dispute_resolved_at');
            $table->text('admin_notes')->nullable()->after('dispute_resolution');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['dispute_resolved_at', 'dispute_resolution', 'admin_notes']);
        });
    }
};
