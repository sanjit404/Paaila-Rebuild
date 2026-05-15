<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TourPackageController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\TrekRatingController;
use App\Http\Controllers\ProfileController;



Route::get('/', [TourPackageController::class, 'index'])->name('home');

Route::get('/tours/{package}', [TourPackageController::class, 'show'])->name('tours.show');
Route::get('/api/tours/{package}/route', [TourPackageController::class, 'routeData'])->name('tours.route');

Route::get('/track', [TrackingController::class, 'pinEntry'])->name('tracking.pin.entry');
Route::post('/track/verify', [TrackingController::class, 'verifyPin'])->name('tracking.verify.pin');
Route::get('/track/{booking}', [TrackingController::class, 'parent'])->name('tracking.parent');
Route::get('/api/track/{booking}/location', [TrackingController::class, 'getCurrentLocation'])->name('tracking.location');

Route::get('/feed', [PostController::class, 'index'])->name('feed.index');
Route::get('/feed/{post}', [PostController::class, 'show'])->name('feed.show');
Route::post('/feed/{post}/like', [PostController::class, 'like'])->name('feed.like');

Route::get('/payment/esewa/success', [PaymentController::class, 'esewaSuccess'])->name('payment.esewa.success');
Route::get('/payment/esewa/failure', [PaymentController::class, 'esewaFailure'])->name('payment.esewa.failure');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect('/');
    })->name('dashboard');


    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::get('/foryou', [TourPackageController::class, 'foryou'])->name('tour.foryou');

    Route::get('/bookings/create/{package}', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/start', [BookingController::class, 'start'])->name('bookings.start');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

    Route::get('/payment/test/{booking}', [PaymentController::class, 'testMode'])->name('payment.test');
    Route::post('/payment/test/{booking}/process', [PaymentController::class, 'testProcess'])->name('payment.test.process');

    Route::get('/payment/esewa/{booking}', [PaymentController::class, 'esewa'])->name('payment.esewa');

    Route::get('/payment/khalti/{booking}', [PaymentController::class, 'khalti'])->name('payment.khalti');
    Route::post('/payment/khalti/{booking}/verify', [PaymentController::class, 'khaltiVerify'])->name('payment.khalti.verify');

    Route::get('/payment/stripe/{booking}', [PaymentController::class, 'stripe'])->name('payment.stripe');
    Route::post('/payment/stripe/{booking}/process', [PaymentController::class, 'stripeProcess'])->name('payment.stripe.process');

    Route::get('/my-tour/{booking}', [TrackingController::class, 'traveler'])->name('tracking.traveler');
    Route::post('/api/tracking/{booking}/location', [TrackingController::class, 'updateLocation'])->name('tracking.update');
    Route::post('/api/tracking/{booking}/facts-viewed/{checkpoint}', [TrackingController::class, 'markFactsViewed'])->name('tracking.facts.viewed');

    Route::get('/preferences', [PreferenceController::class, 'create'])->name('preferences.create');
    Route::post('/preferences', [PreferenceController::class, 'store'])->name('preferences.store');
    Route::get('/preferences/edit',  [PreferenceController::class, 'edit'])->name('preferences.edit');
    Route::put('/preferences/edit',  [PreferenceController::class, 'update'])->name('preferences.update');

    Route::post('/bookings/{booking}/rate', [TrekRatingController::class, 'store'])->name('trek.rate');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/packages', [AdminController::class, 'packages'])->name('packages');
    Route::get('/packages/create', [AdminController::class, 'createPackage'])->name('packages.create');
    Route::post('/packages', [AdminController::class, 'storePackage'])->name('packages.store');
    Route::get('/packages/{package}/edit', [AdminController::class, 'editPackage'])->name('packages.edit');
    Route::put('/packages/{package}', [AdminController::class, 'updatePackage'])->name('packages.update');
    Route::delete('/packages/{package}', [AdminController::class, 'deletePackage'])->name('packages.delete');
    Route::patch('/packages/{package}/toggle-status', [AdminController::class, 'togglePackageStatus'])->name('packages.toggle-status');
    Route::post('/packages/{package}/duplicate', [AdminController::class, 'duplicatePackage'])->name('packages.duplicate');

    Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings');
    Route::get('/bookings/{booking}', [AdminController::class, 'viewBooking'])->name('bookings.show');
    Route::post('/bookings/{booking}/confirm', [AdminController::class, 'confirmBooking'])->name('bookings.confirm');
    Route::post('/bookings/{booking}/complete', [AdminController::class, 'completeBooking'])->name('bookings.complete');
    Route::post('/bookings/{booking}/cancel', [AdminController::class, 'cancelBooking'])->name('bookings.cancel');

    Route::post('/packages/{package}/checkpoints', [AdminController::class, 'addCheckpoint'])->name('checkpoints.add');
    Route::put('/checkpoints/{checkpoint}', [AdminController::class, 'updateCheckpoint'])->name('checkpoints.update');
    Route::delete('/checkpoints/{checkpoint}', [AdminController::class, 'deleteCheckpoint'])->name('checkpoints.delete');

    Route::post('/checkpoints/{checkpoint}/facts', [AdminController::class, 'addFact'])->name('facts.add');
    Route::put('/facts/{fact}', [AdminController::class, 'updateFact'])->name('facts.update');
    Route::delete('/facts/{fact}', [AdminController::class, 'deleteFact'])->name('facts.delete');

    Route::get('/posts', [AdminController::class, 'Posts'])->name('posts');
    Route::get('/posts/create', [AdminController::class, 'createPost'])->name('posts.create');
    Route::post('/posts', [AdminController::class, 'storePost'])->name('posts.store');
    Route::get('/posts/{post}/edit', [AdminController::class, 'editPost'])->name('posts.edit');
    Route::put('/posts/{post}', [AdminController::class, 'updatePost'])->name('posts.update');
    Route::delete('/posts/{post}', [AdminController::class, 'deletePost'])->name('posts.delete');
});

require __DIR__ . '/auth.php';