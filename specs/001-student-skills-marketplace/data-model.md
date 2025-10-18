# Data Model: Student Skills Marketplace

**Date**: 2025-10-18  
**Feature**: Student Skills Marketplace

## Overview

This document defines the complete database schema, entity relationships, validation rules, and state transitions for the Student Skills Marketplace application.

## Database Schema

### Entity Relationship Diagram

```
users (1) ──── (1) student_profiles
users (1) ──── (1) client_profiles
users (1) ──── (*) messages
users (1) ──── (*) reviews (as reviewer)
users (1) ──── (*) reviews (as reviewed)

student_profiles (1) ──── (*) service_listings
student_profiles (1) ──── (*) withdrawals
student_profiles (1) ──── (*) orders (as provider)

client_profiles (1) ──── (*) orders (as client)
client_profiles (1) ──── (*) reviews

categories (1) ──── (*) service_listings

service_listings (1) ──── (*) orders

orders (1) ──── (*) messages
orders (1) ──── (*) transactions
orders (1) ──── (0..1) reviews

transactions (*) ──── (1) users
```

---

## Entity Definitions

### 1. users

Base authentication table for all user types.

**Table**: `users`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint unsigned | PK, auto-increment | Primary key |
| name | varchar(255) | NOT NULL | Full name |
| email | varchar(255) | NOT NULL, UNIQUE | Email address |
| email_verified_at | timestamp | NULL | Email verification timestamp |
| password | varchar(255) | NOT NULL | Hashed password |
| phone | varchar(20) | NULL | Phone number |
| role | enum | NOT NULL | 'student', 'client', 'admin' |
| is_active | boolean | DEFAULT true | Account status |
| remember_token | varchar(100) | NULL | Remember me token |
| created_at | timestamp | NULL | Creation timestamp |
| updated_at | timestamp | NULL | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- UNIQUE KEY (email)
- INDEX (role)

**Validation Rules**:
```php
'name' => 'required|string|max:255',
'email' => 'required|string|email|max:255|unique:users',
'password' => 'required|string|min:8|confirmed',
'phone' => 'nullable|string|max:20',
'role' => 'required|in:student,client,admin',
```

---

### 2. student_profiles

Extended profile information for student providers.

**Table**: `student_profiles`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint unsigned | PK, auto-increment | Primary key |
| user_id | bigint unsigned | FK, UNIQUE, NOT NULL | Reference to users |
| university | varchar(255) | NOT NULL | University name |
| student_id | varchar(100) | NULL | Student ID number |
| bio | text | NULL | Profile bio |
| skills | json | NULL | Array of skills |
| hourly_rate_min | decimal(10,2) | NULL | Minimum hourly rate |
| hourly_rate_max | decimal(10,2) | NULL | Maximum hourly rate |
| portfolio_url | varchar(255) | NULL | External portfolio link |
| profile_picture | varchar(255) | NULL | Profile picture path |
| total_earnings | decimal(12,2) | DEFAULT 0 | Lifetime earnings |
| available_balance | decimal(12,2) | DEFAULT 0 | Withdrawable balance |
| pending_balance | decimal(12,2) | DEFAULT 0 | Balance in escrow |
| average_rating | decimal(3,2) | DEFAULT 0 | Average rating (0-5) |
| total_reviews | int unsigned | DEFAULT 0 | Total review count |
| total_orders | int unsigned | DEFAULT 0 | Completed orders |
| stripe_connect_id | varchar(255) | NULL | Stripe Connect account ID |
| created_at | timestamp | NULL | Creation timestamp |
| updated_at | timestamp | NULL | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- UNIQUE KEY (user_id)
- FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
- INDEX (average_rating)
- INDEX (total_orders)

**Validation Rules**:
```php
'university' => 'required|string|max:255',
'student_id' => 'nullable|string|max:100',
'bio' => 'nullable|string|max:1000',
'skills' => 'nullable|array',
'hourly_rate_min' => 'nullable|numeric|min:0',
'hourly_rate_max' => 'nullable|numeric|min:0|gte:hourly_rate_min',
'portfolio_url' => 'nullable|url|max:255',
'profile_picture' => 'nullable|image|max:2048',
```

---

### 3. client_profiles

Extended profile information for clients.

