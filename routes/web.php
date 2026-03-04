<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\TermsController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\SupportChatController;
use App\Http\Controllers\Admin\SupportChatController as AdminSupportChatController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\PointController as AdminPointController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\InventorySlotController as AdminInventorySlotController;
use App\Http\Controllers\Admin\BookingSettingsController as AdminBookingSettingsController;
use App\Http\Controllers\Admin\VendorController as AdminVendorController;
use App\Http\Controllers\Admin\DispatchAssignmentController as AdminDispatchAssignmentController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\ReviewController as UserReviewController;
use App\Http\Controllers\PointController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\StripeWebhookController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

require __DIR__ . '/auth.php';

// Google OAuth Routes
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google.redirect');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::get('/faq', [FaqController::class, 'index'])->name('faq');
Route::get('/terms', [TermsController::class, 'index'])->name('terms');
Route::view('/cars', 'cars')->name('cars');
Route::view('/tours', 'tours')->name('tours');
Route::view('/membership', 'membership')->name('membership');

// Search Routes (Public)
Route::get('/search/cars', [HomeController::class, 'searchCars'])->name('search.cars');
Route::get('/search/tours', [HomeController::class, 'searchTours'])->name('search.tours');
Route::get('/products/{product:slug}', [HomeController::class, 'show'])->name('products.show');

// Stripe Webhook Route
Route::post('/webhooks/stripe', StripeWebhookController::class)->name('stripe.webhook');

Route::middleware('auth')->get('/dashboard', function () {
    $user = Auth::user();

    if ($user instanceof User && $user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }

    if ($user instanceof User && $user->hasRole('user')) {
        return redirect()->route('user.dashboard');
    }

    return redirect()->route('profile.edit');
})->name('dashboard');

// User Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('role:user')->group(function () {

        // Booking Routes
        Route::get('/booking/book', [BookingController::class, 'create'])->name('booking.create');
        Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');

        // Payment Flow
        Route::get('/booking/payment/{id}', [BookingController::class, 'payment'])->name('booking.payment');
        Route::post('/booking/process/{id}', [BookingController::class, 'processPayment'])->name('booking.process');
        Route::get('/booking/success/{id}', [BookingController::class, 'success'])->name('booking.success');
        Route::get('/booking/invoice/{id}', [BookingController::class, 'downloadInvoice'])->name('booking.invoice');
        Route::get('/booking/voucher/{id}', [BookingController::class, 'voucher'])->name('booking.voucher');
        Route::get('/booking/voucher/{id}/download', [BookingController::class, 'downloadVoucher'])->name('booking.voucher.download');

        // Support Chat
        Route::get('/support/chat', [SupportChatController::class, 'index'])->name('support.chat');
        Route::post('/support/chat/message', [SupportChatController::class, 'store'])->name('support.chat.message');

        // Points Routes
        Route::prefix('points')->name('points.')->group(function () {
            Route::get('/', [PointController::class, 'index'])->name('index');
            Route::get('/history', [PointController::class, 'history'])->name('history');
            Route::get('/balance', [PointController::class, 'balance'])->name('balance');
            Route::post('/calculate', [PointController::class, 'calculate'])->name('calculate');
        });

        // Coupons Routes
        Route::prefix('coupons')->name('coupons.')->group(function () {
            Route::get('/', [CouponController::class, 'index'])->name('index');
            Route::get('/history', [CouponController::class, 'history'])->name('history');
            Route::post('/validate', [CouponController::class, 'validate'])->name('validate');
            Route::get('/available', [CouponController::class, 'available'])->name('available');
        });

        // User Dashboard Routes
        Route::prefix('user')->name('user.')->group(function () {
            Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
            Route::post('/notifications/read-all', [UserDashboardController::class, 'markAllNotificationsRead'])->name('notifications.read-all');
            Route::post('/notifications/{notificationId}/read', [UserDashboardController::class, 'markNotificationRead'])->name('notifications.read');
            Route::post('/membership/subscribe', [UserDashboardController::class, 'subscribeMembership'])->name('membership.subscribe');
            Route::get('/membership/success', [UserDashboardController::class, 'membershipSuccess'])->name('membership.success');

            // My Bookings
            Route::get('/bookings', [UserDashboardController::class, 'bookings'])->name('bookings');
            Route::get('/booking/{id}', [UserDashboardController::class, 'showBooking'])->name('booking.show');
            Route::post('/booking/{id}/cancel', [UserDashboardController::class, 'cancelBooking'])->name('booking.cancel');

            // Points & Coupons
            Route::get('/points', [UserDashboardController::class, 'points'])->name('points');
            Route::get('/coupons', [UserDashboardController::class, 'coupons'])->name('coupons');

            // Profile
            Route::get('/profile', [UserDashboardController::class, 'profile'])->name('profile');
            Route::post('/profile/update', [UserDashboardController::class, 'updateProfile'])->name('profile.update');

            // Reviews & Ratings
            Route::post('/booking/{id}/review', [UserReviewController::class, 'store'])->name('booking.review.store');
        });
    });
});

