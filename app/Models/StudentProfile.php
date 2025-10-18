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
        'student_id',
        'bio',
        'skills',
        'hourly_rate_min',
        'hourly_rate_max',
        'portfolio_url',
        'profile_picture',
        'total_earnings',
        'available_balance',
        'pending_balance',
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
            'hourly_rate_min' => 'decimal:2',
            'hourly_rate_max' => 'decimal:2',
            'total_earnings' => 'decimal:2',
            'available_balance' => 'decimal:2',
            'pending_balance' => 'decimal:2',
            'average_rating' => 'decimal:2',
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