**Table**: `client_profiles`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint unsigned | PK, auto-increment | Primary key |
| user_id | bigint unsigned | FK, UNIQUE, NOT NULL | Reference to users |
| organization | varchar(255) | NULL | Organization name |
| bio | text | NULL | Profile bio |
| profile_picture | varchar(255) | NULL | Profile picture path |
| total_orders | int unsigned | DEFAULT 0 | Total orders placed |
| average_rating | decimal(3,2) | DEFAULT 0 | Average rating from students |
| total_reviews | int unsigned | DEFAULT 0 | Total reviews received |
| stripe_customer_id | varchar(255) | NULL | Stripe customer ID |
| created_at | timestamp | NULL | Creation timestamp |
| updated_at | timestamp | NULL | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- UNIQUE KEY (user_id)
- FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE

**Validation Rules**:
```php
'organization' => 'nullable|string|max:255',
'bio' => 'nullable|string|max:1000',
'profile_picture' => 'nullable|image|max:2048',
```

---

### 4. categories

Service categories for organizing listings.

**Table**: `categories`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint unsigned | PK, auto-increment | Primary key |
| name | varchar(100) | NOT NULL, UNIQUE | Category name |
| slug | varchar(100) | NOT NULL, UNIQUE | URL-friendly slug |
| description | text | NULL | Category description |
| icon | varchar(100) | NULL | Icon identifier |
| is_active | boolean | DEFAULT true | Category status |
| sort_order | int unsigned | DEFAULT 0 | Display order |
| created_at | timestamp | NULL | Creation timestamp |
| updated_at | timestamp | NULL | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- UNIQUE KEY (name)
- UNIQUE KEY (slug)
- INDEX (is_active, sort_order)

**Validation Rules**:
```php
'name' => 'required|string|max:100|unique:categories',
'slug' => 'required|string|max:100|unique:categories',
'description' => 'nullable|string|max:500',
```

**Seed Data**:
- Design (Graphic Design, Logo Design, UI/UX)
- Development (Web Development, Mobile Apps, WordPress)
- Writing (Content Writing, Copywriting, Translation)
- Tutoring (Math, Science, Languages, Test Prep)
- Marketing (Social Media, SEO, Email Marketing)
- Video Editing (Video Production, Animation)
- Data Entry (Data Analysis, Excel, Research)

---

### 5. service_listings

Services offered by student providers.

**Table**: `service_listings`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint unsigned | PK, auto-increment | Primary key |
| student_profile_id | bigint unsigned | FK, NOT NULL | Reference to student_profiles |
| category_id | bigint unsigned | FK, NOT NULL | Reference to categories |
| title | varchar(255) | NOT NULL | Service title |
| slug | varchar(255) | NOT NULL | URL-friendly slug |
| description | text | NOT NULL | Service description |
| pricing_model | enum | NOT NULL | 'fixed', 'hourly' |
| price | decimal(10,2) | NOT NULL | Price amount |
| delivery_days | int unsigned | NOT NULL | Delivery time in days |
| requirements | text | NULL | What client needs to provide |
| portfolio_files | json | NULL | Array of portfolio file paths |
| status | enum | DEFAULT 'draft' | 'draft', 'active', 'paused' |
| views_count | int unsigned | DEFAULT 0 | Total views |
| orders_count | int unsigned | DEFAULT 0 | Total orders |
| average_rating | decimal(3,2) | DEFAULT 0 | Average rating |
| created_at | timestamp | NULL | Creation timestamp |
| updated_at | timestamp | NULL | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- FOREIGN KEY (student_profile_id) REFERENCES student_profiles(id) ON DELETE CASCADE
- FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
- UNIQUE KEY (slug)
- INDEX (status, category_id)
- INDEX (average_rating)
- INDEX (created_at)

**Validation Rules**:
```php
'title' => 'required|string|max:255',
'description' => 'required|string|min:100|max:5000',
'category_id' => 'required|exists:categories,id',
'pricing_model' => 'required|in:fixed,hourly',
'price' => 'required|numeric|min:5|max:10000',
'delivery_days' => 'required|integer|min:1|max:90',
'requirements' => 'nullable|string|max:2000',
'portfolio_files' => 'nullable|array|max:5',
'portfolio_files.*' => 'file|mimes:jpg,png,pdf,docx|max:10240',
```

