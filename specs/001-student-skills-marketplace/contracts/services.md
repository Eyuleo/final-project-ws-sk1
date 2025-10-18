# Service Layer Contracts

**Date**: 2025-10-18  
**Feature**: Student Skills Marketplace

## Overview

This document defines service layer contracts for complex business logic. Services encapsulate business operations that span multiple models or require external integrations.

---

## Service Classes

### 1. OrderService

**Purpose**: Manage order lifecycle, status transitions, and business rules.

**Location**: `app/Services/OrderService.php`

```php
namespace App\Services;

use App\Models\Order;
use App\Models\ServiceListing;
use App\Models\ClientProfile;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Create a new order from service listing
     *
     * @param ServiceListing $service
     * @param ClientProfile $client
     * @param array $data ['requirements', 'quantity', 'deadline']
     * @return Order
     * @throws \Exception
     */
    public function createOrder(ServiceListing $service, ClientProfile $client, array $data): Order;

    /**
     * Accept an order (student action)
     *
     * @param Order $order
     * @return Order
     * @throws \Exception if order not in pending status
     */
    public function acceptOrder(Order $order): Order;

    /**
     * Decline an order with reason (student action)
     *
     * @param Order $order
     * @param string $reason
     * @return Order
     * @throws \Exception if order not in pending status
     */
    public function declineOrder(Order $order, string $reason): Order;

    /**
     * Mark order as in progress (student action)
     *
     * @param Order $order
     * @return Order
     * @throws \Exception if order not accepted
     */
    public function startWork(Order $order): Order;

    /**
     * Upload deliverables and mark as completed (student action)
     *
     * @param Order $order
     * @param array $files Uploaded files
     * @param string|null $note Delivery note
     * @return Order
     * @throws \Exception if order not in progress
     */
    public function submitDeliverables(Order $order, array $files, ?string $note = null): Order;

    /**
     * Approve order and release escrow (client action)
     *
     * @param Order $order
     * @return Order
     * @throws \Exception if order not completed
     */
    public function approveOrder(Order $order): Order;

    /**
     * Request revision (client action)
     *
     * @param Order $order
     * @param string $feedback
     * @return Order
     * @throws \Exception if max revisions exceeded or order not completed
     */
    public function requestRevision(Order $order, string $feedback): Order;

    /**
     * Open dispute (client action)
     *
     * @param Order $order
     * @param string $reason
     * @param array $evidenceFiles
     * @return Order
     * @throws \Exception if revisions not exhausted
     */
    public function openDispute(Order $order, string $reason, array $evidenceFiles = []): Order;

    /**
     * Cancel order (mutual or admin action)
     *
     * @param Order $order
     * @param string $reason
     * @param string $cancelledBy 'client', 'student', or 'admin'
     * @return Order
     * @throws \Exception if order cannot be cancelled
     */
    public function cancelOrder(Order $order, string $reason, string $cancelledBy): Order;

    /**
     * Auto-release escrow after 7 days (scheduled job)
     *
     * @param Order $order
     * @return Order
     */
    public function autoReleaseEscrow(Order $order): Order;

    /**
     * Calculate order totals
     *
     * @param ServiceListing $service
     * @param int $quantity
     * @return array ['subtotal', 'platform_fee', 'total_amount']
     */
    public function calculateOrderTotals(ServiceListing $service, int $quantity): array;
}
```

---

### 2. PaymentService

**Purpose**: Handle Stripe payment processing, checkout sessions, and webhooks.

**Location**: `app/Services/PaymentService.php`

```php
namespace App\Services;

use App\Models\Order;
use App\Models\ClientProfile;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;

class PaymentService
{
    /**
     * Create Stripe Checkout session for order payment
     *
     * @param Order $order
     * @return Session
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function createCheckoutSession(Order $order): Session;

    /**
     * Handle successful payment webhook
     *
     * @param string $paymentIntentId
     * @return void
     * @throws \Exception
     */
    public function handlePaymentSuccess(string $paymentIntentId): void;

    /**
     * Handle failed payment webhook
     *
     * @param string $paymentIntentId
     * @return void
     */
    public function handlePaymentFailed(string $paymentIntentId): void;

    /**
     * Create or retrieve Stripe customer for client
     *
     * @param ClientProfile $client
     * @return string Stripe customer ID
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function getOrCreateStripeCustomer(ClientProfile $client): string;

    /**
     * Process refund to client
     *
     * @param Order $order
     * @param float|null $amount Refund amount (null for full refund)
     * @return string Refund ID
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function processRefund(Order $order, ?float $amount = null): string;

    /**
     * Verify webhook signature
     *
     * @param string $payload
     * @param string $signature
     * @return \Stripe\Event
     * @throws \Stripe\Exception\SignatureVerificationException
     */
    public function verifyWebhookSignature(string $payload, string $signature): \Stripe\Event;
}
```

