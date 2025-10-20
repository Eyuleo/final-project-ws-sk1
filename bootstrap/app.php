<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'student' => \App\Http\Middleware\EnsureUserIsStudent::class,
            'client' => \App\Http\Middleware\EnsureUserIsClient::class,
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        ]);
        
        // Exclude Stripe webhook from CSRF protection
        $middleware->validateCsrfTokens(except: [
            'stripe/webhook',
        ]);
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Auto-release escrow funds for completed orders after 7 days
        $schedule->job(new \App\Jobs\ReleaseEscrowFunds())
            ->daily()
            ->at('03:00')
            ->timezone('UTC')
            ->name('release-escrow-funds')
            ->withoutOverlapping();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