---

### 6. orders

Orders placed by clients for services.

**Table**: `orders`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint unsigned | PK, auto-increment | Primary key |
| order_number | varchar(50) | UNIQUE, NOT NULL | Human-readable order number |
| client_profile_id | bigint unsigned | FK, NOT NULL | Reference to client_profiles |
| student_profile_id | bigint unsigned | FK, NOT NULL | Reference to student_profiles |
| service_listing_id | bigint unsigned | FK, NOT NULL | Reference to service_listings |
| requirements | text | NOT NULL | Client requirements |
| quantity | int unsigned | DEFAULT 1 | Quantity (for hourly: hours) |
| unit_price | decimal(10,2) | NOT NULL | Price per unit |
| subtotal | decimal(10,2) | NOT NULL | Subtotal amount |
| platform_fee | decimal(10,2) | NOT NULL | Platform commission (15%) |
| total_amount | decimal(10,2) | NOT NULL | Total amount paid |
| status | enum | DEFAULT 'pending' | Order status |
| deadline | timestamp | NOT NULL | Delivery deadline |
| accepted_at | timestamp | NULL | When student accepted |
| completed_at | timestamp | NULL | When marked complete |
| approved_at | timestamp | NULL | When client approved |
| cancelled_at | timestamp | NULL | When cancelled |
| cancellation_reason | text | NULL | Cancellation reason |
| deliverable_files | json | NULL | Array of deliverable file paths |
| revision_count | int unsigned | DEFAULT 0 | Number of revisions |
| max_revisions | int unsigned | DEFAULT 2 | Maximum revisions allowed |
| escrow_status | enum | DEFAULT 'pending' | 'pending', 'held', 'released', 'refunded' |
| stripe_payment_intent_id | varchar(255) | NULL | Stripe payment intent ID |
| created_at | timestamp | NULL | Creation timestamp |
| updated_at | timestamp | NULL | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- UNIQUE KEY (order_number)
- FOREIGN KEY (client_profile_id) REFERENCES client_profiles(id) ON DELETE RESTRICT
- FOREIGN KEY (student_profile_id) REFERENCES student_profiles(id) ON DELETE RESTRICT
- FOREIGN KEY (service_listing_id) REFERENCES service_listings(id) ON DELETE RESTRICT
- INDEX (status, created_at)
- INDEX (escrow_status)

**Status Values**:
- `pending`: Order placed, awaiting student acceptance
- `accepted`: Student accepted order
- `in_progress`: Work in progress
- `revision_requested`: Client requested revisions
- `completed`: Student marked as complete, awaiting client approval
- `approved`: Client approved, payment released
- `cancelled`: Order cancelled
- `disputed`: Under admin review

**Validation Rules**:
```php
'service_listing_id' => 'required|exists:service_listings,id',
'requirements' => 'required|string|min:50|max:5000',
'quantity' => 'required|integer|min:1|max:100',
'deadline' => 'required|date|after:now',
'deliverable_files' => 'nullable|array|max:10',
'deliverable_files.*' => 'file|mimes:jpg,png,pdf,docx,zip|max:10240',
```

---

### 7. messages

In-platform messaging between users.

**Table**: `messages`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint unsigned | PK, auto-increment | Primary key |
| order_id | bigint unsigned | FK, NOT NULL | Reference to orders |
| sender_id | bigint unsigned | FK, NOT NULL | Reference to users |
| receiver_id | bigint unsigned | FK, NOT NULL | Reference to users |
| message | text | NOT NULL | Message content |
| attachment_path | varchar(255) | NULL | Attachment file path |
| is_read | boolean | DEFAULT false | Read status |
| read_at | timestamp | NULL | When message was read |
| created_at | timestamp | NULL | Creation timestamp |
| updated_at | timestamp | NULL | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
- FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE
- FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
- INDEX (order_id, created_at)
- INDEX (receiver_id, is_read)

**Validation Rules**:
```php
'order_id' => 'required|exists:orders,id',
'message' => 'required|string|max:5000',
'attachment' => 'nullable|file|mimes:jpg,png,pdf,docx|max:5120',
```

---

### 8. reviews

Reviews and ratings for completed orders.

