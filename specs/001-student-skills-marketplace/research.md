# Technology Research: Student Skills Marketplace

**Date**: 2025-10-18  
**Feature**: Student Skills Marketplace  
**Purpose**: Document technology choices, rationale, and alternatives considered

## Overview

This document captures research findings and technology decisions for the Student Skills Marketplace Laravel application. Each decision includes rationale, alternatives considered, and implementation guidance.

---

## 1. Authentication Scaffolding

### Decision: Laravel Breeze (Blade + Tailwind)

**Rationale**:
- Lightweight and minimal, perfect for traditional server-rendered applications
- Provides complete authentication scaffolding (login, registration, password reset, email verification)
- Uses Blade templates and Tailwind CSS, matching our tech stack requirements
- Easy to customize and extend for role-based registration flows
- No unnecessary complexity (unlike Jetstream's team management features)
- Well-documented and officially maintained by Laravel

**Implementation**:
```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
php artisan migrate
npm install && npm run dev
```

**Alternatives Considered**:
- **Laravel Jetstream**: Rejected because it includes team management, API tokens, and two-factor authentication that we don't need for MVP. Adds unnecessary complexity.
- **Laravel Fortify**: Rejected because it's backend-only and we'd still need to build all frontend views from scratch.
- **Custom Authentication**: Rejected because it violates the "never roll custom auth" security principle and would take significantly more development time.

**Customization Required**:
- Extend registration to capture role (student/client) and role-specific fields
- Add separate registration routes: `/register/student` and `/register/client`
- Customize registration controllers to create associated StudentProfile or ClientProfile records

---

## 2. Authorization & Role Management

### Decision: Spatie Laravel Permission

**Rationale**:
- Industry-standard package with 11k+ stars and active maintenance
- Provides role and permission management with database-backed flexibility
- Supports role assignment, permission checking, and middleware
- Integrates seamlessly with Laravel's authorization system (Gates and Policies)
- Allows dynamic role/permission management through admin interface
- Better than pure Gates/Policies for complex role hierarchies (student, client, admin)
- Cached permission checks for performance

**Implementation**:
```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

**Usage Pattern**:
```php
// Assign role on registration
$user->assignRole('student');

// Middleware
Route::middleware(['role:student'])->group(function () {
    // Student routes
});

// Policy checks
$this->authorize('update', $serviceListing);
```

**Alternatives Considered**:
- **Laravel Gates/Policies Only**: Rejected because managing complex role hierarchies with pure Gates becomes cumbersome. Spatie provides better structure for role-based systems.
- **Custom Role System**: Rejected because it reinvents the wheel and lacks the battle-tested security of Spatie's implementation.
- **Bouncer**: Rejected because Spatie has better documentation, larger community, and more active maintenance.

**Roles to Define**:
- `student`: Can create services, manage orders, withdraw earnings
- `client`: Can browse services, place orders, leave reviews
- `admin`: Can moderate disputes, manage users, view analytics

---

## 3. Payment Processing

### Decision: Stripe + Stripe Connect

**Rationale**:
- **Stripe**: Industry-leading payment processor with excellent developer experience
- **Stripe Connect**: Designed specifically for marketplace platforms with multi-party payments
- Supports escrow-like payment flows (charge customer, hold funds, pay out to provider)
- Handles Ethiopian market through international card payments
- Automatic currency conversion and compliance
- Built-in fraud detection and PCI compliance
- Comprehensive webhooks for payment events
- Laravel Cashier provides Laravel-friendly wrapper (optional)

**Implementation Approach**:
- Use Stripe Checkout for client payments (hosted payment page)
- Use Stripe Connect Express accounts for student payouts
- Implement custom `PaymentService` and `EscrowService` classes
- Store Stripe customer IDs and Connect account IDs in database
- Use webhooks to handle payment events (payment succeeded, payout completed, etc.)

**Payment Flow**:
1. Client places order → Stripe Checkout creates payment intent
2. Payment succeeds → Funds held in platform Stripe account (escrow)
3. Student completes order → Client approves
4. Platform releases funds to student's Stripe Connect account (minus 15% commission)
5. Student requests withdrawal → Stripe Connect transfers to bank account

**Alternatives Considered**:
- **PayPal**: Rejected because Stripe has better developer experience, more flexible marketplace features, and better documentation.
- **Flutterwave**: Rejected because while it has better African market coverage, Stripe Connect's marketplace features are more mature for escrow and split payments.
- **Manual Bank Transfers**: Rejected because it doesn't scale, lacks automation, and creates trust issues.

**Ethiopian Market Considerations**:
- Stripe supports international card payments in Ethiopia
- Mobile money integration depends on Stripe's partnerships (may not be available initially)
- Display prices in USD/EUR with ETB equivalent where possible
- Document payment method limitations clearly to users

**Laravel Cashier Decision**:
- **Use Cashier for subscription features if needed later**
- **Build custom integration for marketplace payments** because Cashier is optimized for subscriptions, not marketplace escrow flows
- Custom implementation gives more control over escrow logic and commission handling

---

## 4. Real-Time Messaging

### Decision: Laravel Echo + Pusher (with fallback to polling)

**Rationale**:
- **Laravel Echo**: Official Laravel package for real-time event broadcasting
- **Pusher**: Managed WebSocket service with generous free tier (100 concurrent connections, 200k messages/day)
- Zero infrastructure management (no WebSocket server to maintain)
- Reliable message delivery with automatic reconnection
- Easy integration with Laravel's broadcasting system
- Scales automatically with platform growth
- Fallback to polling for users with WebSocket issues

**Implementation**:
```bash
composer require pusher/pusher-php-server
npm install --save-dev laravel-echo pusher-js
```

**Broadcasting Setup**:
- Use Laravel's event broadcasting with Pusher driver
- Create `MessageSent` event that broadcasts to private channels
- Use private channels for order-specific conversations
- Implement presence channels for "user is typing" indicators (optional)

**Alternatives Considered**:
- **Laravel WebSockets**: Rejected because it requires self-hosting WebSocket server, adds infrastructure complexity, and needs more DevOps expertise. Good for high-scale but overkill for MVP.
- **Polling (AJAX)**: Rejected as primary solution because it creates poor UX and higher server load. Will use as fallback only.
- **Soketi**: Rejected because it's newer and less battle-tested than Pusher, though it's a good future migration path if costs become an issue.

**Cost Considerations**:
- Pusher free tier: 100 concurrent connections, 200k messages/day
- Sufficient for MVP (target: 50 students, 100 orders)
- Upgrade to paid plan ($49/month) when exceeding free tier
- Future migration to self-hosted Soketi if costs become prohibitive

**Fallback Strategy**:
- Implement polling-based message refresh for users without WebSocket support
- Use `setInterval` to check for new messages every 10 seconds
- Detect WebSocket connection failures and automatically switch to polling

---

## 5. File Storage

### Decision: Local Storage (MVP) → S3-Compatible (Production)

**Rationale**:
- **Local Storage for MVP**: Simplifies initial development, no external dependencies, zero cost
- **S3-Compatible for Production**: Scalable, reliable, CDN integration, automatic backups
- Laravel's filesystem abstraction makes migration seamless (change config, no code changes)
- Use DigitalOcean Spaces or AWS S3 for production (both S3-compatible)

**Implementation**:
```php
// Same code works for local and S3
Storage::disk('public')->put($path, $file);
$url = Storage::disk('public')->url($path);
```

**File Types & Limits**:
- **Portfolio Images**: JPG, PNG, WebP | Max 5MB | Optimize with Intervention Image
- **Deliverable Files**: PDF, DOCX, ZIP, images | Max 10MB
- **Message Attachments**: PDF, images, documents | Max 5MB
- **Profile Pictures**: JPG, PNG | Max 2MB | Generate thumbnails

**Security**:
- Validate file types using MIME type detection (not just extension)
- Scan uploaded files for malware (use ClamAV in production)
- Store files outside public directory for deliverables (use signed URLs)
- Generate unique filenames to prevent overwrites
- Implement rate limiting on file uploads

**Alternatives Considered**:
- **Cloudinary**: Rejected because it's optimized for images only, not general file storage. Good for future image optimization needs.
- **Direct S3 from Start**: Rejected because it adds complexity and cost during MVP development. Easy to migrate later.

**Migration Path**:
```bash
# Production: Install S3 driver
composer require league/flysystem-aws-s3-v3

# Update .env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket
```

---

## 6. Queue Driver

### Decision: Database (MVP) → Redis (Production)

**Rationale**:
- **Database Queue for MVP**: Zero additional infrastructure, works out of the box, sufficient for low volume
- **Redis for Production**: Better performance, supports job prioritization, handles higher throughput
- Laravel queue abstraction makes migration seamless

**Queued Jobs**:
- Email notifications (order placed, order completed, message received)
- SMS notifications (optional, via Twilio or Africa's Talking)
- Escrow fund releases (scheduled job)
- File processing (image optimization, thumbnail generation)
- Withdrawal processing
- Analytics aggregation

**Implementation**:
```bash
# MVP: Database queue
php artisan queue:table
php artisan migrate
php artisan queue:work

# Production: Redis queue
composer require predis/predis
# Update .env: QUEUE_CONNECTION=redis
php artisan queue:work redis
```

**Alternatives Considered**:
- **Sync Driver**: Rejected because it blocks HTTP requests, creating poor UX for email sending and file processing.
- **Amazon SQS**: Rejected because it adds external dependency and cost. Redis is simpler and sufficient.
- **RabbitMQ**: Rejected because it's overkill for our use case and adds infrastructure complexity.

**Queue Monitoring**:
- Use Laravel Horizon for Redis queue monitoring (production)
- Implement failed job handling with retry logic
- Set up alerts for failed jobs (email or Slack notifications)

---

## 7. Image Processing

### Decision: Intervention Image

**Rationale**:
- Most popular Laravel image manipulation library (13k+ stars)
- Supports both GD and Imagick drivers
- Easy to use API for resizing, cropping, watermarking
- Integrates seamlessly with Laravel's filesystem
- Handles common image formats (JPG, PNG, WebP, GIF)

**Implementation**:
```bash
composer require intervention/image
```

**Use Cases**:
- Resize portfolio images to standard dimensions (1200x800)
- Generate thumbnails for service listings (400x300)
- Optimize image file sizes (compress to 80% quality)
- Create profile picture thumbnails (200x200)
- Add watermarks to portfolio samples (optional)

**Processing Strategy**:
- Process images asynchronously using queued jobs
- Store original + optimized versions
- Generate multiple sizes for responsive images
- Use WebP format for better compression (with fallback to JPG)

**Alternatives Considered**:
- **GD Library Directly**: Rejected because Intervention Image provides cleaner API and better error handling.
- **Imagick Extension**: Rejected as sole solution because it requires server extension installation. Intervention Image supports both GD and Imagick.
- **Cloud Image Processing (Cloudinary)**: Rejected for MVP to reduce external dependencies and costs. Good future enhancement.

---

## 8. Email Service

### Decision: Laravel Mail + SMTP (Mailgun or SendGrid)

**Rationale**:
- Laravel's built-in mail system is robust and feature-complete
- Mailgun/SendGrid provide reliable SMTP with good deliverability
- Both have generous free tiers (Mailgun: 5k emails/month, SendGrid: 100 emails/day)
- Support email tracking, analytics, and bounce handling
- Easy to switch between providers (just change .env config)

**Implementation**:
```bash
# .env configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@studentskills.com
MAIL_FROM_NAME="Student Skills Marketplace"
```

**Email Types**:
- **Transactional**: Order confirmations, payment receipts, password resets
- **Notifications**: New messages, order updates, review requests
- **Marketing**: Weekly digest, featured services (future)

**Queue All Emails**:
- Never send emails synchronously (blocks HTTP requests)
- Use `Mail::queue()` or implement Mailable with `ShouldQueue`
- Set appropriate retry logic for failed emails

**Alternatives Considered**:
- **Amazon SES**: Rejected because Mailgun/SendGrid have better developer experience and easier setup.
- **Local SMTP**: Rejected because deliverability is poor and emails often land in spam.
- **Postmark**: Rejected because it's more expensive than Mailgun/SendGrid for our volume.

**Deliverability Best Practices**:
- Set up SPF, DKIM, and DMARC records
- Use verified sending domain
- Implement email verification for user accounts
- Monitor bounce rates and unsubscribe requests
- Avoid spam trigger words in email content

---

## 9. Frontend Asset Management

### Decision: Vite + PNPM

**Rationale**:
- **Vite**: Official Laravel asset bundler (replaced Laravel Mix in Laravel 9+)
- Fast hot module replacement (HMR) for development
- Optimized production builds with code splitting
- Native ES modules support
- **PNPM**: Faster and more disk-efficient than NPM/Yarn
- Strict dependency resolution prevents phantom dependencies
- Workspace support for future monorepo needs

**Implementation**:
```bash
# Install PNPM globally
npm install -g pnpm

# Install dependencies
pnpm install

# Development
pnpm run dev

# Production build
pnpm run build
```

**Frontend Stack**:
- **Tailwind CSS**: Utility-first CSS framework
- **Alpine.js**: Lightweight JavaScript framework for interactivity (optional)
- **Laravel Echo**: Real-time event broadcasting
- **Axios**: HTTP client for AJAX requests

**Tailwind Configuration**:
- Customize color palette (avoid purple/indigo per requirements)
- Use neutral grays, blues, and greens for modern look
- Configure responsive breakpoints
- Add custom components for buttons, forms, cards
- Use JIT mode for faster compilation

**Alternatives Considered**:
- **Laravel Mix (Webpack)**: Rejected because Vite is now the official Laravel standard and is significantly faster.
- **NPM**: Rejected in favor of PNPM for better performance and disk efficiency.
- **Yarn**: Rejected because PNPM is faster and has better workspace support.

---

## 10. Testing Strategy

### Decision: PHPUnit (Unit + Feature Tests) + Laravel Dusk (Critical UI Flows)

**Rationale**:
- **PHPUnit**: Built into Laravel, industry standard for PHP testing
- **Feature Tests**: Test HTTP endpoints, authentication, authorization, database interactions
- **Unit Tests**: Test business logic in services, models, helpers
- **Laravel Dusk**: Browser automation for critical UI flows (optional for MVP)

**Test Coverage Goals**:
- **Unit Tests**: 80%+ coverage for service classes and business logic
- **Feature Tests**: 100% coverage for all routes and controllers
- **Browser Tests**: Critical user journeys (registration, order placement, payment)

**Testing Approach**:
- Use in-memory SQLite for fast test execution
- Use factories for test data generation
- Mock external services (Stripe, Pusher, email)
- Use RefreshDatabase trait to reset database between tests
- Implement parallel test execution for faster CI/CD

**Test Organization**:
```
tests/
├── Feature/
│   ├── Auth/                    # Authentication flows
│   ├── Student/                 # Student features
│   ├── Client/                  # Client features
│   └── Admin/                   # Admin features
├── Unit/
│   ├── Services/                # Business logic
│   └── Models/                  # Model methods
└── Browser/                     # Dusk tests (optional)
```

**Alternatives Considered**:
- **Pest PHP**: Rejected because PHPUnit is more widely known and has better IDE support. Pest is great but adds learning curve.
- **Codeception**: Rejected because PHPUnit + Dusk covers our needs without additional complexity.

---

## 11. Development Environment

### Decision: Laravel Sail (Docker)

**Rationale**:
- Official Laravel Docker development environment
- Consistent environment across team members
- Includes PHP, MySQL, Redis, Mailhog, and other services
- Easy to customize via docker-compose.yml
- No need to install PHP, MySQL, Redis locally
- Works on Windows, macOS, and Linux

**Implementation**:
```bash
# Install Sail
composer require laravel/sail --dev
php artisan sail:install

# Start environment
./vendor/bin/sail up -d

# Run commands
./vendor/bin/sail artisan migrate
./vendor/bin/sail composer install
./vendor/bin/sail npm install
```

**Services Included**:
- PHP 8.2
- MySQL 8.0
- Redis
- Mailhog (email testing)
- Selenium (for Dusk tests)

**Alternatives Considered**:
- **Valet**: Rejected because it's macOS-only and doesn't provide consistent environment across team.
- **Homestead**: Rejected because Sail is lighter and more modern.
- **XAMPP/WAMP**: Rejected because Docker provides better isolation and consistency.
- **Local Installation**: Rejected because it creates "works on my machine" problems.

---

## 12. Code Quality Tools

### Decision: Laravel Pint + PHPStan

**Rationale**:
- **Laravel Pint**: Official Laravel code style fixer based on PHP-CS-Fixer
- Enforces PSR-12 standards automatically
- Zero configuration required (opinionated defaults)
- **PHPStan**: Static analysis tool that catches bugs before runtime
- Enforces type safety and finds potential issues
- Configurable strictness levels (start at level 5, move to level 8)

**Implementation**:
```bash
# Install
composer require laravel/pint --dev
composer require phpstan/phpstan --dev

# Run Pint
./vendor/bin/pint

# Run PHPStan
./vendor/bin/phpstan analyse
```

**CI/CD Integration**:
- Run Pint in check mode (fail if code not formatted)
- Run PHPStan at level 5 minimum
- Run tests with coverage reporting
- Block merge if any check fails

**Alternatives Considered**:
- **PHP-CS-Fixer**: Rejected because Pint is the official Laravel tool and has better defaults.
- **Psalm**: Rejected in favor of PHPStan because PHPStan has better Laravel support and documentation.

---

## 13. Monitoring & Logging

### Decision: Laravel Telescope (Development) + Laravel Log (Production)

**Rationale**:
- **Laravel Telescope**: Debug assistant for development
- Provides insights into requests, exceptions, database queries, jobs, mail, etc.
- Essential for debugging performance issues and N+1 queries
- **Laravel Log**: Built-in logging to files/stack
- Use structured logging with context
- Integrate with external services (Sentry, Bugsnag) for production error tracking

**Implementation**:
```bash
# Install Telescope (dev only)
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

**Production Monitoring**:
- Use Sentry or Bugsnag for error tracking
- Set up log aggregation (Papertrail or Logtail)
- Monitor performance metrics (response times, memory usage)
- Set up uptime monitoring (Pingdom or UptimeRobot)

**Alternatives Considered**:
- **Debugbar**: Rejected because Telescope provides more comprehensive insights.
- **Custom Logging**: Rejected because Laravel's logging is feature-complete.

---

## 14. Deployment Strategy

### Decision: Laravel Forge + DigitalOcean

**Rationale**:
- **Laravel Forge**: Official Laravel deployment platform
- Automates server provisioning, deployment, SSL, queues, cron jobs
- One-click deployments from Git
- Built-in support for zero-downtime deployments
- **DigitalOcean**: Reliable, affordable cloud hosting
- $12/month droplet sufficient for MVP
- Easy to scale vertically (more CPU/RAM) or horizontally (load balancer)

**Server Configuration**:
- Ubuntu 22.04 LTS
- Nginx web server
- PHP 8.2 with OPcache
- MySQL 8.0
- Redis for caching and queues
- Supervisor for queue workers
- SSL certificate via Let's Encrypt

**Deployment Flow**:
1. Push code to Git (GitHub/GitLab)
2. Forge detects push and triggers deployment
3. Forge runs deployment script:
   - Pull latest code
   - Install Composer dependencies
   - Run migrations
   - Build frontend assets
   - Clear caches
   - Reload PHP-FPM
4. Zero downtime achieved via symlink swap

**Alternatives Considered**:
- **Heroku**: Rejected because it's more expensive and less flexible than Forge + DigitalOcean.
- **AWS Elastic Beanstalk**: Rejected because it's overkill for our scale and more complex to manage.
- **Manual Deployment**: Rejected because it's error-prone and doesn't scale.
- **Vapor**: Rejected because serverless adds complexity and cost for traditional Laravel apps.

---

## Summary of Key Decisions

| Area | Decision | Rationale |
|------|----------|-----------|
| **Authentication** | Laravel Breeze (Blade + Tailwind) | Lightweight, matches tech stack, easy to customize |
| **Authorization** | Spatie Laravel Permission | Industry standard, flexible role management |
| **Payments** | Stripe + Stripe Connect | Best marketplace features, Ethiopian market support |
| **Real-time** | Laravel Echo + Pusher | Managed service, zero infrastructure, reliable |
| **File Storage** | Local → S3-compatible | Simple MVP, easy production migration |
| **Queue** | Database → Redis | Zero setup for MVP, scalable for production |
| **Images** | Intervention Image | Popular, easy API, supports multiple drivers |
| **Email** | Laravel Mail + Mailgun | Reliable, good deliverability, generous free tier |
| **Assets** | Vite + PNPM | Official Laravel standard, fast, efficient |
| **Testing** | PHPUnit + Dusk | Built-in, comprehensive, industry standard |
| **Dev Environment** | Laravel Sail | Consistent Docker environment, official tool |
| **Code Quality** | Pint + PHPStan | Official Laravel tools, enforce standards |
| **Deployment** | Forge + DigitalOcean | Automated, reliable, affordable |

---

## Implementation Priority

### Phase 1: Core Infrastructure (Week 1-2)
1. Set up Laravel Sail development environment
2. Install and configure Laravel Breeze
3. Set up Spatie Laravel Permission
4. Configure database migrations for all entities
5. Set up Tailwind CSS with custom configuration

### Phase 2: Payment Integration (Week 3)
1. Integrate Stripe for client payments
2. Set up Stripe Connect for student payouts
3. Implement PaymentService and EscrowService
4. Test payment flows in Stripe test mode

### Phase 3: Core Features (Week 4-6)
1. Student registration and profile management
2. Service listing creation and management
3. Client service discovery and search
4. Order placement and management
5. File upload handling

### Phase 4: Communication (Week 7)
1. Implement messaging system
2. Integrate Laravel Echo + Pusher
3. Set up email notifications

### Phase 5: Reviews & Earnings (Week 8)
1. Review and rating system
2. Earnings dashboard
3. Withdrawal processing

### Phase 6: Admin & Polish (Week 9-10)
1. Admin dashboard
2. Dispute resolution
3. Performance optimization
4. Security hardening
5. Production deployment

---

## Open Questions & Future Research

1. **Mobile Money Integration**: Research Flutterwave or Paystack as supplement to Stripe for better Ethiopian mobile money support
2. **SMS Notifications**: Evaluate Africa's Talking vs. Twilio for Ethiopian SMS delivery
3. **Localization**: Consider adding Amharic language support in future
4. **Advanced Search**: Evaluate Algolia or Meilisearch for better search experience as platform grows
5. **Analytics**: Consider integrating Google Analytics or Plausible for user behavior tracking
6. **CDN**: Evaluate Cloudflare or BunnyCDN for static asset delivery in production

---

**Next Steps**: Proceed to Phase 1 (Design & Contracts) to generate data-model.md, contracts/, and quickstart.md.
