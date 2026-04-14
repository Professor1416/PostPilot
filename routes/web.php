<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GenerateController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

// ─── Auth ─────────────────────────────────────────────────────────────────────
Route::get('/login', fn() => view('auth.login'))->name('login');

Route::get('/auth/google',          [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::post('/logout',              [SocialiteController::class, 'logout'])->name('logout');

// ─── Authenticated Routes ─────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Root redirect
    Route::get('/', fn() => redirect()->route('dashboard.calendar'));

    // Dashboard pages
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/calendar', [DashboardController::class, 'calendar'])->name('calendar');
        Route::get('/queue',    [DashboardController::class, 'queue'])->name('queue');
        Route::get('/accounts', [DashboardController::class, 'accounts'])->name('accounts');
        Route::get('/pricing',  [DashboardController::class, 'pricing'])->name('pricing');
    });

    // Calendar JSON API (called by JavaScript)
    Route::get('/api/calendar', [DashboardController::class, 'calendarData'])->name('api.calendar');

    // Onboarding
    Route::post('/onboarding/complete', [DashboardController::class, 'completeOnboarding'])->name('onboarding.complete');

    // ─── Instagram OAuth ──────────────────────────────────────────────────────
    Route::get('/auth/instagram',          [AccountController::class, 'redirectToInstagram'])->name('auth.instagram');
    Route::get('/auth/instagram/callback', [AccountController::class, 'handleInstagramCallback'])->name('auth.instagram.callback');
    Route::delete('/accounts/{account}',   [AccountController::class, 'disconnect'])->name('accounts.disconnect');

    // ─── Posts ────────────────────────────────────────────────────────────────
    Route::post('/posts',                     [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}',               [PostController::class, 'show'])->name('posts.show');
    Route::put('/posts/{post}',               [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}',            [PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{post}/reschedule',   [PostController::class, 'reschedule'])->name('posts.reschedule');

    // ─── AI Generation ────────────────────────────────────────────────────────
    Route::post('/generate',  [GenerateController::class, 'generate'])->name('generate');
    Route::get('/festivals',  [GenerateController::class, 'festivals'])->name('festivals');

    // ─── Payments ─────────────────────────────────────────────────────────────
    Route::post('/payment/create-order', [PaymentController::class, 'createOrder'])->name('payment.create-order');
    Route::post('/payment/verify',       [PaymentController::class, 'verifyPayment'])->name('payment.verify');
});

// ─── Admin ────────────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', fn() => view('admin.login'))->name('login');
    Route::post('/login', function () {
        $key = request('admin_key');
        if ($key === config('app.admin_secret_key')) {
            session(['admin_key' => $key]);
            return redirect()->route('admin.dashboard');
        }
        return back()->withErrors(['admin_key' => 'Invalid admin key.']);
    })->name('login.post');

    Route::middleware(\App\Http\Middleware\AdminAuth::class)->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    });
});
