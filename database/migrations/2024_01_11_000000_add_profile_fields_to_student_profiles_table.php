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
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->string('tagline', 100)->nullable()->after('bio');
            $table->string('field_of_study')->nullable()->after('university');
            $table->string('year_of_study', 50)->nullable()->after('field_of_study');
            $table->json('languages')->nullable()->after('skills');
            $table->string('github_url')->nullable()->after('portfolio_url');
            $table->string('linkedin_url')->nullable()->after('github_url');
            $table->string('behance_url')->nullable()->after('linkedin_url');
            $table->json('portfolio_files')->nullable()->after('profile_picture');
            $table->boolean('available_for_work')->default(true)->after('portfolio_files');
            $table->decimal('hourly_rate', 10, 2)->nullable()->after('available_for_work');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'tagline',
                'field_of_study',
                'year_of_study',
                'languages',
                'github_url',
                'linkedin_url',
                'behance_url',
                'portfolio_files',
                'available_for_work',
                'hourly_rate',
            ]);
        });
    }
};
