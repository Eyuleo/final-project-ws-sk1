# Implementation Plan: Student Skills Marketplace

**Branch**: `001-student-skills-marketplace` | **Date**: 2025-10-18 | **Spec**: [spec.md](./spec.md)
**Input**: Feature specification from `/specs/001-student-skills-marketplace/spec.md`

## Summary

The Student Skills Marketplace is a traditional Laravel web application that connects university students in Ethiopia with clients who need their services. The platform uses server-side rendering with Blade templates, Tailwind CSS for styling, and follows Laravel MVC architecture. Key features include student/client registration, service listings, order management with escrow payments via Stripe, in-platform messaging, reviews/ratings, and admin moderation. The application handles payments through Stripe and Stripe Connect, with a 15% platform commission on completed transactions.

## Technical Context

**Language/Version**: PHP 8.2+ with Laravel 11.x  
**Primary Dependencies**: 
- Laravel Breeze (Blade + Tailwind authentication scaffolding)
- Spatie Laravel Permission (role-based authorization)
- Laravel Cashier or custom Stripe integration
- Intervention Image (image processing for portfolios)
- Laravel Echo + Pusher or Laravel WebSockets (real-time messaging)
- PNPM (frontend package management)
- Vite (asset bundling)

**Storage**: MySQL 8.0+ for relational data, Redis for caching and queues, S3-compatible storage for file uploads  
**Testing**: PHPUnit for unit and feature tests, Laravel Dusk for critical UI flows (optional)  
**Target Platform**: Linux server (Ubuntu 22.04+), PHP-FPM with Nginx, deployed via Laravel Forge or similar  
**Project Type**: Single monolithic web application (traditional Laravel MVC)  
**Performance Goals**: 
- API response time: p95 <200ms, p99 <500ms
- Database queries: <10 per page load, individual queries <50ms
- Support 100 concurrent users without degradation
- Page load time: <3s on 3G connection

**Constraints**: 
- Server-side rendering only (no separate API)
- All routes through web.php (no API routes)
- Blade templates for all views
- CSRF protection on all forms
- Ethiopian market focus (Stripe payment methods available in Ethiopia)

**Scale/Scope**: 
- Target: 50 active student providers and 100 completed orders in first 3 months
- Expected growth: 500+ students, 1000+ orders within 6 months
- Database: Support 1M+ records with maintained performance

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

Verify compliance with `.specify/memory/constitution.md` principles:

- [x] **Code Quality & Maintainability**: PSR-12 compliance via Laravel Pint, strict types enabled, service layer for business logic, meaningful naming conventions
- [x] **Test-First Development**: TDD cycle planned with PHPUnit for unit tests (models, services) and feature tests (controllers, routes, user workflows)
- [x] **Laravel Best Practices**: Eloquent ORM for all database operations, service classes for complex logic, Form Requests for validation, API Resources for responses, queues for emails/notifications
- [x] **Database Integrity**: Migrations for all schema changes, foreign key constraints on relationships, indexes on searchable columns, factories and seeders for test data
- [x] **Security First**: Laravel Breeze for authentication, Spatie Permission for authorization, Form Requests for input validation, Blade escaping for XSS prevention, CSRF tokens on all forms, mass assignment protection
- [x] **Performance & Scalability**: Eager loading to prevent N+1 queries, Redis caching for expensive operations, queued jobs for emails and notifications, pagination for large collections, chunking for batch operations
- [x] **User Experience Consistency**: Tailwind CSS for responsive design, inline validation feedback, loading states for async operations, success confirmations, accessible forms with proper labels

**Violations Requiring Justification** (if any):
- None. All constitution principles are followed.

## Project Structure

### Documentation (this feature)

```
specs/001-student-skills-marketplace/
├── plan.md              # This file (/speckit.plan command output)
├── research.md          # Phase 0 output (/speckit.plan command)
├── data-model.md        # Phase 1 output (/speckit.plan command)
├── quickstart.md        # Phase 1 output (/speckit.plan command)
├── contracts/           # Phase 1 output (/speckit.plan command)
│   ├── routes.md        # Web routes documentation
│   ├── forms.md         # Form validation contracts
│   └── services.md      # Service layer contracts
└── tasks.md             # Phase 2 output (/speckit.tasks command - NOT created by /speckit.plan)
```

