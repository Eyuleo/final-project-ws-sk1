<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_number',
        'client_profile_id',
        'student_profile_id',
        'service_listing_id',
        'requirements',
        'quantity',
        'unit_price',
        'subtotal',
        'platform_fee',
        'total_amount',
        'status',
        'deadline',
        'accepted_at',
        'completed_at',
        'approved_at',
        'cancelled_at',
        'cancellation_reason',
        'deliverable_files',
        'revision_count',
        'max_revisions',
        'escrow_status',
        'stripe_payment_intent_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'platform_fee' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'deadline' => 'datetime',
            'accepted_at' => 'datetime',
            'completed_at' => 'datetime',
            'approved_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'deliverable_files' => 'array',
            'revision_count' => 'integer',
            'max_revisions' => 'integer',
        ];
    }

    /**
     * Get the client profile that owns the order.
     */
    public function clientProfile(): BelongsTo
    {
        return $this->belongsTo(ClientProfile::class);
    }

    /**
     * Get the student profile that owns the order.
     */
    public function studentProfile(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class);
    }

    /**
     * Get the service listing for the order.
     */
    public function serviceListing(): BelongsTo
    {
        return $this->belongsTo(ServiceListing::class);
    }

    /**
     * Get the messages for the order.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the transactions for the order.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the review for the order.
     */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Scope a query to only include pending orders.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include accepted orders.
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    /**
     * Scope a query to only include in-progress orders.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope a query to only include completed orders.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include approved orders.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Check if order can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'accepted']);
    }

    /**
     * Check if order can be accepted.
     */
    public function canBeAccepted(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if revisions can be requested.
     */
    public function canRequestRevision(): bool
    {
        return $this->status === 'completed' && $this->revision_count < $this->max_revisions;
    }
}