**Table**: `reviews`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint unsigned | PK, auto-increment | Primary key |
| order_id | bigint unsigned | FK, UNIQUE, NOT NULL | Reference to orders |
| reviewer_id | bigint unsigned | FK, NOT NULL | Reference to users (reviewer) |
| reviewed_id | bigint unsigned | FK, NOT NULL | Reference to users (reviewed) |
| rating | tinyint unsigned | NOT NULL | Rating (1-5) |
| review_text | text | NULL | Review content |
| tags | json | NULL | Review tags (professional, responsive, etc.) |
| is_visible | boolean | DEFAULT true | Visibility status |
| created_at | timestamp | NULL | Creation timestamp |
| updated_at | timestamp | NULL | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- UNIQUE KEY (order_id)
- FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
- FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE CASCADE
- FOREIGN KEY (reviewed_id) REFERENCES users(id) ON DELETE CASCADE
- INDEX (reviewed_id, rating)

**Validation Rules**:
```php
'order_id' => 'required|exists:orders,id|unique:reviews',
'rating' => 'required|integer|min:1|max:5',
'review_text' => 'nullable|string|min:20|max:1000',
'tags' => 'nullable|array',
'tags.*' => 'string|in:professional,responsive,quality,communication,timely',
```

---

### 9. transactions

Financial transaction records.

**Table**: `transactions`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint unsigned | PK, auto-increment | Primary key |
| user_id | bigint unsigned | FK, NOT NULL | Reference to users |
| order_id | bigint unsigned | FK, NULL | Reference to orders (if applicable) |
| type | enum | NOT NULL | Transaction type |
| amount | decimal(12,2) | NOT NULL | Transaction amount |
| fee | decimal(12,2) | DEFAULT 0 | Platform fee |
| net_amount | decimal(12,2) | NOT NULL | Net amount after fees |
| status | enum | DEFAULT 'pending' | Transaction status |
| stripe_transaction_id | varchar(255) | NULL | Stripe transaction ID |
| description | text | NULL | Transaction description |
| metadata | json | NULL | Additional metadata |
| created_at | timestamp | NULL | Creation timestamp |
| updated_at | timestamp | NULL | Last update timestamp |

**Type Values**:
- `payment`: Client payment for order
- `escrow_hold`: Funds held in escrow
- `escrow_release`: Funds released to student
- `withdrawal`: Student withdrawal
- `refund`: Refund to client
- `commission`: Platform commission

**Status Values**:
- `pending`: Transaction initiated
- `processing`: Being processed
- `completed`: Successfully completed
- `failed`: Transaction failed
- `cancelled`: Transaction cancelled

**Indexes**:
- PRIMARY KEY (id)
- FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
- FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL
- INDEX (user_id, type, created_at)
- INDEX (status)

---

### 10. withdrawals

Student withdrawal requests.

**Table**: `withdrawals`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint unsigned | PK, auto-increment | Primary key |
| student_profile_id | bigint unsigned | FK, NOT NULL | Reference to student_profiles |
| amount | decimal(12,2) | NOT NULL | Withdrawal amount |
| fee | decimal(12,2) | DEFAULT 0 | Processing fee |
| net_amount | decimal(12,2) | NOT NULL | Net amount after fees |
| method | enum | NOT NULL | Withdrawal method |
| account_details | json | NOT NULL | Encrypted account details |
| status | enum | DEFAULT 'pending' | Withdrawal status |
| stripe_payout_id | varchar(255) | NULL | Stripe payout ID |
| processed_at | timestamp | NULL | When processed |
| notes | text | NULL | Admin notes |
| created_at | timestamp | NULL | Creation timestamp |
| updated_at | timestamp | NULL | Last update timestamp |

**Method Values**:
- `bank_transfer`: Bank account transfer
- `mobile_money`: Mobile money transfer

**Status Values**:
- `pending`: Awaiting processing
- `processing`: Being processed
- `completed`: Successfully completed
- `failed`: Withdrawal failed
- `cancelled`: Withdrawal cancelled

**Indexes**:
- PRIMARY KEY (id)
- FOREIGN KEY (student_profile_id) REFERENCES student_profiles(id) ON DELETE CASCADE
- INDEX (student_profile_id, status, created_at)

