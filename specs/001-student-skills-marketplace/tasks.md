# Tasks: Student Skills Marketplace

**Input**: Design documents from `/specs/001-student-skills-marketplace/`  
**Prerequisites**: plan.md, spec.md, data-model.md, contracts/, research.md, quickstart.md

**Tests**: Tests are NOT explicitly requested in the specification, so test tasks are EXCLUDED from this implementation plan.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`
- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

## Path Conventions
- Laravel monolithic application structure
- `app/` for application code
- `database/` for migrations, factories, seeders
- `resources/views/` for Blade templates
- `routes/web.php` for all routes
- `tests/` for PHPUnit tests

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Project initialization and basic structure

- [x] T001 Install Laravel Breeze with Blade stack via `composer require laravel/breeze --dev && php artisan breeze:install blade`
- [x] T002 Install Spatie Laravel Permission via `composer require spatie/laravel-permission`
- [x] T003 [P] Install Intervention Image via `composer require intervention/image`
- [x] T004 [P] Install Laravel Telescope for development via `composer require laravel/telescope --dev`
- [x] T005 [P] Install Pusher PHP SDK via `composer require pusher/pusher-php-server`
- [x] T006 [P] Install frontend dependencies (Laravel Echo, Pusher JS) via `pnpm add laravel-echo pusher-js`
- [x] T007 Configure Tailwind CSS with custom color palette (avoid purple/indigo) in `tailwind.config.js`
- [x] T008 [P] Configure Vite for asset bundling in `vite.config.js`
- [x] T009 [P] Set up environment variables in `.env` for Stripe, Pusher, database, mail
- [x] T010 Create storage directories structure for uploads (portfolios, deliverables, messages, profiles)

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core infrastructure that MUST be complete before ANY user story can be implemented

**⚠️ CRITICAL**: No user story work can begin until this phase is complete

### Database Schema

- [x] T011 Create users table migration in `database/migrations/2024_01_01_000000_create_users_table.php`
- [x] T012 [P] Create student_profiles table migration in `database/migrations/2024_01_02_000000_create_student_profiles_table.php`
- [x] T013 [P] Create client_profiles table migration in `database/migrations/2024_01_03_000000_create_client_profiles_table.php`
- [x] T014 [P] Create categories table migration in `database/migrations/2024_01_04_000000_create_categories_table.php`
- [x] T015 [P] Create service_listings table migration in `database/migrations/2024_01_05_000000_create_service_listings_table.php`
- [x] T016 [P] Create orders table migration in `database/migrations/2024_01_06_000000_create_orders_table.php`
- [x] T017 [P] Create messages table migration in `database/migrations/2024_01_07_000000_create_messages_table.php`
- [x] T018 [P] Create reviews table migration in `database/migrations/2024_01_08_000000_create_reviews_table.php`
- [x] T019 [P] Create transactions table migration in `database/migrations/2024_01_09_000000_create_transactions_table.php`
- [x] T020 [P] Create withdrawals table migration in `database/migrations/2024_01_10_000000_create_withdrawals_table.php`
- [x] T021 Run migrations via `php artisan migrate`

### Core Models

- [x] T022 Update User model in `app/Models/User.php` with role enum and relationships
- [x] T023 [P] Create StudentProfile model in `app/Models/StudentProfile.php` with relationships and casts
- [x] T024 [P] Create ClientProfile model in `app/Models/ClientProfile.php` with relationships
- [x] T025 [P] Create Category model in `app/Models/Category.php`
- [x] T026 [P] Create ServiceListing model in `app/Models/ServiceListing.php` with status enum and relationships
- [x] T027 [P] Create Order model in `app/Models/Order.php` with status/escrow enums and relationships
- [x] T028 [P] Create Message model in `app/Models/Message.php` with relationships
- [x] T029 [P] Create Review model in `app/Models/Review.php` with relationships
- [x] T030 [P] Create Transaction model in `app/Models/Transaction.php` with type/status enums
- [x] T031 [P] Create Withdrawal model in `app/Models/Withdrawal.php` with method/status enums

### Authentication & Authorization

- [x] T032 Customize Breeze registration to support role selection in `app/Http/Controllers/Auth/RegisteredUserController.php`
- [x] T033 Create separate registration routes for students and clients in `routes/web.php`
- [x] T034 [P] Create EnsureUserIsStudent middleware in `app/Http/Middleware/EnsureUserIsStudent.php`
- [x] T035 [P] Create EnsureUserIsClient middleware in `app/Http/Middleware/EnsureUserIsClient.php`
- [x] T036 [P] Create EnsureUserIsAdmin middleware in `app/Http/Middleware/EnsureUserIsAdmin.php`
- [x] T037 Register custom middleware in `bootstrap/app.php`
- [x] T038 Configure Spatie Permission roles (student, client, admin) in database seeder

### Seeders & Factories

- [x] T039 Create CategorySeeder in `database/seeders/CategorySeeder.php` with initial categories
- [x] T040 [P] Create StudentProfileFactory in `database/factories/StudentProfileFactory.php`
- [x] T041 [P] Create ServiceListingFactory in `database/factories/ServiceListingFactory.php`
- [x] T042 [P] Create OrderFactory in `database/factories/OrderFactory.php`
- [x] T043 [P] Create ReviewFactory in `database/factories/ReviewFactory.php`
- [x] T044 Update DatabaseSeeder in `database/seeders/DatabaseSeeder.php` to create test users
- [x] T045 Run seeders via `php artisan db:seed`

### Service Layer Foundation

- [x] T046 Create PaymentService in `app/Services/PaymentService.php` for Stripe integration
- [x] T047 [P] Create EscrowService in `app/Services/EscrowService.php` for fund management
- [x] T048 [P] Create NotificationService in `app/Services/NotificationService.php` for email/in app messages
- [x] T049 [P] Create FileUploadService in `app/Services/FileUploadService.php` for file handling
- [x] T050 Create stripe configuration file in `config/stripe.php`

### Layout & Components

- [x] T051 Create main app layout in `resources/views/layouts/app.blade.php`
- [x] T052 [P] Create guest layout in `resources/views/layouts/guest.blade.php`
- [x] T053 [P] Create admin layout in `resources/views/layouts/admin.blade.php`
- [x] T054 [P] Create navigation component in `resources/views/components/navigation.blade.php`
- [x] T055 [P] Create service-card component in `resources/views/components/service-card.blade.php`
- [x] T056 [P] Create order-status component in `resources/views/components/order-status.blade.php`
- [x] T057 [P] Create rating-stars component in `resources/views/components/rating-stars.blade.php`

**Checkpoint**: Foundation ready - user story implementation can now begin in parallel

---

## Phase 3: User Story 1 - Student Service Provider Registration & Profile Setup (Priority: P1) 🎯 MVP

**Goal**: Enable university students to register, verify email, build professional profiles with skills/portfolio, and make profiles visible/searchable

**Independent Test**: Register a student account, complete profile setup with skills/portfolio, verify profile is visible and searchable

### Implementation for User Story 1

- [x] T058 [P] [US1] Create student registration view in `resources/views/auth/register-student.blade.php`
- [x] T059 [P] [US1] Create UpdateProfileRequest form request in `app/Http/Requests/Student/UpdateProfileRequest.php`
- [x] T060 [US1] Implement student registration logic in `app/Http/Controllers/Auth/RegisteredUserController.php` (createStudent, storeStudent methods)
- [x] T061 [US1] Create StudentProfileController in `app/Http/Controllers/Student/ProfileController.php` with show, edit, update methods
- [x] T062 [P] [US1] Create student dashboard view in `resources/views/student/dashboard.blade.php`
- [x] T063 [P] [US1] Create profile show view in `resources/views/student/profile/show.blade.php`
- [x] T064 [P] [US1] Create profile edit view in `resources/views/student/profile/edit.blade.php`
- [x] T065 [US1] Add student profile routes to `routes/web.php` with student middleware
- [x] T066 [US1] Implement profile picture upload in FileUploadService with image optimization
- [x] T067 [US1] Implement portfolio file upload in FileUploadService
- [x] T068 [US1] Create public profile view in `resources/views/student/profile/public.blade.php` for provider discovery

**Checkpoint**: Students can register, set up profiles with skills/portfolio, and profiles are publicly visible

---

## Phase 4: User Story 2 - Service Listing Creation & Management (Priority: P1)

**Goal**: Enable student providers to create specific service offerings with descriptions, pricing, delivery time, and manage multiple listings

**Independent Test**: Create multiple service listings with different categories/prices, verify they appear in search results and on student profile

### Implementation for User Story 2

- [x] T069 [P] [US2] Create CreateServiceRequest form request in `app/Http/Requests/Student/CreateServiceRequest.php`
- [x] T070 [P] [US2] Create UpdateServiceRequest form request in `app/Http/Requests/Student/UpdateServiceRequest.php`
- [x] T071 [US2] Create ServiceListingController in `app/Http/Controllers/Student/ServiceListingController.php` with CRUD methods
- [x] T072 [P] [US2] Create service index view in `resources/views/student/services/index.blade.php`
- [x] T073 [P] [US2] Create service create view in `resources/views/student/services/create.blade.php`
- [x] T074 [P] [US2] Create service edit view in `resources/views/student/services/edit.blade.php`
- [x] T075 [P] [US2] Create service show view in `resources/views/student/services/show.blade.php`
- [x] T076 [US2] Add service listing routes to `routes/web.php` with student middleware
- [x] T077 [US2] Implement ServiceListingPolicy in `app/Policies/ServiceListingPolicy.php` for authorization
- [x] T078 [US2] Add portfolio file handling to service listings in FileUploadService
- [x] T079 [US2] Implement service status toggle (active/paused) functionality

**Checkpoint**: Students can create, edit, pause, and delete service listings with portfolio samples

---

## Phase 5: User Story 3 - Client Registration & Service Discovery (Priority: P1)

**Goal**: Enable clients to register, browse/search services by category and filters, view detailed service listings and provider profiles

**Independent Test**: Register client account, search for services using filters (category, price, rating), view service details and provider profiles

### Implementation for User Story 3

- [ ] T080 [P] [US3] Create client registration view in `resources/views/auth/register-client.blade.php`
- [ ] T081 [US3] Implement client registration logic in `app/Http/Controllers/Auth/RegisteredUserController.php` (createClient, storeClient methods)
- [ ] T082 [US3] Create SearchService in `app/Services/SearchService.php` with search and filter methods
- [ ] T083 [US3] Create ServiceDiscoveryController in `app/Http/Controllers/Client/ServiceDiscoveryController.php` with index and show methods
- [ ] T084 [P] [US3] Create homepage view in `resources/views/welcome.blade.php` with featured services and search
- [ ] T085 [P] [US3] Create service discovery index view in `resources/views/client/services/index.blade.php` with filters
- [ ] T086 [P] [US3] Create service detail view in `resources/views/client/services/show.blade.php`
- [ ] T087 [P] [US3] Create client dashboard view in `resources/views/client/dashboard.blade.php`
- [ ] T088 [P] [US3] Create CategoryController in `app/Http/Controllers/CategoryController.php` for category browsing
- [ ] T089 [P] [US3] Create category views in `resources/views/categories/` directory
- [ ] T090 [US3] Add public and client routes to `routes/web.php`
- [ ] T091 [US3] Implement search filters (category, price range, rating, delivery time) in SearchService
- [ ] T092 [US3] Implement sorting options (relevance, price, rating, newest) in SearchService

**Checkpoint**: Clients can register, search/filter services, view detailed listings and provider profiles

---

## Phase 6: User Story 4 - Order Placement & Escrow Payment (Priority: P2)

**Goal**: Enable clients to place orders with custom requirements, make secure payments held in escrow, and confirm orders are in progress

**Independent Test**: Place order with custom requirements, make payment via Stripe, verify funds held in escrow, confirm student receives notification

### Implementation for User Story 4

- [ ] T093 [P] [US4] Create PlaceOrderRequest form request in `app/Http/Requests/Client/PlaceOrderRequest.php`
- [ ] T094 [US4] Create OrderService in `app/Services/OrderService.php` with order lifecycle methods
- [ ] T095 [US4] Create Client OrderController in `app/Http/Controllers/Client/OrderController.php` with create, store, success, cancel methods
- [ ] T096 [P] [US4] Create order create view in `resources/views/client/orders/create.blade.php`
- [ ] T097 [P] [US4] Create order success view in `resources/views/client/orders/success.blade.php`
- [ ] T098 [US4] Implement Stripe Checkout session creation in PaymentService
- [ ] T099 [US4] Implement escrow fund holding in EscrowService
- [ ] T100 [US4] Create Stripe webhook controller in `app/Http/Controllers/StripeWebhookController.php`
- [ ] T101 [US4] Add Stripe webhook route to `routes/web.php` (exclude CSRF)
- [ ] T102 [US4] Implement order calculation (subtotal, platform fee, total) in OrderService
- [ ] T103 [US4] Create order notification job in `app/Jobs/SendOrderNotification.php`
- [ ] T104 [US4] Implement order notification emails in NotificationService
- [ ] T105 [US4] Add client order routes to `routes/web.php` with client middleware
- [ ] T106 [US4] Create OrderPolicy in `app/Policies/OrderPolicy.php` for authorization

**Checkpoint**: Clients can place orders, make payments via Stripe, funds held in escrow, students notified

---

## Phase 7: User Story 5 - Order Management & Delivery (Priority: P2)

**Goal**: Enable students to manage orders (accept/decline), upload deliverables, mark complete; enable clients to review and approve deliverables

**Independent Test**: Accept order, upload deliverable files, mark complete, client reviews and approves, payment released

### Implementation for User Story 5

- [ ] T107 [P] [US5] Create DeclineOrderRequest form request in `app/Http/Requests/Student/DeclineOrderRequest.php`
- [ ] T108 [P] [US5] Create UploadDeliverablesRequest form request in `app/Http/Requests/Student/UploadDeliverablesRequest.php`
- [ ] T109 [P] [US5] Create RequestRevisionRequest form request in `app/Http/Requests/Client/RequestRevisionRequest.php`
- [ ] T110 [US5] Create Student OrderController in `app/Http/Controllers/Student/OrderController.php` with index, show, accept, decline, updateStatus, uploadDeliverables methods
- [ ] T111 [P] [US5] Create student orders index view in `resources/views/student/orders/index.blade.php`
- [ ] T112 [P] [US5] Create student order show view in `resources/views/student/orders/show.blade.php`
- [ ] T113 [P] [US5] Create client orders index view in `resources/views/client/orders/index.blade.php`
- [ ] T114 [P] [US5] Create client order show view in `resources/views/client/orders/show.blade.php`
- [ ] T115 [US5] Add order management methods to Client OrderController (approve, requestRevision)
- [ ] T116 [US5] Implement order status transitions in OrderService (accept, decline, startWork, submitDeliverables, approve, requestRevision)
- [ ] T117 [US5] Implement deliverable file upload in FileUploadService
- [ ] T118 [US5] Implement escrow release in EscrowService when order approved
- [ ] T119 [US5] Add student order routes to `routes/web.php` with student middleware
- [ ] T120 [US5] Create order status update notifications in NotificationService

**Checkpoint**: Students can manage orders and upload deliverables, clients can approve or request revisions, escrow released on approval

---

## Phase 8: User Story 6 - In-Platform Messaging (Priority: P2)

**Goal**: Enable students and clients to communicate about orders through secure in-platform messaging with file attachments and conversation history

**Independent Test**: Send messages between student and client accounts, attach files, verify message history preserved and real-time delivery

### Implementation for User Story 6

- [ ] T121 [P] [US6] Create MessageRequest form request in `app/Http/Requests/MessageRequest.php`
- [ ] T122 [US6] Create MessageController in `app/Http/Controllers/MessageController.php` with index, show, store, markAsRead methods
- [ ] T123 [P] [US6] Create messages index view in `resources/views/messages/index.blade.php`
- [ ] T124 [P] [US6] Create message show view (conversation) in `resources/views/messages/show.blade.php`
- [ ] T125 [P] [US6] Create message-thread component in `resources/views/components/message-thread.blade.php`
- [ ] T126 [US6] Configure Laravel Echo and Pusher in `resources/js/bootstrap.js`
- [ ] T127 [US6] Create MessageSent event in `app/Events/MessageSent.php` for broadcasting
- [ ] T128 [US6] Implement real-time message broadcasting with Pusher
- [ ] T129 [US6] Add message routes to `routes/web.php` with auth middleware
- [ ] T130 [US6] Create MessagePolicy in `app/Policies/MessagePolicy.php` for authorization
- [ ] T131 [US6] Implement message attachment upload in FileUploadService
- [ ] T132 [US6] Create message notification job in `app/Jobs/SendMessageNotification.php`
- [ ] T133 [US6] Add JavaScript for real-time message updates in `resources/js/app.js`

**Checkpoint**: Students and clients can send messages, attach files, receive real-time notifications, view conversation history

---

## Phase 9: User Story 7 - Review & Rating System (Priority: P3)

**Goal**: Enable clients to leave reviews and ratings for students after order completion, display reviews on profiles and service listings

**Independent Test**: Complete order, submit review with rating and text, verify review appears on provider profile and service listing

### Implementation for User Story 7

- [ ] T134 [P] [US7] Create ReviewRequest form request in `app/Http/Requests/Client/ReviewRequest.php`
- [ ] T135 [US7] Create ReviewService in `app/Services/ReviewService.php` with review management and rating aggregation methods
- [ ] T136 [US7] Create ReviewController in `app/Http/Controllers/Client/ReviewController.php` with create and store methods
- [ ] T137 [P] [US7] Create review create view in `resources/views/client/reviews/create.blade.php`
- [ ] T138 [US7] Add review routes to `routes/web.php` with client middleware
- [ ] T139 [US7] Create ReviewPolicy in `app/Policies/ReviewPolicy.php` for authorization
- [ ] T140 [US7] Implement rating aggregation updates in ReviewService (student profile and service listing)
- [ ] T141 [US7] Update student profile views to display reviews and average rating
- [ ] T142 [US7] Update service listing views to display reviews and average rating
- [ ] T143 [US7] Create review display component in `resources/views/components/review-card.blade.php`

**Checkpoint**: Clients can leave reviews after order completion, reviews displayed on profiles and listings with aggregated ratings

---

## Phase 10: User Story 8 - Student Earnings & Withdrawal (Priority: P3)

**Goal**: Enable students to track earnings, view transaction history, and withdraw funds to bank account or mobile money

**Independent Test**: Complete orders to accumulate earnings, request withdrawal, verify funds transferred to student account

### Implementation for User Story 8

- [ ] T144 [P] [US8] Create WithdrawalRequest form request in `app/Http/Requests/Student/WithdrawalRequest.php`
- [ ] T145 [US8] Create WithdrawalService in `app/Services/WithdrawalService.php` with Stripe Connect integration
- [ ] T146 [US8] Create EarningsController in `app/Http/Controllers/Student/EarningsController.php` with index, createWithdrawal, storeWithdrawal, withdrawals methods
- [ ] T147 [P] [US8] Create earnings dashboard view in `resources/views/student/earnings/index.blade.php`
- [ ] T148 [P] [US8] Create withdrawal request view in `resources/views/student/earnings/withdraw.blade.php`
- [ ] T149 [US8] Add earnings and withdrawal routes to `routes/web.php` with student middleware
- [ ] T150 [US8] Implement Stripe Connect account creation in WithdrawalService
- [ ] T151 [US8] Implement Stripe Connect onboarding link generation in WithdrawalService
- [ ] T152 [US8] Implement withdrawal processing job in `app/Jobs/ProcessWithdrawal.php`
- [ ] T153 [US8] Implement balance tracking and transaction history display
- [ ] T154 [US8] Create withdrawal notification in NotificationService
- [ ] T155 [US8] Implement auto-release escrow job in `app/Jobs/ReleaseEscrowFunds.php` for 7-day timeout
- [ ] T156 [US8] Schedule auto-release job in `app/Console/Kernel.php`

**Checkpoint**: Students can view earnings, request withdrawals via Stripe Connect, funds transferred to bank accounts

---

## Phase 11: Admin Features - Dispute Resolution & Moderation

**Purpose**: Enable admin users to moderate platform, resolve disputes, manage users

- [ ] T157 [P] Create DisputeOrderRequest form request in `app/Http/Requests/Client/DisputeOrderRequest.php`
- [ ] T158 [P] Create ResolveDisputeRequest form request in `app/Http/Requests/Admin/ResolveDisputeRequest.php`
- [ ] T159 Create Admin DashboardController in `app/Http/Controllers/Admin/DashboardController.php`
- [ ] T160 [P] Create Admin DisputeController in `app/Http/Controllers/Admin/DisputeController.php` with index, show, resolve methods
- [ ] T161 [P] Create Admin ModerationController in `app/Http/Controllers/Admin/ModerationController.php`
- [ ] T162 [P] Create Admin UserController in `app/Http/Controllers/Admin/UserController.php`
- [ ] T163 [P] Create admin dashboard view in `resources/views/admin/dashboard.blade.php`
- [ ] T164 [P] Create disputes index view in `resources/views/admin/disputes/index.blade.php`
- [ ] T165 [P] Create dispute show view in `resources/views/admin/disputes/show.blade.php`
- [ ] T166 [P] Create moderation views in `resources/views/admin/moderation/` directory
- [ ] T167 Add admin routes to `routes/web.php` with admin middleware
- [ ] T168 Implement dispute opening in Client OrderController
- [ ] T169 Implement dispute resolution (release, refund, split) in OrderService and EscrowService
- [ ] T170 Create dispute notification in NotificationService

---

## Phase 12: Polish & Cross-Cutting Concerns

**Purpose**: Improvements that affect multiple user stories

- [ ] T171 [P] Implement eager loading to prevent N+1 queries across all controllers
- [ ] T172 [P] Add Redis caching for frequently accessed data (categories, featured services)
- [ ] T173 [P] Implement rate limiting on routes (authentication, file uploads, API-like actions)
- [ ] T174 [P] Add pagination to all list views (services, orders, messages, reviews)
- [ ] T175 [P] Implement file size validation and MIME type checking in FileUploadService
- [ ] T176 [P] Add image optimization and thumbnail generation for all uploads
- [ ] T177 [P] Create email templates for all notifications in `resources/views/emails/` directory
- [ ] T178 [P] Implement queue workers configuration in `config/queue.php`
- [ ] T179 [P] Add logging for critical operations (payments, escrow, withdrawals)
- [ ] T180 [P] Implement error handling and user-friendly error pages
- [ ] T181 [P] Add CSRF protection verification on all forms
- [ ] T182 [P] Implement XSS prevention in Blade templates
- [ ] T183 [P] Add database indexes for performance (see data-model.md)
- [ ] T184 [P] Create responsive mobile views for all pages
- [ ] T185 [P] Add loading states and success/error messages to forms
- [ ] T186 [P] Implement breadcrumb navigation across all pages
- [ ] T187 [P] Add search functionality to admin panels
- [ ] T188 [P] Create dashboard widgets for statistics (orders, earnings, users)
- [ ] T189 [P] Implement export functionality for transaction history
- [ ] T190 [P] Add accessibility features (ARIA labels, keyboard navigation)
- [ ] T191 Code cleanup and refactoring per Laravel best practices
- [ ] T192 Run Laravel Pint for code formatting via `sail ./vendor/bin/pint`
- [ ] T193 Security audit (check for SQL injection, XSS, CSRF vulnerabilities)
- [ ] T194 Performance optimization (query optimization, caching strategy)
- [ ] T195 Run quickstart.md validation to ensure setup instructions work
- [ ] T196 Create README.md with project overview and setup instructions
- [ ] T197 Generate API documentation for service layer contracts
- [ ] T198 Final testing of all user journeys end-to-end

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies - can start immediately
- **Foundational (Phase 2)**: Depends on Setup completion - BLOCKS all user stories
- **User Stories (Phase 3-10)**: All depend on Foundational phase completion
  - User stories can then proceed in parallel (if staffed)
  - Or sequentially in priority order (P1 → P2 → P3)
- **Admin Features (Phase 11)**: Depends on User Stories 4-5 (order and dispute functionality)
- **Polish (Phase 12)**: Depends on all desired user stories being complete

### User Story Dependencies

- **User Story 1 (P1)**: Can start after Foundational - No dependencies on other stories
- **User Story 2 (P1)**: Can start after Foundational - Integrates with US1 (student profiles)
- **User Story 3 (P1)**: Can start after Foundational - Requires US2 (service listings to display)
- **User Story 4 (P2)**: Can start after Foundational - Requires US2 (services) and US3 (clients)
- **User Story 5 (P2)**: Can start after Foundational - Requires US4 (orders to manage)
- **User Story 6 (P2)**: Can start after Foundational - Requires US4 (orders for messaging context)
- **User Story 7 (P3)**: Can start after Foundational - Requires US5 (completed orders to review)
- **User Story 8 (P3)**: Can start after Foundational - Requires US5 (completed orders for earnings)

### Within Each User Story

- Form requests before controllers
- Models before services
- Services before controllers
- Controllers before views
- Views before routes
- Core implementation before integration

### Parallel Opportunities

- All Setup tasks marked [P] can run in parallel
- All Foundational tasks marked [P] within same subsection can run in parallel
- Once Foundational phase completes:
  - US1, US2, US3 can start in parallel (P1 stories)
  - After P1 stories: US4, US5, US6 can start in parallel (P2 stories)
  - After P2 stories: US7, US8 can start in parallel (P3 stories)
- All tasks within a user story marked [P] can run in parallel
- All Polish tasks marked [P] can run in parallel

---

## Parallel Example: User Story 1

```bash
# Launch all parallelizable tasks for User Story 1 together:
Task T058: "Create student registration view in resources/views/auth/register-student.blade.php"
Task T059: "Create UpdateProfileRequest form request in app/Http/Requests/Student/UpdateProfileRequest.php"
Task T062: "Create student dashboard view in resources/views/student/dashboard.blade.php"
Task T063: "Create profile show view in resources/views/student/profile/show.blade.php"
Task T064: "Create profile edit view in resources/views/student/profile/edit.blade.php"

