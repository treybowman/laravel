<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\VenueController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/listings', [ListingController::class, 'index'])->name('listings.index');
Route::get('/listings/{listing}', [ListingController::class, 'show'])->name('listings.show');
Route::get('/venues', [VenueController::class, 'index'])->name('venues.index');
Route::get('/venues/{venue:slug}', [VenueController::class, 'show'])->name('venues.show');
Route::get('/venues/{venue:slug}/events/{event:slug}', [EventController::class, 'show'])->name('events.show');
Route::get('/teams/{team}', [TeamController::class, 'show'])->name('teams.show');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/u/{username}', [ProfileController::class, 'show'])->name('profile.show');
Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');

// Auth required routes
Route::middleware(['auth', 'verified', 'check.banned'])->group(function () {
    // Dashboard
    Route::get('/dashboard/listings', [DashboardController::class, 'listings'])->name('dashboard.listings');

    // Listings CRUD
    Route::get('/listings/create', [ListingController::class, 'create'])->name('listings.create')->middleware('check.subscription');
    Route::post('/listings', [ListingController::class, 'store'])->name('listings.store')->middleware('check.subscription');
    Route::get('/listings/{listing}/edit', [ListingController::class, 'edit'])->name('listings.edit');
    Route::put('/listings/{listing}', [ListingController::class, 'update'])->name('listings.update');
    Route::delete('/listings/{listing}', [ListingController::class, 'destroy'])->name('listings.destroy');
    Route::post('/listings/{listing}/sold', [ListingController::class, 'markSold'])->name('listings.sold');
    Route::post('/listings/{listing}/relist', [ListingController::class, 'relist'])->name('listings.relist');

    // Start conversation from listing
    Route::post('/listings/{listing}/contact', [MessageController::class, 'startConversation'])->name('listings.contact');

    // Messages
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{conversation}', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/api/messages/unread', [MessageController::class, 'unreadCount'])->name('messages.unread');

    // Feedback
    Route::get('/feedback/{token}', [FeedbackController::class, 'show'])->name('feedback.show');
    Route::post('/feedback/{token}', [FeedbackController::class, 'store'])->name('feedback.store');

    // Credits
    Route::get('/settings/credits', [CreditController::class, 'index'])->name('credits.index');
    Route::post('/credits/redeem', [CreditController::class, 'redeem'])->name('credits.redeem');
    Route::post('/credits/purchase', [CreditController::class, 'purchase'])->name('credits.purchase');

    // Billing
    Route::get('/settings/billing', [BillingController::class, 'index'])->name('billing.index');

    // Ban redirect
    Route::get('/banned', function () {
        return view('errors.banned');
    })->name('banned')->withoutMiddleware(['check.banned']);
});

// Auth routes (Breeze)
require __DIR__.'/auth.php';

// Static pages - MUST be last (wildcard route)
Route::get('/{slug}', [PageController::class, 'show'])->name('pages.show');
