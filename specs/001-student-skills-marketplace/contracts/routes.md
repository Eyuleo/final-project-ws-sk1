# Web Routes Contract

**Date**: 2025-10-18  
**Feature**: Student Skills Marketplace

## Overview

This document defines all web routes for the Student Skills Marketplace application. All routes use `web.php` with server-side rendering via Blade templates.

---

## Route Groups & Middleware

### Public Routes (Guest)
- No authentication required
- CSRF protection on forms
- Rate limiting on registration/login

### Authenticated Routes
- `auth` middleware required
- CSRF protection on all forms
- Session-based authentication

### Role-Specific Routes
- `role:student` middleware for student features
- `role:client` middleware for client features
- `role:admin` middleware for admin features

---

## Route Definitions

### Authentication Routes (Laravel Breeze)

```php
// Registration
GET  /register/student          → Auth\RegisteredUserController@createStudent
POST /register/student          → Auth\RegisteredUserController@storeStudent
GET  /register/client           → Auth\RegisteredUserController@createClient
POST /register/client           → Auth\RegisteredUserController@storeClient

// Login
GET  /login                     → Auth\AuthenticatedSessionController@create
POST /login                     → Auth\AuthenticatedSessionController@store
POST /logout                    → Auth\AuthenticatedSessionController@destroy

// Password Reset
GET  /forgot-password           → Auth\PasswordResetLinkController@create
POST /forgot-password           → Auth\PasswordResetLinkController@store
GET  /reset-password/{token}    → Auth\NewPasswordController@create
POST /reset-password            → Auth\NewPasswordController@store

// Email Verification
GET  /verify-email              → Auth\EmailVerificationPromptController@__invoke
GET  /verify-email/{id}/{hash}  → Auth\VerifyEmailController@__invoke
POST /email/verification-notification → Auth\EmailVerificationNotificationController@store
```

---

### Public Routes

```php
// Homepage
GET  /                          → HomeController@index
    - Display featured services, categories, search
    - No auth required

// Service Discovery
GET  /services                  → Client\ServiceDiscoveryController@index
    - List all services with filters
    - Query params: category, price_min, price_max, rating, sort
    - No auth required

GET  /services/{slug}           → Client\ServiceDiscoveryController@show
    - Show service details, provider profile, reviews
    - No auth required

// Category Browsing
GET  /categories                → CategoryController@index
    - List all categories
    - No auth required

GET  /categories/{slug}         → CategoryController@show
    - Show services in category
    - No auth required

// Provider Profiles (Public View)
GET  /providers/{id}            → Student\ProfileController@showPublic
    - Show student profile, services, reviews
    - No auth required
```

---

### Student Routes

**Middleware**: `auth`, `verified`, `role:student`

```php
// Dashboard
GET  /student/dashboard         → Student\DashboardController@index
    - Overview: pending orders, earnings, recent activity

// Profile Management
GET  /student/profile           → Student\ProfileController@show
    - View own profile
    
GET  /student/profile/edit      → Student\ProfileController@edit
    - Edit profile form
    
PUT  /student/profile           → Student\ProfileController@update
    - Update profile (bio, skills, rates, portfolio)
    - Validation: UpdateProfileRequest

// Service Listings
GET  /student/services          → Student\ServiceController@index
    - List student's services (all statuses)
    
GET  /student/services/create   → Student\ServiceController@create
    - Create service form
    
POST /student/services          → Student\ServiceController@store
    - Store new service
    - Validation: StoreServiceRequest
    
GET  /student/services/{id}/edit → Student\ServiceController@edit
    - Edit service form
    
PUT  /student/services/{id}     → Student\ServiceController@update
    - Update service
    - Validation: UpdateServiceRequest
    
DELETE /student/services/{id}   → Student\ServiceController@destroy
    - Delete service (only if no active orders)
    
PATCH /student/services/{id}/status → Student\ServiceController@updateStatus
    - Toggle status (active/paused)

// Order Management
GET  /student/orders            → Student\OrderController@index
    - List all orders (filter by status)
    
GET  /student/orders/{id}       → Student\OrderController@show
    - View order details, requirements, deliverables
    
POST /student/orders/{id}/accept → Student\OrderController@accept
    - Accept order
    
POST /student/orders/{id}/decline → Student\OrderController@decline
    - Decline order with reason
    - Validation: DeclineOrderRequest
    
PATCH /student/orders/{id}/status → Student\OrderController@updateStatus
    - Update order status (in_progress, completed)
    
POST /student/orders/{id}/deliverables → Student\OrderController@uploadDeliverables
    - Upload deliverable files
    - Validation: UploadDeliverablesRequest

// Earnings & Withdrawals
GET  /student/earnings          → Student\EarningsController@index
    - View earnings dashboard, transaction history
    
GET  /student/earnings/withdraw → Student\EarningsController@createWithdrawal
    - Withdrawal request form
    
POST /student/earnings/withdraw → Student\EarningsController@storeWithdrawal
    - Submit withdrawal request
    - Validation: WithdrawalRequest
    
GET  /student/withdrawals       → Student\EarningsController@withdrawals
    - List withdrawal history
```

---

### Client Routes

**Middleware**: `auth`, `verified`, `role:client`