---

### 3. EscrowService

**Purpose**: Manage escrow funds, releases, and balance tracking.

**Location**: `app/Services/EscrowService.php`

```php
namespace App\Services;

use App\Models\Order;
use App\Models\StudentProfile;
use App\Models\Transaction;

class EscrowService
{
    /**
     * Hold funds in escrow after successful payment
     *
     * @param Order $order
     * @return Transaction
     */
    public function holdFunds(Order $order): Transaction;

    /**
     * Release escrow funds to student
     *
     * @param Order $order
     * @return Transaction
     * @throws \Exception if escrow not held
     */
    public function releaseFunds(Order $order): Transaction;

    /**
     * Refund escrow funds to client
     *
     * @param Order $order
     * @param float|null $amount Refund amount (null for full refund)
     * @return Transaction
     * @throws \Exception if escrow not held
     */
    public function refundFunds(Order $order, ?float $amount = null): Transaction;

    /**
     * Split escrow funds (for dispute resolution)
     *
     * @param Order $order
     * @param float $studentAmount
     * @param float $clientAmount
     * @return array [Transaction, Transaction]
     * @throws \Exception if amounts don't match order total
     */
    public function splitFunds(Order $order, float $studentAmount, float $clientAmount): array;

    /**
     * Update student balance after escrow release
     *
     * @param StudentProfile $student
     * @param float $amount
     * @return void
     */
    public function updateStudentBalance(StudentProfile $student, float $amount): void;

    /**
     * Get orders eligible for auto-release (completed > 7 days ago)
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEligibleForAutoRelease(): \Illuminate\Database\Eloquent\Collection;

    /**
     * Calculate platform commission
     *
     * @param float $amount
     * @return float Commission amount (15%)
     */
    public function calculateCommission(float $amount): float;
}
```

---

### 4. WithdrawalService

**Purpose**: Process student withdrawal requests via Stripe Connect.

**Location**: `app/Services/WithdrawalService.php`

```php
namespace App\Services;

use App\Models\StudentProfile;
use App\Models\Withdrawal;
use Stripe\Transfer;
use Stripe\Payout;

class WithdrawalService
{
    /**
     * Create withdrawal request
     *
     * @param StudentProfile $student
     * @param array $data ['amount', 'method', 'account_details']
     * @return Withdrawal
     * @throws \Exception if insufficient balance
     */
    public function createWithdrawal(StudentProfile $student, array $data): Withdrawal;

    /**
     * Process withdrawal via Stripe Connect
     *
     * @param Withdrawal $withdrawal
     * @return Withdrawal
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function processWithdrawal(Withdrawal $withdrawal): Withdrawal;

    /**
     * Create or retrieve Stripe Connect account for student
     *
     * @param StudentProfile $student
     * @return string Stripe Connect account ID
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function getOrCreateConnectAccount(StudentProfile $student): string;

    /**
     * Generate Stripe Connect onboarding link
     *
     * @param StudentProfile $student
     * @return string Onboarding URL
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function generateOnboardingLink(StudentProfile $student): string;

    /**
     * Check if student has completed Stripe Connect onboarding
     *
     * @param StudentProfile $student
     * @return bool
     */
    public function hasCompletedOnboarding(StudentProfile $student): bool;

    /**
     * Cancel withdrawal request
     *
     * @param Withdrawal $withdrawal
     * @return Withdrawal
     * @throws \Exception if already processed
     */
    public function cancelWithdrawal(Withdrawal $withdrawal): Withdrawal;

    /**
     * Calculate withdrawal fee
     *
     * @param float $amount
     * @param string $method
     * @return float Fee amount
     */
    public function calculateWithdrawalFee(float $amount, string $method): float;
}
```

---

### 5. SearchService

**Purpose**: Handle service discovery, filtering, and search.

**Location**: `app/Services/SearchService.php`

