<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\SubscribeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebsitesController;
use App\Http\Controllers\Admin\AdminChatController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminOrdersController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;





Route::get('/homepagetesti', [MainController::class, 'getTestimonialsFounders'])->name('home');
Route::get('/blogs', [MainController::class, 'getBlogs'])->name('blogs');
Route::get('/websites', [WebsitesController::class, 'index'])->name('websites_home');
Route::post('/recent/websites', [WebsitesController::class, 'recent_websites'])->name('recent');

Route::post('/subscribe', [SubscribeController::class, 'subscribe'])->name('subscribe');

Route::post('/signin', [UserController::class, 'login'])->name('login');
Route::post('/signup', [UserController::class, 'register'])->name('register');



Route::post('/message/contact', [ContactController::class, 'store_message'])->name('message.contact');


Route::get('/auth', [AuthController::class, 'redirectToAuth']);
Route::get('/auth/callback', [AuthController::class, 'handleAuthCallBack']);

Route::post('/email/verifiction', [UserController::class, 'sendVerificationEmail'])->name('email.verifiction');
Route::post('/email/verifiction/check', [UserController::class, 'checkVerification'])->name('email.verifiction');
Route::get('/email/verify/{email}/{token}', [UserController::class, 'verifyEmail'])->name('email.verify');


Route::middleware('auth:sanctum')->group(function () {


    Route::middleware('admin')->group(function () {

        Route::post('/websites/create', [WebsitesController::class, 'store'])->name('create');
        Route::post('/websites/update', [WebsitesController::class, 'update'])->name('update.website');
        Route::post('/websites/delete', [WebsitesController::class, 'delete'])->name('delete.website');
        
        Route::post('/admin/dashboard' , [AdminDashboardController::class, 'index'])->name('dashboard');
        
        Route::post('/admin/chat', [AdminChatController::class, 'admin_chat'])->name('admin.chat');
        Route::post('/admin/chat/user', [AdminChatController::class, 'user_messages'])->name('admin.User.chat');
    
        Route::post('/admin/users', [AdminUsersController::class, 'user_index'])->name('admin.users');

        Route::post('/admin/orders', [AdminDashboardController::class , 'getOrders'])->name('admin.orders');
        Route::post('/admin/order', [AdminDashboardController::class , 'getOrder'])->name('admin.order');


        // Route::post('/admin/order/confirmation', [AdminOrdersController::class, 'order_status'])->name('admin.order.confirmation');
    });

    Route::post('/profile', [UserController::class, 'profile'])->name('profile');

    Route::get('/website/{token}', [WebsitesController::class, 'show'])->name('delete');

    Route::post('/orders', [OrdersController::class, 'orders_all'])->name('orders');
    Route::post('/orders/create', [OrdersController::class, 'create_order'])->name('create_order');
    Route::post('/order', [OrdersController::class, 'order_show'])->name('show_order');

    Route::post('/checkout', [CheckoutController::class, 'paymecntCheck'])->name('checkout');


    Route::post('/user', [UserController::class, 'profile'])->name('profile');
    Route::post('/user/update', [UserController::class, 'update'])->name('update');
    Route::post('/user/update/avatar', [UserController::class, 'updateAvatar'])->name('updateAvatar');
    Route::post('/user/delete', [UserController::class, 'delete'])->name('delete');

    Route::post('/chat/message', [ChatController::class, 'sendMessage'])->name('sendMessage');
    Route::post('/chat/messages', [ChatController::class, 'messages'])->name('messages');
});
