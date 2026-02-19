<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\GiftCertificateController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

// ─── Auth ───────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

// ─── Public ──────────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('/privacy', fn() => view('pages.privacy'))->name('privacy');
Route::get('/terms',   fn() => view('pages.terms'))->name('terms');
Route::get('/inn',     fn() => view('pages.inn'))->name('inn');

// ─── Gift Certificates ───────────────────────────────────────────────────────
Route::get('/certificates', [GiftCertificateController::class, 'index'])->name('certificates.index');
Route::middleware('auth')->group(function () {
    Route::post('/certificates', [GiftCertificateController::class, 'store'])->name('certificates.store');
});

// ─── Orders ──────────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/order',             [OrderController::class, 'create'])->name('orders.create');
    Route::post('/order',            [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}',    [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/comment', [OrderController::class, 'updateComment'])->name('orders.comment');
    Route::post('/orders/{order}/select',   [OrderController::class, 'selectVersion'])->name('orders.select');
    Route::post('/orders/{order}/review',   [OrderController::class, 'submitReview'])->name('orders.review');
    Route::post('/orders/{order}/upgrade',      [OrderController::class, 'upgrade'])->name('orders.upgrade');
    Route::post('/orders/{order}/edit-request', [OrderController::class, 'requestEdit'])->name('orders.edit-request');

    // Chat
    Route::post('/orders/{order}/chat', [ChatController::class, 'store'])->name('chat.store');
});

// ─── Profile ─────────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile',                  [ProfileController::class, 'index'])->name('profile');
    Route::patch('/profile',                [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password',         [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile',               [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ─── Payments (YooKassa webhook) ─────────────────────────────────────────────
Route::post('/payments/webhook', [\App\Http\Controllers\PaymentController::class, 'webhook'])
    ->name('payments.webhook')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

Route::get('/payments/success', [\App\Http\Controllers\PaymentController::class, 'success'])->name('payments.success');
Route::get('/payments/cancel', [\App\Http\Controllers\PaymentController::class, 'cancel'])->name('payments.cancel');
