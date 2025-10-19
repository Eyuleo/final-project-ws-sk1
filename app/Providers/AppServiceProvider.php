<?php

namespace App\Providers;

use App\Models\Message;
use App\Models\Order;
use App\Models\ServiceListing;
use App\Policies\MessagePolicy;
use App\Policies\OrderPolicy;
use App\Policies\ServiceListingPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(ServiceListing::class, ServiceListingPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(Message::class, MessagePolicy::class);
        
        // Register message-related abilities
        Gate::define('viewMessages', [MessagePolicy::class, 'viewMessages']);
        Gate::define('sendMessage', [MessagePolicy::class, 'sendMessage']);
        
        // Load broadcast channel routes
        if (file_exists(base_path('routes/channels.php'))) {
            require base_path('routes/channels.php');
        }
    }
}
