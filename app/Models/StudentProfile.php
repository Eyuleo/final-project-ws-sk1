<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'university',
        'field_of_study',
        'year_of_study',
        'student_id',
        'bio',
        'tagline',
        'skills',
        'languages',
        'hourly_rate_min',
        'hourly_rate_max',
        'hourly_rate',
        'portfolio_url',
        'github_url',
        'linkedin_url',
        'behance_url',
        'profile_picture',
        'portfolio_files',
        'available_for_work',
        'total_earnings',
        'available_balance',
        'pending_balance',
        'withdrawn_balance',
        'average_rating',
        'total_reviews',
        'total_orders',
        'stripe_connect_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'skills' => 'array',
            'languages' => 'array',
            'portfolio_files' => 'array',
            'available_for_work' => 'boolean',
            'hourly_rate_min' => 'float',
            'hourly_rate_max' => 'float',
            'hourly_rate' => 'float',
            'total_earnings' => 'float',
            'available_balance' => 'float',
            'pending_balance' => 'float',
            'withdrawn_balance' => 'float',
            'average_rating' => 'float',
            'total_reviews' => 'integer',
            'total_orders' => 'integer',
        ];
    }

    /**
     * Get the user that owns the student profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the service listings for the student.
     */
    public function serviceListings(): HasMany
    {
        return $this->hasMany(ServiceListing::class);
    }

    /**
     * Get the orders where the student is the provider.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the withdrawals for the student.
     */
    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    /**
     * Get the reviews received by the student.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewed_id', 'user_id');
    }
}
