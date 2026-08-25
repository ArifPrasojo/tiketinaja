<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\CourtController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\Manage\VenueController as ManageVenueController;
use App\Http\Controllers\Manage\CourtController as ManageCourtController;
use App\Http\Controllers\Manage\BookingManageController;
use App\Http\Controllers\Manage\OperatingHourController;
use App\Http\Controllers\Manage\PromoController;
use App\Http\Controllers\Admin\UserController;

// ──────────────────────────────────────────
// Public Catalog
// ──────────────────────────────────────────
Route::get('/', [VenueController::class, 'index'])->name('home');
Route::get('/venues', [VenueController::class, 'index'])->name('venues.index');
Route::get('/venues/{venue:slug}', [VenueController::class, 'show'])->name('venues.show');
Route::get('/courts/{court}', [CourtController::class, 'show'])->name('courts.show');
Route::get('/courts/{court}/availability', [CourtController::class, 'availability'])->name('courts.availability');

// Promo validation endpoint (used by booking form JS)
Route::post('/promos/validate', [\App\Http\Controllers\PromoValidateController::class, 'validate'])
    ->middleware('auth')
    ->name('promos.validate');

// ──────────────────────────────────────────
// Auth Required
// ──────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Customer Bookings
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/pay', [BookingController::class, 'pay'])->name('bookings.pay');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

    // Reviews
    Route::post('/bookings/{booking}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'readAll'])->name('notifications.readAll');

    // QR Check-in (customer view)
    Route::get('/checkin/{booking}', [CheckInController::class, 'show'])->name('checkin.show');
    Route::post('/checkin/{booking}', [CheckInController::class, 'store'])->name('checkin.store');
});

// ──────────────────────────────────────────
// Manage (Manager + Admin)
// ──────────────────────────────────────────
Route::middleware(['auth', 'role:manager,admin'])->prefix('manage')->name('manage.')->group(function () {
    // Venues
    Route::resource('venues', ManageVenueController::class)->except('show');
    Route::put('venues/{venue}/operating-hours', [OperatingHourController::class, 'update'])->name('venues.operating-hours.update');

    // Courts (nested under venue)
    Route::get('venues/{venue}/courts/create', [ManageCourtController::class, 'create'])->name('courts.create');
    Route::post('venues/{venue}/courts', [ManageCourtController::class, 'store'])->name('courts.store');
    Route::get('courts/{court}/edit', [ManageCourtController::class, 'edit'])->name('courts.edit');
    Route::put('courts/{court}', [ManageCourtController::class, 'update'])->name('courts.update');
    Route::delete('courts/{court}', [ManageCourtController::class, 'destroy'])->name('courts.destroy');

    // Bookings
    Route::get('bookings', [BookingManageController::class, 'index'])->name('bookings.index');
    Route::get('bookings/{booking}', [BookingManageController::class, 'show'])->name('bookings.show');
    Route::post('bookings/{booking}/confirm', [BookingManageController::class, 'confirm'])->name('bookings.confirm');
    Route::post('bookings/{booking}/cancel', [BookingManageController::class, 'cancel'])->name('bookings.cancel');

    // Promos
    Route::resource('promos', PromoController::class)->except('show');

    // QR Check-in scanner (manager)
    Route::get('checkin', [CheckInController::class, 'index'])->name('checkin.index');
    Route::post('checkin/verify', [CheckInController::class, 'verify'])->name('checkin.verify');
});

// ──────────────────────────────────────────
// Admin Only
// ──────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

require __DIR__ . '/auth.php';
