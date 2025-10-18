<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Client\ServiceDiscoveryController;
use App\Http\Controllers\ProfileController;
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

Route::get('/dashboard', function () {
    return view('dashboard');
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
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