# Then sequential tasks:
Task T060: "Implement student registration logic" (depends on T058, T059)
Task T061: "Create StudentProfileController" (depends on T059)
Task T065: "Add student profile routes" (depends on T061, T062, T063, T064)
Task T066: "Implement profile picture upload" (depends on T061)
Task T067: "Implement portfolio file upload" (depends on T061)
Task T068: "Create public profile view" (depends on T061)
```

---

## Implementation Strategy

### MVP First (User Stories 1-3 Only)

1. Complete Phase 1: Setup (10 tasks)
2. Complete Phase 2: Foundational (47 tasks) - **CRITICAL BLOCKER**
3. Complete Phase 3: User Story 1 (11 tasks) - Student registration & profiles
4. Complete Phase 4: User Story 2 (11 tasks) - Service listings
5. Complete Phase 5: User Story 3 (13 tasks) - Client registration & discovery
6. **STOP and VALIDATE**: Test all three stories independently
7. Deploy/demo MVP (students can offer services, clients can discover them)

**MVP Scope**: 92 tasks total for basic marketplace discovery

### Incremental Delivery

1. **Foundation** (Phases 1-2): 57 tasks → Foundation ready
2. **MVP** (Phases 3-5): +35 tasks → Service discovery working
3. **Transactions** (Phases 6-7): +42 tasks → Orders and payments working
4. **Communication** (Phase 8): +13 tasks → Messaging working
5. **Trust & Payouts** (Phases 9-10): +23 tasks → Reviews and withdrawals working
6. **Admin & Polish** (Phases 11-12): +40 tasks → Full platform complete

**Total**: 198 tasks for complete platform

### Parallel Team Strategy

With multiple developers after Foundational phase:

1. **Team completes Setup + Foundational together** (57 tasks)
2. **Once Foundational is done**:
   - Developer A: User Story 1 (11 tasks)
   - Developer B: User Story 2 (11 tasks)
   - Developer C: User Story 3 (13 tasks)
3. **After P1 stories complete**:
   - Developer A: User Story 4 (14 tasks)
   - Developer B: User Story 5 (14 tasks)
   - Developer C: User Story 6 (13 tasks)
4. **After P2 stories complete**:
   - Developer A: User Story 7 (10 tasks)
   - Developer B: User Story 8 (13 tasks)
   - Developer C: Admin Features (14 tasks)
5. **All developers**: Polish tasks in parallel (28 tasks)

---

## Task Summary

### By Phase
- **Phase 1 (Setup)**: 10 tasks
- **Phase 2 (Foundational)**: 47 tasks
- **Phase 3 (US1 - Student Registration)**: 11 tasks
- **Phase 4 (US2 - Service Listings)**: 11 tasks
- **Phase 5 (US3 - Client Discovery)**: 13 tasks
- **Phase 6 (US4 - Order Placement)**: 14 tasks
- **Phase 7 (US5 - Order Management)**: 14 tasks
- **Phase 8 (US6 - Messaging)**: 13 tasks
- **Phase 9 (US7 - Reviews)**: 10 tasks
- **Phase 10 (US8 - Earnings)**: 13 tasks
- **Phase 11 (Admin)**: 14 tasks
- **Phase 12 (Polish)**: 28 tasks

**Total Tasks**: 198

### By Priority
- **P1 Stories (US1-US3)**: 35 tasks (MVP core)
- **P2 Stories (US4-US6)**: 41 tasks (Transactions & communication)
- **P3 Stories (US7-US8)**: 23 tasks (Trust & payouts)
- **Infrastructure (Setup + Foundational)**: 57 tasks
- **Admin & Polish**: 42 tasks

### Parallel Opportunities
- **Setup Phase**: 7 parallelizable tasks
- **Foundational Phase**: 38 parallelizable tasks
- **User Stories**: 72 parallelizable tasks across all stories
- **Polish Phase**: 26 parallelizable tasks

**Total Parallelizable**: 143 tasks (72% of all tasks)

---

## Notes

- [P] tasks = different files, no dependencies
- [Story] label maps task to specific user story for traceability
- Each user story should be independently completable and testable
- Commit after each task or logical group
- Stop at any checkpoint to validate story independently
- Tests are NOT included as they were not explicitly requested in spec.md
- Follow Laravel best practices and PSR-12 coding standards
- Use Blade templates for all views (no separate API)
- All routes through web.php with CSRF protection
- Queue all emails and notifications
- Eager load relationships to prevent N+1 queries

---

**Generated**: 2025-10-18  
**Feature**: Student Skills Marketplace  
**Source Documents**: plan.md, spec.md, data-model.md, contracts/, research.md, quickstart.md
