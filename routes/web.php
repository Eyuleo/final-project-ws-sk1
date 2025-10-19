<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Client\OrderController as ClientOrderController;
use App\Http\Controllers\Client\ProfileController as ClientProfileController;
use App\Http\Controllers\Client\ServiceDiscoveryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\Student\OrderController as StudentOrderController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\Student\ServiceListingController;
use App\Services\SearchService;
use Illuminate\Support\Facades\Route;

// Homepage with featured services
Route::get('/', function (SearchService $searchService) {
    $categories = $searchService->getCategories();
    $featuredServices = $searchService->getFeaturedServices(8);
    return view('welcome', compact('categories', 'featuredServices'));
})->name('welcome');

// Public service discovery routes
Route::get('/services', [ServiceDiscoveryController::class, 'index'])->name('services.index');
Route::get('/services/{service}', [ServiceDiscoveryController::class, 'show'])->name('services.show');

// Category routes
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

// Stripe webhook (exclude from CSRF protection in bootstrap/app.php)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])->name('stripe.webhook');

Route::get('/dashboard', function () {
    $user = Auth::user();
    
    return match ($user->role) {
        'student' => redirect()->route('student.dashboard'),
        'client' => redirect()->route('client.dashboard'),
        'admin' => redirect()->route('admin.dashboard'),
        default => redirect()->route('welcome'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

// Student dashboard route
Route::get('/student/dashboard', function () {
    return view('student.dashboard');
})->middleware(['auth', 'student'])->name('student.dashboard');

// Client dashboard route
Route::get('/client/dashboard', function () {
    return view('client.dashboard');
})->middleware(['auth', 'client'])->name('client.dashboard');

// Admin dashboard route
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'admin'])->name('admin.dashboard');

// Public student profile
Route::get('/student/{user}/profile', [StudentProfileController::class, 'publicProfile'])->name('student.profile.public');

// Student profile routes
Route::middleware(['auth', 'student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/profile', [StudentProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [StudentProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [StudentProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/portfolio/{index}', [StudentProfileController::class, 'deletePortfolioFile'])->name('profile.portfolio.delete');
    
    // Service listing routes
    Route::resource('services', ServiceListingController::class);
    Route::patch('/services/{service}/toggle-status', [ServiceListingController::class, 'toggleStatus'])->name('services.toggle-status');
    Route::delete('/services/{service}/samples/{index}', [ServiceListingController::class, 'deleteSample'])->name('services.delete-sample');
    
    // Order management routes
    Route::get('/orders', [StudentOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [StudentOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/accept', [StudentOrderController::class, 'accept'])->name('orders.accept');
    Route::post('/orders/{order}/decline', [StudentOrderController::class, 'decline'])->name('orders.decline');
    Route::post('/orders/{order}/start', [StudentOrderController::class, 'updateStatus'])->name('orders.start');
    Route::post('/orders/{order}/upload', [StudentOrderController::class, 'uploadDeliverables'])->name('orders.upload');
});

// Client routes
Route::middleware(['auth', 'client'])->prefix('client')->name('client.')->group(function () {
    // Profile routes
    Route::get('/profile', [ClientProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ClientProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ClientProfileController::class, 'update'])->name('profile.update');
    
    // Service discovery routes (client-specific views)
    Route::get('/services', [ServiceDiscoveryController::class, 'index'])->name('services.index');
    Route::get('/services/{service}', [ServiceDiscoveryController::class, 'show'])->name('services.show');
    
    // Order placement routes
    Route::get('/orders/create/{service}', [ClientOrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [ClientOrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/success', [ClientOrderController::class, 'success'])->name('orders.success');
    Route::get('/orders/cancel', [ClientOrderController::class, 'cancel'])->name('orders.cancel');
    
    // Order management routes
    Route::get('/orders', [ClientOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [ClientOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/approve', [ClientOrderController::class, 'approve'])->name('orders.approve');
    Route::post('/orders/{order}/revision', [ClientOrderController::class, 'requestRevision'])->name('orders.revision');
    Route::post('/orders/{order}/dispute', [ClientOrderController::class, 'dispute'])->name('orders.dispute');
});

// Generic profile routes (Breeze default) - Replaced by role-specific routes above
// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// Test email route (remove after testing)
Route::get('/test-email', function () {
    try {
        \Illuminate\Support\Facades\Mail::raw('Test email from Laravel', function ($message) {
            $message->to('eyualxprogram@gmail.com')
                ->subject('Test Email');
        });
        return 'Email sent! Check your logs or mail service.';
    } catch (\Exception $e) {
        return 'Email failed: ' . $e->getMessage();
    }
});

require __DIR__.'/auth.php';