**Validation Rules**:
```php
'amount' => 'required|numeric|min:10|max:available_balance',
'method' => 'required|in:bank_transfer,mobile_money',
'account_details' => 'required|array',
'account_details.account_number' => 'required_if:method,bank_transfer',
'account_details.bank_name' => 'required_if:method,bank_transfer',
'account_details.phone_number' => 'required_if:method,mobile_money',
```

---

## State Transitions

### Order Status Flow

```
pending → accepted → in_progress → completed → approved
   ↓         ↓            ↓            ↓
cancelled  cancelled  revision_requested  disputed
                           ↓
                      in_progress
```

**Transition Rules**:
- `pending` → `accepted`: Student accepts order
- `pending` → `cancelled`: Client or student cancels before acceptance
- `accepted` → `in_progress`: Student starts work
- `accepted` → `cancelled`: Student cancels with reason
- `in_progress` → `completed`: Student uploads deliverables
- `in_progress` → `cancelled`: Mutual agreement or admin decision
- `completed` → `approved`: Client approves work
- `completed` → `revision_requested`: Client requests revisions (max 2)
- `completed` → `disputed`: Client disputes quality
- `revision_requested` → `in_progress`: Student works on revisions
- `approved`: Final state, payment released
- `cancelled`: Final state, refund processed
- `disputed` → `approved` or `cancelled`: Admin decision

### Escrow Status Flow

```
pending → held → released
   ↓       ↓        
refunded  refunded
```

**Transition Rules**:
- `pending` → `held`: Payment successful
- `pending` → `refunded`: Payment failed or order cancelled before acceptance
- `held` → `released`: Order approved by client or auto-released after 7 days
- `held` → `refunded`: Order cancelled or disputed with client favor

---

## Calculated Fields & Aggregations

### Student Profile Aggregations

Updated via database triggers or scheduled jobs:

```php
// Average rating
$student->average_rating = $student->reviews()->avg('rating');

// Total earnings (lifetime)
$student->total_earnings = $student->transactions()
    ->where('type', 'escrow_release')
    ->sum('net_amount');

// Available balance (withdrawable)
$student->available_balance = $student->transactions()
    ->where('type', 'escrow_release')
    ->sum('net_amount')
    - $student->withdrawals()
    ->where('status', 'completed')
    ->sum('amount');

// Pending balance (in escrow)
$student->pending_balance = $student->orders()
    ->where('escrow_status', 'held')
    ->sum('total_amount');
```

### Service Listing Aggregations

```php
// Average rating
$service->average_rating = $service->orders()
    ->whereHas('review')
    ->join('reviews', 'orders.id', '=', 'reviews.order_id')
    ->avg('reviews.rating');

// Orders count
$service->orders_count = $service->orders()
    ->whereIn('status', ['approved', 'completed'])
    ->count();
```

---

## Database Indexes Strategy

### Performance Indexes

1. **Search & Discovery**:
   - `service_listings(status, category_id, average_rating)`
   - `service_listings(status, created_at)`

2. **Order Management**:
   - `orders(student_profile_id, status, created_at)`
   - `orders(client_profile_id, status, created_at)`
   - `orders(escrow_status, completed_at)` for auto-release job

3. **Messaging**:
   - `messages(order_id, created_at)`
   - `messages(receiver_id, is_read)`

4. **Financial**:
   - `transactions(user_id, type, created_at)`
   - `withdrawals(student_profile_id, status, created_at)`

---

## Data Integrity Rules

### Foreign Key Constraints

- **CASCADE DELETE**: When parent is deleted, children are deleted
  - `users` → `student_profiles`, `client_profiles`
  - `orders` → `messages`, `transactions`, `reviews`
  - `service_listings` → `orders` (RESTRICT to prevent deletion with active orders)

- **RESTRICT DELETE**: Prevent deletion if children exist
  - `categories` → `service_listings`
  - `student_profiles` → `orders` (can't delete student with orders)
  - `client_profiles` → `orders` (can't delete client with orders)

### Data Validation

- All monetary values use `decimal(12,2)` for precision
- Enum fields enforce valid state values
- JSON fields validated at application level
- Timestamps use UTC timezone
- Soft deletes not used (hard deletes with constraints)

---

**Next Steps**: Proceed to generate API contracts (routes.md, forms.md, services.md).