### Source Code (repository root)

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/                    # Laravel Breeze authentication controllers
│   │   ├── Student/
│   │   │   ├── ProfileController.php
│   │   │   ├── ServiceController.php
│   │   │   ├── OrderController.php
│   │   │   ├── EarningsController.php
│   │   │   └── WithdrawalController.php
│   │   ├── Client/
│   │   │   ├── ServiceDiscoveryController.php
│   │   │   ├── OrderController.php
│   │   │   └── ReviewController.php
│   │   ├── MessageController.php
│   │   └── Admin/
│   │       ├── DashboardController.php
│   │       ├── DisputeController.php
│   │       └── ModerationController.php
│   ├── Requests/
│   │   ├── Student/
│   │   │   ├── StoreServiceRequest.php
│   │   │   ├── UpdateProfileRequest.php
│   │   │   └── WithdrawalRequest.php
│   │   ├── Client/
│   │   │   ├── PlaceOrderRequest.php
│   │   │   └── ReviewRequest.php
│   │   └── MessageRequest.php
│   └── Middleware/
│       ├── EnsureUserIsStudent.php
│       ├── EnsureUserIsClient.php
│       └── EnsureUserIsAdmin.php
├── Models/
│   ├── User.php
│   ├── StudentProfile.php
│   ├── ClientProfile.php
│   ├── Category.php
│   ├── ServiceListing.php
│   ├── Order.php
│   ├── Message.php
│   ├── Review.php
│   ├── Transaction.php
│   └── Withdrawal.php
├── Services/
│   ├── OrderService.php            # Order lifecycle management
│   ├── PaymentService.php          # Stripe integration and escrow
│   ├── EscrowService.php           # Escrow fund management
│   ├── NotificationService.php     # Email/SMS notifications
│   ├── SearchService.php           # Service discovery and filtering
│   └── WithdrawalService.php       # Payout processing
├── Jobs/
│   ├── SendOrderNotification.php
│   ├── ProcessWithdrawal.php
│   ├── ReleaseEscrowFunds.php
│   └── SendMessageNotification.php
├── Policies/
│   ├── ServiceListingPolicy.php
│   ├── OrderPolicy.php
│   ├── MessagePolicy.php
│   └── ReviewPolicy.php
└── Providers/
    └── AppServiceProvider.php

database/
├── migrations/
│   ├── 2024_01_01_000000_create_users_table.php
│   ├── 2024_01_02_000000_create_student_profiles_table.php
│   ├── 2024_01_03_000000_create_client_profiles_table.php
│   ├── 2024_01_04_000000_create_categories_table.php
│   ├── 2024_01_05_000000_create_service_listings_table.php
│   ├── 2024_01_06_000000_create_orders_table.php
│   ├── 2024_01_07_000000_create_messages_table.php
│   ├── 2024_01_08_000000_create_reviews_table.php
│   ├── 2024_01_09_000000_create_transactions_table.php
│   └── 2024_01_10_000000_create_withdrawals_table.php
├── factories/
│   ├── StudentProfileFactory.php
│   ├── ServiceListingFactory.php
│   ├── OrderFactory.php
│   └── ReviewFactory.php
└── seeders/
    ├── CategorySeeder.php
    └── DatabaseSeeder.php

resources/
├── views/
│   ├── layouts/
│   │   ├── app.blade.php           # Main layout
│   │   ├── guest.blade.php         # Guest layout
│   │   └── admin.blade.php         # Admin layout
│   ├── components/
│   │   ├── navigation.blade.php
│   │   ├── service-card.blade.php
│   │   ├── order-status.blade.php
│   │   ├── rating-stars.blade.php
│   │   └── message-thread.blade.php
│   ├── auth/                        # Laravel Breeze auth views
│   ├── student/
│   │   ├── dashboard.blade.php
│   │   ├── profile/
│   │   │   ├── edit.blade.php
│   │   │   └── show.blade.php
│   │   ├── services/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   ├── edit.blade.php
│   │   │   └── show.blade.php
│   │   ├── orders/
│   │   │   ├── index.blade.php
│   │   │   └── show.blade.php
│   │   └── earnings/
│   │       ├── index.blade.php
│   │       └── withdraw.blade.php
│   ├── client/
│   │   ├── dashboard.blade.php
│   │   ├── services/
│   │   │   ├── index.blade.php
│   │   │   └── show.blade.php
│   │   ├── orders/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   └── show.blade.php
│   │   └── reviews/
│   │       └── create.blade.php
│   ├── messages/
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   ├── admin/
│   │   ├── dashboard.blade.php
│   │   ├── disputes/
│   │   │   ├── index.blade.php
│   │   │   └── show.blade.php
│   │   └── moderation/
│   │       └── index.blade.php
│   └── welcome.blade.php            # Public homepage
├── css/
│   └── app.css                      # Tailwind CSS
└── js/
    ├── app.js                       # Main JS entry
    └── echo.js                      # Laravel Echo configuration

