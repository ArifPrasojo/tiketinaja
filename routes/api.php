<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VenueController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PromoController;
use App\Http\Controllers\Api\CustomerDashboardController;
use App\Http\Controllers\Api\NotificationController as ApiNotificationController;
use App\Http\Controllers\Api\CheckInController as ApiCheckInController;

/*
|--------------------------------------------------------------------------
| API Routes – TiketinAja REST API v1
|--------------------------------------------------------------------------
| Authentication : Laravel Sanctum (Bearer token)
| Base URL       : /api
*/

// ── Auth (rate-limited: 10 req/min) ──────────────────────────────────────
Route::prefix('auth')->name('api.auth.')->middleware('throttle:10,1')->group(function () {
    Route::post('login',    [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');
});

// ── Public catalog ────────────────────────────────────────────────────────
Route::get('venues',                      [VenueController::class, 'index'])->name('api.venues.index');
Route::get('venues/{venue}',              [VenueController::class, 'show'])->name('api.venues.show');
Route::get('venues/{venue}/reviews',      [VenueController::class, 'reviews'])->name('api.venues.reviews');
Route::get('courts/{court}/availability', [VenueController::class, 'availability'])->name('api.courts.availability');
Route::get('sports',                      [VenueController::class, 'sports'])->name('api.sports');
Route::get('cities',                      [VenueController::class, 'cities'])->name('api.cities');

// ── Authenticated Customer & Manager (Sanctum) ────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth & Profile
    Route::post('auth/logout',    [AuthController::class, 'logout'])->name('api.auth.logout');
    Route::get('auth/me',         [AuthController::class, 'me'])->name('api.auth.me');
    Route::put('auth/me',         [AuthController::class, 'updateProfile'])->name('api.auth.update');
    Route::put('auth/password',   [AuthController::class, 'updatePassword'])->name('api.auth.password');
    Route::delete('auth/me',      [AuthController::class, 'deleteAccount'])->name('api.auth.delete');

    // Customer Home / Dashboard (Mobile App Home Screen)
    Route::get('customer/dashboard', [CustomerDashboardController::class, 'index'])->name('api.customer.dashboard');

    // Bookings & Payments
    Route::get('bookings',                                   [BookingController::class, 'index'])->name('api.bookings.index');
    Route::post('bookings',                                  [BookingController::class, 'store'])->name('api.bookings.store');
    Route::get('bookings/{booking}',                         [BookingController::class, 'show'])->name('api.bookings.show');
    Route::post('bookings/{booking}/pay',                    [BookingController::class, 'pay'])->name('api.bookings.pay');
    Route::post('bookings/{booking}/payment-token',          [BookingController::class, 'paymentToken'])->name('api.bookings.payment-token');
    Route::post('bookings/{booking}/payment-finish',         [BookingController::class, 'finishPayment'])->name('api.bookings.payment-finish');
    Route::post('bookings/{booking}/cancel',                 [BookingController::class, 'cancel'])->name('api.bookings.cancel');

    // Reviews
    Route::post('bookings/{booking}/reviews',                [BookingController::class, 'storeReview'])->name('api.bookings.reviews.store');
    Route::delete('bookings/{booking}/reviews',              [BookingController::class, 'destroyReview'])->name('api.bookings.reviews.destroy');

    // Tickets & QR Check-in
    Route::get('bookings/{booking}/ticket',                  [ApiCheckInController::class, 'ticket'])->name('api.bookings.ticket');
    Route::post('bookings/{booking}/checkin',                [ApiCheckInController::class, 'store'])->name('api.bookings.checkin');

    // Promos
    Route::get('promos',                                     [PromoController::class, 'index'])->name('api.promos.index');
    Route::post('promos/validate',                           [PromoController::class, 'validate'])->name('api.promos.validate');

    // Check-in scanner (manager/admin)
    Route::post('checkin/verify',                            [ApiCheckInController::class, 'verify'])->name('api.checkin.verify');

    // Notifications
    Route::get('notifications',                              [ApiNotificationController::class, 'index'])->name('api.notifications.index');
    Route::get('notifications/unread-count',                 [ApiNotificationController::class, 'unreadCount'])->name('api.notifications.unread');
    Route::post('notifications/{id}/read',                   [ApiNotificationController::class, 'markRead'])->name('api.notifications.read');
    Route::post('notifications/read-all',                    [ApiNotificationController::class, 'readAll'])->name('api.notifications.readAll');
});

