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
use App\Http\Controllers\WebSiteStatusController;
use App\Models\WebSiteStatus;
use Illuminate\Support\Facades\Route;




Route::get('/website/status', [WebSiteStatusController::class, 'index'])->name('websites.status');
Route::get('/testimonials', [MainController::class, 'getTestimonialsFounders'])->name('home');
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

        Route::post('/admin/website/create', [WebsitesController::class, 'store'])->name('create');
        Route::post('/admin/website/update', [WebsitesController::class, 'update'])->name('update.website');
        Route::post('/admin/website/delete', [WebsitesController::class, 'delete'])->name('delete.website');

        Route::post('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::post('/admin/chat', [AdminChatController::class, 'admin_chat'])->name('admin.chat');
        Route::post('/admin/chat/user', [AdminChatController::class, 'user_messages'])->name('admin.User.chat');

        Route::post('/admin/users', [AdminDashboardController::class, 'users_index'])->name('admin.users');

        Route::post('/admin/orders', [AdminDashboardController::class, 'getOrders'])->name('admin.orders');
        Route::post('/admin/order', [AdminDashboardController::class, 'getOrder'])->name('admin.order');
        Route::post('/admin/order/status', [AdminDashboardController::class, 'setOrderStatus'])->name('admin.order.status');

        Route::post('/admin/websites', [AdminDashboardController::class, 'website_index'])->name('admin.websites');

        Route::post('/admin/website/status/set', [WebSiteStatusController::class, 'setStatus'])->name('websites.status.set');


        Route::post('/admin/newsletter', [AdminDashboardController::class, 'news_letter'])->name('news_letter');

        Route::get('/admin/blogs', [AdminDashboardController::class, 'blogs_index'])->name('blogs');
        Route::post('/admin/blogs/create', [AdminDashboardController::class, 'blogs_create'])->name('blogs_create');
        Route::post('/admin/blogs/update/{id}', [AdminDashboardController::class , 'blogs_update'])->name('blogs_update');
        Route::post('/admin/blogs/delete/{id}', [AdminDashboardController::class , 'blogs_delete'])->name('blogs_delete');


        Route::get('/admin/contact', [AdminDashboardController::class, 'contact_index'])->name('contact');

        Route::get('/discounts', [AdminDashboardController::class, 'discount_index'])->name('discounts');
        Route::post('/discount', [AdminDashboardController::class, 'discount_create'])->name('discount_create');

        // Route::post('/admin/order/confirmation', [AdminOrdersController::class, 'order_status'])->name('admin.order.confirmation');
    });

    Route::post('/profile', [UserController::class, 'profile'])->name('profile');

    Route::get('/website/{token}', [WebsitesController::class, 'show'])->name('delete');
    Route::post('/websites/download', [WebsitesController::class, 'download_website'])->name('download');

    Route::post('/orders', [OrdersController::class, 'orders_all'])->name('orders');
    Route::post('/order', [OrdersController::class, 'order_show'])->name('show_order');

    Route::post('/checkout', [CheckoutController::class, 'paymecntCheck'])->name('checkout');
    Route::post('/checkout/paypal', [CheckoutController::class, 'paypalcheckout'])->name('checkout.paypalcheckout');

    Route::post('/checkout/cash', [CheckoutController::class, 'cashCheckout'])->name('cash.checkout');
    Route::post('/payment/westmoney', [CheckoutController::class, 'cashCheckoutCheck'])->name('cash.checkout.check');

    Route::post('/checkout/discount', [CheckoutController::class, 'discountCheck'])->name('discount_check');

    Route::post('/review/create' , [CheckoutController::class, 'creteReview'])->name('review_create');

    Route::post('/user', [UserController::class, 'profile'])->name('profile');
    Route::post('/user/update', [UserController::class, 'update'])->name('update');
    Route::post('/user/update/avatar', [UserController::class, 'updateAvatar'])->name('updateAvatar');
    Route::post('/user/password/update', [UserController::class, 'passwordUpdate'])->name('updatePassword');

    Route::post('/chat/message', [ChatController::class, 'sendMessage'])->name('sendMessage');
    Route::post('/chat/messages', [ChatController::class, 'messages'])->name('messages');
});