routes/
└── web.php                          # All application routes

tests/
├── Feature/
│   ├── Auth/
│   │   ├── RegistrationTest.php
│   │   └── AuthenticationTest.php
│   ├── Student/
│   │   ├── ProfileTest.php
│   │   ├── ServiceListingTest.php
│   │   ├── OrderManagementTest.php
│   │   └── WithdrawalTest.php
│   ├── Client/
│   │   ├── ServiceDiscoveryTest.php
│   │   ├── OrderPlacementTest.php
│   │   └── ReviewTest.php
│   ├── MessageTest.php
│   └── Admin/
│       └── DisputeResolutionTest.php
└── Unit/
    ├── Services/
    │   ├── OrderServiceTest.php
    │   ├── PaymentServiceTest.php
    │   ├── EscrowServiceTest.php
    │   └── SearchServiceTest.php
    └── Models/
        ├── OrderTest.php
        └── ServiceListingTest.php

config/
├── stripe.php                       # Stripe configuration
└── services.php                     # Third-party services

public/
├── images/                          # Static images
└── storage/                         # Symlink to storage/app/public
```

**Structure Decision**: This is a single monolithic Laravel web application following traditional MVC architecture. All application logic resides in the `app/` directory with clear separation of concerns: Controllers handle HTTP requests and return Blade views, Models represent database entities, Services contain complex business logic, and Policies enforce authorization. The frontend uses Blade templates organized by user role (student, client, admin) with reusable components. All routing is handled through `routes/web.php` with no separate API layer.

## Complexity Tracking

*No constitution violations requiring justification.*

## Phase 0: Research & Technology Decisions

See [research.md](./research.md) for detailed technology research and decisions.

**Key Research Areas**:
1. Stripe vs. Stripe Connect for marketplace payments
2. Laravel Breeze vs. Jetstream for authentication scaffolding
3. Real-time messaging implementation (Pusher vs. Laravel WebSockets vs. polling)
4. File storage strategy (local vs. S3)
5. Queue driver selection (database vs. Redis)
6. Role-based authorization approach (Gates/Policies vs. Spatie Permission)

## Phase 1: Design Artifacts

### Data Model
See [data-model.md](./data-model.md) for complete database schema, relationships, and validation rules.

### API Contracts
See [contracts/](./contracts/) directory for:
- **routes.md**: Complete web route definitions with middleware and authorization
- **forms.md**: Form Request validation rules for all user inputs
- **services.md**: Service layer method signatures and contracts

### Quickstart Guide
See [quickstart.md](./quickstart.md) for local development setup instructions.

## Implementation Phases

### Phase 2: Task Generation
Run `/speckit.tasks` to generate actionable, dependency-ordered tasks in `tasks.md`.

### Phase 3: Implementation
Run `/speckit.implement` to execute the implementation plan by processing all tasks.

## Success Metrics

From spec.md Success Criteria:
- Students can complete registration and profile setup in <5 minutes
- Service listing creation in <3 minutes
- 90% successful order placement rate
- 80% order acceptance rate within 24 hours
- <200ms p95 API response time
- 100 concurrent users supported
- 50 active students and 100 completed orders in first 3 months

## Risk Assessment

**High Risk**:
- Stripe Connect integration complexity for Ethiopian market
- Real-time messaging scalability with 100+ concurrent users
- Escrow fund management and dispute resolution logic

**Medium Risk**:
- File upload security and storage management
- Email deliverability in Ethiopian context
- Mobile money integration availability through Stripe

**Low Risk**:
- Authentication and authorization (well-established Laravel patterns)
- Database schema design (straightforward relational model)
- UI implementation with Tailwind CSS

## Next Steps

1. ✅ Complete Phase 0: Generate research.md
2. ✅ Complete Phase 1: Generate data-model.md, contracts/, quickstart.md
3. Run `/speckit.tasks` to generate implementation tasks
4. Run `/speckit.implement` to begin development