```php
// Dashboard
GET  /client/dashboard          → Client\DashboardController@index
    - Overview: active orders, recent services

// Service Discovery (Authenticated)
GET  /client/services           → Client\ServiceDiscoveryController@index
    - Same as public but with saved/favorited indicators
    
GET  /client/services/{slug}    → Client\ServiceDiscoveryController@show
    - Service details with "Order Now" button

// Order Placement
GET  /client/orders/create      → Client\OrderController@create
    - Order form (requires service_id param)
    - Validation: service exists and is active
    
POST /client/orders             → Client\OrderController@store
    - Place order and process payment
    - Validation: PlaceOrderRequest
    - Redirects to Stripe Checkout
    
GET  /client/orders/success     → Client\OrderController@success
    - Payment success callback from Stripe
    
GET  /client/orders/cancel      → Client\OrderController@cancel
    - Payment cancelled callback

// Order Management
GET  /client/orders             → Client\OrderController@index
    - List all orders (filter by status)
    
GET  /client/orders/{id}        → Client\OrderController@show
    - View order details, deliverables
    
POST /client/orders/{id}/approve → Client\OrderController@approve
    - Approve completed order (releases escrow)
    
POST /client/orders/{id}/revision → Client\OrderController@requestRevision
    - Request revision with feedback
    - Validation: RequestRevisionRequest
    
POST /client/orders/{id}/dispute → Client\OrderController@dispute
    - Open dispute (admin mediation)
    - Validation: DisputeOrderRequest

// Reviews
GET  /client/orders/{id}/review → Client\ReviewController@create
    - Review form (only for approved orders)
    
POST /client/reviews            → Client\ReviewController@store
    - Submit review
    - Validation: ReviewRequest
```

---

### Messaging Routes

**Middleware**: `auth`, `verified`

```php
// Message Inbox
GET  /messages                  → MessageController@index
    - List all conversations grouped by order
    
GET  /messages/{orderId}        → MessageController@show
    - View conversation for specific order
    - Authorization: user is part of order
    
POST /messages                  → MessageController@store
    - Send message
    - Validation: MessageRequest
    - Authorization: user is part of order
    
PATCH /messages/{orderId}/read  → MessageController@markAsRead
    - Mark all messages in conversation as read
```

---

### Admin Routes

**Middleware**: `auth`, `verified`, `role:admin`

```php
// Dashboard
GET  /admin/dashboard           → Admin\DashboardController@index
    - Platform statistics, recent activity

// User Management
GET  /admin/users               → Admin\UserController@index
    - List all users (filter by role, status)
    
GET  /admin/users/{id}          → Admin\UserController@show
    - View user details
    
PATCH /admin/users/{id}/status  → Admin\UserController@updateStatus
    - Activate/deactivate user account

// Dispute Resolution
GET  /admin/disputes            → Admin\DisputeController@index
    - List all disputed orders
    
GET  /admin/disputes/{id}       → Admin\DisputeController@show
    - View dispute details, evidence, messages
    
POST /admin/disputes/{id}/resolve → Admin\DisputeController@resolve
    - Resolve dispute (release funds, refund, or split)
    - Validation: ResolveDisputeRequest

// Moderation
GET  /admin/services            → Admin\ModerationController@services
    - List flagged services
    
GET  /admin/reviews             → Admin\ModerationController@reviews
    - List flagged reviews
    
DELETE /admin/services/{id}     → Admin\ModerationController@deleteService
    - Remove service listing
    
DELETE /admin/reviews/{id}      → Admin\ModerationController@deleteReview
    - Remove review

// Reports & Analytics
GET  /admin/reports             → Admin\ReportController@index
    - Platform analytics, revenue, user growth
```

---

## Route Naming Convention

All routes use named routes for easy reference in views:

```php
// Examples
Route::get('/student/dashboard', [DashboardController::class, 'index'])
    ->name('student.dashboard');

Route::post('/student/orders/{order}/accept', [OrderController::class, 'accept'])
    ->name('student.orders.accept');

Route::get('/client/services/{service}', [ServiceDiscoveryController::class, 'show'])
    ->name('client.services.show');
```

**Naming Pattern**: `{role}.{resource}.{action}`

---

## Authorization Policies

Routes use Laravel Policies for fine-grained authorization:

```php
// Service Listing
- update: Only owner can update
- delete: Only owner can delete (no active orders)
- pause: Only owner can pause

// Order
- view: Client or student involved in order
- accept: Student who owns the service
- approve: Client who placed order
- requestRevision: Client who placed order

// Message
- view: User is part of order
- send: User is part of order

// Review
- create: Client who placed order, order is approved
```

---

## Rate Limiting

```php
// Authentication routes
RateLimiter::for('auth', function (Request $request) {
    return Limit::perMinute(5)->by($request->ip());
});

// API-like actions (search, filters)
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
});

// File uploads
RateLimiter::for('uploads', function (Request $request) {
    return Limit::perMinute(10)->by($request->user()->id);
});
```

---

## CSRF Protection

All POST, PUT, PATCH, DELETE requests require CSRF token:

```blade
<form method="POST" action="{{ route('student.services.store') }}">
    @csrf
    <!-- form fields -->
</form>
```

---

## Redirect Rules

- Unauthenticated users → `/login`
- Unverified email → `/verify-email`
- Wrong role accessing route → `/dashboard` (their role's dashboard)
- After login → Role-specific dashboard
- After registration → Email verification prompt

---

**Next Steps**: Generate forms.md with validation contracts.
