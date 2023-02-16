<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\SubscribeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebsitesController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware(['api', 'check.frontend'])->group(function () {
    Route::get('/homepagetesti', [MainController::class, 'getTestimonialsFounders'])->name('home');
    Route::get('/blogs', [MainController::class, 'getBlogs'])->name('blogs');
    Route::get('/websites', [WebsitesController::class, 'index'])->name('websites_home');
    Route::post('/websites/create', [WebsitesController::class, 'store'])->name('create');
    Route::get('/website/{token}', [WebsitesController::class, 'show'])->name('delete');
    Route::delete('/website/delete/{token}', [WebsitesController::class, 'delete'])->name('delete');
    Route::post('/recent/websites', [WebsitesController::class, 'recent_websites'])->name('recent');
    Route::post('/orders', [OrdersController::class, 'orders_all'])->name('orders');
    Route::post('/orders/create', [OrdersController::class, 'create_order'])->name('create_order');
    Route::post('/order', [OrdersController::class, 'order_show'])->name('show_order');
    Route::post('/checkout', [CheckoutController::class, 'paymecntCheck'])->name('checkout');
    Route::post('/subscribe', [SubscribeController::class, 'subscribe'])->name('subscribe');
    Route::post('/signin', [UserController::class, 'login'])->name('login');
    Route::post('/signup', [UserController::class, 'register'])->name('register');
    Route::post('/user', [UserController::class, 'profile'])->name('profile');
    Route::post('/user/update', [UserController::class, 'update'])->name('update');
    Route::post('/user/update/avatar', [UserController::class, 'updateAvatar'])->name('updateAvatar');
    Route::post('/user/delete', [UserController::class, 'delete'])->name('delete');
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    Route::post('/chat/message', [ChatController::class, 'sendMessage'])->name('sendMessage');
    Route::post('/chat/messages', [ChatController::class, 'messages'])->name('messages');
    Route::post('/message/contact', [ContactController::class, 'store_message'])->name('message.contact');
    Route::post('/admin/check', [UserController::class, 'checkAdmin'])->name('admin.check');
});

Route::get('/auth', [AuthController::class, 'redirectToAuth']);
Route::get('/auth/callback', [AuthController::class, 'handleAuthCallBack']);
Route::post('/email/verify', [UserController::class, 'verifyEmailSign'])->name('verify_email');
Route::post('/email/verification', [UserController::class, 'sendVerificationEmail'])->name('verification');
Route::get('/email/verify/{email}/{token}', [UserController::class, 'verifyEmail'])->name('verify_email');
Route::get('/email/check-verify/{email}/{token}', [UserController::class, 'verifyEmailget'])->name('check_verify_get');

Route::get('/invalid_url' , function () {
    return view('invalid_url');
});