```php
namespace App\Services;

use App\Models\ServiceListing;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchService
{
    /**
     * Search and filter service listings
     *
     * @param array $filters [
     *   'query' => string,
     *   'category_id' => int,
     *   'price_min' => float,
     *   'price_max' => float,
     *   'rating_min' => float,
     *   'delivery_days_max' => int,
     *   'sort' => string ('relevance', 'price_asc', 'price_desc', 'rating', 'newest')
     * ]
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function searchServices(array $filters, int $perPage = 20): LengthAwarePaginator;

    /**
     * Get featured services for homepage
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFeaturedServices(int $limit = 12): \Illuminate\Database\Eloquent\Collection;

    /**
     * Get top-rated services
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTopRatedServices(int $limit = 10): \Illuminate\Database\Eloquent\Collection;

    /**
     * Get recently added services
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentServices(int $limit = 10): \Illuminate\Database\Eloquent\Collection;

    /**
     * Get related services (same category)
     *
     * @param ServiceListing $service
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRelatedServices(ServiceListing $service, int $limit = 6): \Illuminate\Database\Eloquent\Collection;

    /**
     * Apply search query filter
     *
     * @param Builder $query
     * @param string $searchTerm
     * @return Builder
     */
    protected function applySearchFilter(Builder $query, string $searchTerm): Builder;

    /**
     * Apply sorting
     *
     * @param Builder $query
     * @param string $sort
     * @return Builder
     */
    protected function applySorting(Builder $query, string $sort): Builder;
}
```

---

### 6. NotificationService

**Purpose**: Send notifications via email, SMS, and in-app channels.

**Location**: `app/Services/NotificationService.php`

```php
namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\Message;

class NotificationService
{
    /**
     * Notify student of new order
     *
     * @param Order $order
     * @return void
     */
    public function notifyNewOrder(Order $order): void;

    /**
     * Notify client of order acceptance
     *
     * @param Order $order
     * @return void
     */
    public function notifyOrderAccepted(Order $order): void;

    /**
     * Notify client of order completion
     *
     * @param Order $order
     * @return void
     */
    public function notifyOrderCompleted(Order $order): void;

    /**
     * Notify student of order approval
     *
     * @param Order $order
     * @return void
     */
    public function notifyOrderApproved(Order $order): void;

    /**
     * Notify student of revision request
     *
     * @param Order $order
     * @param string $feedback
     * @return void
     */
    public function notifyRevisionRequested(Order $order, string $feedback): void;

    /**
     * Notify both parties of dispute opened
     *
     * @param Order $order
     * @return void
     */
    public function notifyDisputeOpened(Order $order): void;

    /**
     * Notify both parties of dispute resolution
     *
     * @param Order $order
     * @param string $resolution
     * @param string $adminNotes
     * @return void
     */
    public function notifyDisputeResolved(Order $order, string $resolution, string $adminNotes): void;

    /**
     * Notify user of new message
     *
     * @param Message $message
     * @return void
     */
    public function notifyNewMessage(Message $message): void;

    /**
     * Notify student of withdrawal processed
     *
     * @param \App\Models\Withdrawal $withdrawal
     * @return void
     */
    public function notifyWithdrawalProcessed(\App\Models\Withdrawal $withdrawal): void;

    /**
     * Send email notification
     *
     * @param User $user
     * @param string $subject
     * @param string $view
     * @param array $data
     * @return void
     */
    protected function sendEmail(User $user, string $subject, string $view, array $data): void;

    /**
     * Send SMS notification (optional)
     *
     * @param User $user
     * @param string $message
     * @return void
     */
    protected function sendSMS(User $user, string $message): void;

    /**
     * Create in-app notification
     *
     * @param User $user
     * @param string $type
     * @param string $message
     * @param array $data
     * @return void
     */
    protected function createInAppNotification(User $user, string $type, string $message, array $data): void;
}
```

---

### 7. FileUploadService

**Purpose**: Handle file uploads, validation, and storage.

**Location**: `app/Services/FileUploadService.php`

```php
namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class FileUploadService
{
    /**
     * Upload profile picture with optimization
     *
     * @param UploadedFile $file
     * @param string $userId
     * @return string File path
     */
    public function uploadProfilePicture(UploadedFile $file, string $userId): string;

    /**
     * Upload portfolio file
     *
     * @param UploadedFile $file
     * @param string $studentId
     * @return string File path
     */
    public function uploadPortfolioFile(UploadedFile $file, string $studentId): string;

    /**
     * Upload deliverable file
     *
     * @param UploadedFile $file
     * @param string $orderId
     * @return string File path
     */
    public function uploadDeliverableFile(UploadedFile $file, string $orderId): string;

    /**
     * Upload message attachment
     *
     * @param UploadedFile $file
     * @param string $messageId
     * @return string File path
     */
    public function uploadMessageAttachment(UploadedFile $file, string $messageId): string;

    /**
     * Delete file from storage
     *
     * @param string $path
     * @return bool
     */
    public function deleteFile(string $path): bool;

    /**
     * Generate thumbnail for image
     *
     * @param string $imagePath
     * @param int $width
     * @param int $height
     * @return string Thumbnail path
     */
    public function generateThumbnail(string $imagePath, int $width, int $height): string;

    /**
     * Optimize image (resize and compress)
     *
     * @param UploadedFile $file
     * @param int $maxWidth
     * @param int $quality
     * @return \Intervention\Image\Image
     */
    protected function optimizeImage(UploadedFile $file, int $maxWidth = 1200, int $quality = 80): \Intervention\Image\Image;

    /**
     * Generate unique filename
     *
     * @param UploadedFile $file
     * @param string $prefix
     * @return string
     */
    protected function generateFilename(UploadedFile $file, string $prefix = ''): string;

    /**
     * Validate file type and size
     *
     * @param UploadedFile $file
     * @param array $allowedMimes
     * @param int $maxSize Size in KB
     * @return bool
     * @throws \Exception if validation fails
     */
    protected function validateFile(UploadedFile $file, array $allowedMimes, int $maxSize): bool;
}
```

