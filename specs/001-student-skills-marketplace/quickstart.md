# Quickstart Guide: Student Skills Marketplace

**Date**: 2025-10-18  
**Feature**: Student Skills Marketplace

## Overview

This guide will help you set up the Student Skills Marketplace Laravel application for local development using Laravel Sail (Docker).

---

## Prerequisites

- **Windows 10/11** with WSL2 enabled
- **Docker Desktop** installed and running
- **Git** installed
- **PNPM** installed globally (`npm install -g pnpm`)
- **Composer** installed (for initial setup)

---

## Initial Setup

### 1. Clone Repository

```bash
git clone <repository-url> student-skills-marketplace
cd student-skills-marketplace
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Environment Variables

Edit `.env` file with the following settings:

```env
APP_NAME="Student Skills Marketplace"
APP_ENV=local
APP_KEY=<generated-key>
APP_DEBUG=true
APP_URL=http://localhost

# Database (Sail defaults)
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=student_skills_marketplace
DB_USERNAME=sail
DB_PASSWORD=password

# Redis (Sail defaults)
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail (Mailhog for local testing)
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@studentskills.test"
MAIL_FROM_NAME="${APP_NAME}"

# Queue
QUEUE_CONNECTION=database

# Stripe (Test Mode)
STRIPE_KEY=pk_test_your_key_here
STRIPE_SECRET=sk_test_your_secret_here
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret_here

# Pusher (for real-time messaging)
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1

# Filesystem
FILESYSTEM_DISK=public
```

### 5. Install Laravel Sail

```bash
php artisan sail:install

# Select services: mysql, redis, mailhog, selenium (optional)
```

### 6. Start Docker Environment

```bash
# Start Sail
./vendor/bin/sail up -d

# Create alias for convenience (optional)
alias sail='./vendor/bin/sail'
```

### 7. Run Database Migrations

```bash
sail artisan migrate
```

### 8. Seed Database

```bash
sail artisan db:seed
```

### 9. Install Frontend Dependencies

```bash
sail pnpm install
```

### 10. Build Frontend Assets

```bash
# Development mode with hot reload
sail pnpm run dev

# Or in a separate terminal for background
sail pnpm run dev &
```

### 11. Create Storage Symlink

```bash
sail artisan storage:link
```

---

## Accessing the Application

- **Application**: http://localhost
- **Mailhog** (Email testing): http://localhost:8025
- **MySQL**: localhost:3306 (use TablePlus, DBeaver, or MySQL Workbench)
- **Redis**: localhost:6379

---

## Install Laravel Breeze

```bash
# Install Breeze
sail composer require laravel/breeze --dev

# Install Blade stack with Tailwind
sail artisan breeze:install blade

# Install dependencies and build
sail pnpm install
sail pnpm run build

# Run migrations (if not already run)
sail artisan migrate
```

---

## Install Additional Packages

```bash
# Spatie Laravel Permission
sail composer require spatie/laravel-permission
sail artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
sail artisan migrate

# Intervention Image
sail composer require intervention/image

# Laravel Telescope (development only)
sail composer require laravel/telescope --dev
sail artisan telescope:install
sail artisan migrate
```

---

## Configure Stripe

### 1. Create Stripe Account

- Sign up at https://stripe.com
- Get test API keys from Dashboard > Developers > API keys

### 2. Install Stripe CLI (for webhook testing)

```bash
# Download Stripe CLI for Windows
# https://github.com/stripe/stripe-cli/releases

# Login to Stripe
stripe login

# Forward webhooks to local application
stripe listen --forward-to localhost/stripe/webhook
```

### 3. Update .env with Stripe Keys

```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_... (from stripe listen output)
```

---

## Configure Pusher

### 1. Create Pusher Account

- Sign up at https://pusher.com
- Create new app (Channels product)
- Get credentials from App Keys tab

### 2. Update .env with Pusher Credentials

```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1
```

### 3. Install Pusher PHP SDK

```bash
sail composer require pusher/pusher-php-server
```

### 4. Install Laravel Echo and Pusher JS

```bash
sail pnpm add laravel-echo pusher-js
```

### 5. Configure Broadcasting

Edit `resources/js/bootstrap.js`:

```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});
```

Add to `.env`:

```env
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

---

## Seed Test Data

### 1. Create Seeders

```bash
sail artisan make:seeder CategorySeeder
sail artisan make:seeder TestUserSeeder
```

### 2. Run Seeders

```bash
sail artisan db:seed --class=CategorySeeder
sail artisan db:seed --class=TestUserSeeder
```

### 3. Create Test Users

Add to `DatabaseSeeder.php`:

```php
public function run()
{
    // Create admin user
    $admin = User::create([
        'name' => 'Admin User',
        'email' => 'admin@test.com',
        'password' => bcrypt('password'),
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);

    // Create student user
    $student = User::create([
        'name' => 'John Student',
        'email' => 'student@test.com',
        'password' => bcrypt('password'),
        'role' => 'student',
        'email_verified_at' => now(),
    ]);

    StudentProfile::create([
        'user_id' => $student->id,
        'university' => 'Addis Ababa University',
        'bio' => 'Experienced web developer and designer',
        'skills' => ['Web Development', 'UI/UX Design', 'Laravel'],
    ]);

    // Create client user
    $client = User::create([
        'name' => 'Jane Client',
        'email' => 'client@test.com',
        'password' => bcrypt('password'),
        'role' => 'client',
        'email_verified_at' => now(),
    ]);

    ClientProfile::create([
        'user_id' => $client->id,
        'organization' => 'Tech Startup Inc',
    ]);
}
```