// Admin Routes
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Booking Routes
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{id}', [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::put('/bookings/{id}', [AdminBookingController::class, 'update'])->name('bookings.update');
    Route::post('/bookings/{id}/refund', [AdminBookingController::class, 'refund'])->name('bookings.refund');
    Route::post('/bookings/{id}/resend-confirmation', [AdminBookingController::class, 'resendConfirmationEmail'])->name('bookings.resend-confirmation');
    Route::get('/bookings/check-in/{token}', [AdminBookingController::class, 'showCheckInByToken'])->middleware('signed')->name('bookings.checkin.show');
    Route::post('/bookings/check-in/{token}', [AdminBookingController::class, 'processCheckInByToken'])->name('bookings.checkin.process');

    // Booking Settings
    Route::get('/settings/checkin-window', [AdminBookingSettingsController::class, 'editCheckInWindow'])->name('settings.checkin-window.edit');
    Route::put('/settings/checkin-window', [AdminBookingSettingsController::class, 'updateCheckInWindow'])->name('settings.checkin-window.update');
    Route::delete('/settings/checkin-window', [AdminBookingSettingsController::class, 'resetCheckInWindow'])->name('settings.checkin-window.reset');

    // Products (Cars & Tours)
    Route::resource('products', AdminProductController::class);

    // Inventory Slots
    Route::get('/inventory-slots', [AdminInventorySlotController::class, 'index'])->name('inventory-slots.index');
    Route::post('/inventory-slots', [AdminInventorySlotController::class, 'store'])->name('inventory-slots.store');
    Route::put('/inventory-slots/{inventorySlot}', [AdminInventorySlotController::class, 'update'])->name('inventory-slots.update');
    Route::delete('/inventory-slots/{inventorySlot}', [AdminInventorySlotController::class, 'destroy'])->name('inventory-slots.destroy');

    // Customers
    Route::resource('customers', AdminCustomerController::class)->parameters([
        'customers' => 'user'
    ]);

    // Vendors
    Route::resource('vendors', AdminVendorController::class)->except(['show']);

    // Dispatch Assignments
    Route::get('/dispatch-assignments', [AdminDispatchAssignmentController::class, 'index'])->name('dispatch-assignments.index');
    Route::post('/dispatch-assignments', [AdminDispatchAssignmentController::class, 'store'])->name('dispatch-assignments.store');
    Route::put('/dispatch-assignments/{dispatchAssignment}', [AdminDispatchAssignmentController::class, 'update'])->name('dispatch-assignments.update');

    // Support Chat
    Route::get('/support/chat', [AdminSupportChatController::class, 'index'])->name('support.chat');
    Route::post('/support/chat/message', [AdminSupportChatController::class, 'store'])->name('support.chat.message');
    Route::delete('/support/chat/{conversation}', [AdminSupportChatController::class, 'destroy'])->name('support.chat.destroy');

    // Points Management
    Route::prefix('points')->name('points.')->group(function () {
        Route::get('/', [AdminPointController::class, 'index'])->name('index');
        Route::get('/membership-stats', [AdminPointController::class, 'membershipStats'])->name('membership-stats');
        Route::get('/user/{user}/adjust', [AdminPointController::class, 'adjustForm'])->name('adjust.form');
        Route::post('/user/{user}/adjust', [AdminPointController::class, 'adjust'])->name('adjust');
        Route::post('/expire', [AdminPointController::class, 'expirePoints'])->name('expire');
    });

    // Coupon Management
    Route::resource('coupons', AdminCouponController::class)->except(['show']);
    Route::post('/coupons/{coupon}/toggle-status', [AdminCouponController::class, 'toggleStatus'])->name('coupons.toggle-status');

    // Reviews Moderation
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::put('/reviews/{review}/moderate', [AdminReviewController::class, 'moderate'])->name('reviews.moderate');
});