---

### 8. ReviewService

**Purpose**: Manage reviews, ratings, and aggregations.

**Location**: `app/Services/ReviewService.php`

```php
namespace App\Services;

use App\Models\Order;
use App\Models\Review;
use App\Models\StudentProfile;

class ReviewService
{
    /**
     * Create review for completed order
     *
     * @param Order $order
     * @param array $data ['rating', 'review_text', 'tags']
     * @return Review
     * @throws \Exception if order not approved or review already exists
     */
    public function createReview(Order $order, array $data): Review;

    /**
     * Update student profile rating after new review
     *
     * @param StudentProfile $student
     * @return void
     */
    public function updateStudentRating(StudentProfile $student): void;

    /**
     * Update service listing rating after new review
     *
     * @param int $serviceListingId
     * @return void
     */
    public function updateServiceRating(int $serviceListingId): void;

    /**
     * Get reviews for student profile
     *
     * @param StudentProfile $student
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getStudentReviews(StudentProfile $student, int $perPage = 10): \Illuminate\Pagination\LengthAwarePaginator;

    /**
     * Get reviews for service listing
     *
     * @param int $serviceListingId
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getServiceReviews(int $serviceListingId, int $perPage = 10): \Illuminate\Pagination\LengthAwarePaginator;

    /**
     * Flag review for moderation
     *
     * @param Review $review
     * @param string $reason
     * @return void
     */
    public function flagReview(Review $review, string $reason): void;

    /**
     * Calculate rating distribution
     *
     * @param StudentProfile $student
     * @return array ['5' => count, '4' => count, ...]
     */
    public function getRatingDistribution(StudentProfile $student): array;
}
```

---

## Service Layer Best Practices

### 1. Dependency Injection

```php
class OrderService
{
    public function __construct(
        protected PaymentService $paymentService,
        protected EscrowService $escrowService,
        protected NotificationService $notificationService
    ) {}
}
```

### 2. Database Transactions

```php
public function approveOrder(Order $order): Order
{
    return DB::transaction(function () use ($order) {
        $order->update(['status' => 'approved', 'approved_at' => now()]);
        
        $this->escrowService->releaseFunds($order);
        $this->notificationService->notifyOrderApproved($order);
        
        return $order->fresh();
    });
}
```

### 3. Exception Handling

```php
public function createOrder(ServiceListing $service, ClientProfile $client, array $data): Order
{
    if ($service->status !== 'active') {
        throw new \Exception('Service is not available.');
    }
    
    if (!$client->stripe_customer_id) {
        throw new \Exception('Client must have payment method on file.');
    }
    
    // Create order...
}
```

### 4. Event Dispatching

```php
use App\Events\OrderCreated;

public function createOrder(...): Order
{
    $order = Order::create([...]);
    
    event(new OrderCreated($order));
    
    return $order;
}
```

### 5. Queued Operations

```php
use App\Jobs\SendOrderNotification;

public function notifyNewOrder(Order $order): void
{
    SendOrderNotification::dispatch($order)->onQueue('notifications');
}
```

---

## Testing Service Classes

### Unit Test Example

```php
namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\OrderService;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_accept_pending_order()
    {
        $order = Order::factory()->create(['status' => 'pending']);
        $service = app(OrderService::class);
        
        $result = $service->acceptOrder($order);
        
        $this->assertEquals('accepted', $result->status);
        $this->assertNotNull($result->accepted_at);
    }

    public function test_cannot_accept_non_pending_order()
    {
        $order = Order::factory()->create(['status' => 'in_progress']);
        $service = app(OrderService::class);
        
        $this->expectException(\Exception::class);
        $service->acceptOrder($order);
    }
}
```

---

**Next Steps**: Generate quickstart.md for local development setup.