Run seeder:

```bash
sail artisan db:seed
```

---

## Running Queue Workers

```bash
# Start queue worker
sail artisan queue:work

# Or run in background
sail artisan queue:work --daemon
```

---

## Running Tests

```bash
# Run all tests
sail artisan test

# Run specific test file
sail artisan test tests/Feature/Student/ServiceListingTest.php

# Run with coverage
sail artisan test --coverage

# Run parallel tests (faster)
sail artisan test --parallel
```

---

## Code Quality Tools

### Laravel Pint (Code Formatting)

```bash
# Check code style
sail ./vendor/bin/pint --test

# Fix code style
sail ./vendor/bin/pint
```

### PHPStan (Static Analysis)

```bash
# Install PHPStan
sail composer require phpstan/phpstan --dev

# Create phpstan.neon configuration
# Run analysis
sail ./vendor/bin/phpstan analyse
```

---

## Common Commands

### Artisan Commands

```bash
# Clear all caches
sail artisan optimize:clear

# Cache config
sail artisan config:cache

# Cache routes
sail artisan route:cache

# Generate IDE helper files
sail composer require --dev barryvdh/laravel-ide-helper
sail artisan ide-helper:generate
sail artisan ide-helper:models
```

### Database Commands

```bash
# Fresh migration (drop all tables and re-migrate)
sail artisan migrate:fresh

# Fresh migration with seeding
sail artisan migrate:fresh --seed

# Rollback last migration
sail artisan migrate:rollback

# Create new migration
sail artisan make:migration create_table_name
```

### Make Commands

```bash
# Create controller
sail artisan make:controller Student/ServiceController

# Create model with migration and factory
sail artisan make:model ServiceListing -mf

# Create form request
sail artisan make:request Student/StoreServiceRequest

# Create service class
sail artisan make:class Services/OrderService

# Create job
sail artisan make:job SendOrderNotification

# Create event
sail artisan make:event OrderCreated

# Create listener
sail artisan make:listener SendOrderNotification --event=OrderCreated

# Create policy
sail artisan make:policy ServiceListingPolicy --model=ServiceListing
```

---

## Troubleshooting

### Port Already in Use

```bash
# Stop Sail
sail down

# Check what's using port 80
netstat -ano | findstr :80

# Kill process or change APP_PORT in .env
APP_PORT=8000
```

### Permission Issues

```bash
# Fix storage permissions
sail artisan storage:link
sail chmod -R 775 storage bootstrap/cache
```

### Database Connection Issues

```bash
# Restart MySQL container
sail down
sail up -d

# Check MySQL logs
sail logs mysql
```

### Frontend Not Updating

```bash
# Clear Vite cache
sail pnpm run build
rm -rf node_modules/.vite

# Restart dev server
sail pnpm run dev
```

### Queue Jobs Not Processing

```bash
# Restart queue worker
sail artisan queue:restart

# Check failed jobs
sail artisan queue:failed

# Retry failed jobs
sail artisan queue:retry all
```

---

## Development Workflow

### 1. Create Feature Branch

```bash
git checkout -b feature/001-student-registration
```

### 2. Write Tests First (TDD)

```bash
sail artisan make:test Feature/Student/RegistrationTest
```

### 3. Implement Feature

- Create migrations
- Create models
- Create controllers
- Create form requests
- Create views
- Create service classes

### 4. Run Tests

```bash
sail artisan test
```

### 5. Check Code Quality

```bash
sail ./vendor/bin/pint
sail ./vendor/bin/phpstan analyse
```

### 6. Commit Changes

```bash
git add .
git commit -m "feat: implement student registration"
```

### 7. Push and Create PR

```bash
git push origin feature/001-student-registration
```

---

## Production Deployment Checklist

- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Generate new `APP_KEY`
- [ ] Configure production database
- [ ] Set up Redis for caching and queues
- [ ] Configure production mail service (Mailgun/SendGrid)
- [ ] Set up Stripe production keys
- [ ] Configure S3 for file storage
- [ ] Set up SSL certificate
- [ ] Configure queue workers with Supervisor
- [ ] Set up cron jobs for scheduled tasks
- [ ] Enable OPcache
- [ ] Set up error tracking (Sentry/Bugsnag)
- [ ] Configure backups
- [ ] Set up monitoring and alerts

---

## Useful Resources

- **Laravel Documentation**: https://laravel.com/docs
- **Laravel Sail**: https://laravel.com/docs/sail
- **Laravel Breeze**: https://laravel.com/docs/starter-kits#breeze
- **Spatie Permission**: https://spatie.be/docs/laravel-permission
- **Stripe Documentation**: https://stripe.com/docs
- **Pusher Documentation**: https://pusher.com/docs
- **Tailwind CSS**: https://tailwindcss.com/docs

---

## Support

For issues or questions:
1. Check documentation in `specs/001-student-skills-marketplace/`
2. Review Laravel logs: `storage/logs/laravel.log`
3. Check Docker logs: `sail logs`
4. Consult team members or create GitHub issue

---

**Next Steps**: Run `/speckit.tasks` to generate implementation tasks.
