<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebsitesController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Auth::routes([
    'verify' => true
]);

Route::get('/websites', [WebsitesController::class, 'index'])->name('home');
Route::post('/websites/create', [WebsitesController::class, 'store'])->name('create');
Route::get('/website/{token}', [WebsitesController::class, 'show'])->name('delete');
Route::delete('/website/delete/{token}', [WebsitesController::class, 'delete'])->name('delete');
Route::post('/recent/websites', [WebsitesController::class, 'recent_websites'])->name('recent');
Route::post('/orders' , [OrdersController::class, 'orders_all'])->name('orders');
Route::post('/orders/create', [OrdersController::class, 'create_order'])->name('create_order');
Route::post('/order/{token}', [OrdersController::class, 'order_show'])->name('show_order');



Route::post('/signin', [UserController::class, 'login'])->name('login');
Route::post('/signup', [UserController::class, 'register'])->name('register');
Route::post('/user' , [UserController::class, 'profile'])->name('profile');
Route::post('/user/update' , [UserController::class, 'update'])->name('update');
Route::post('/user/delete', [UserController::class, 'delete'])->name('delete');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');
Route::post('/email/verification' , [UserController::class, 'sendVerificationEmail'])->name('verification');
Route::post('/email/check-verify' , [UserController::class, 'verifyEmail'])->name('check_verify');
Route::post('/email/verify/{id}/{token}/{email}' , [UserController::class, 'verifyEmail'])->name('verify_email');



Route::get('/auth/google', [AuthController::class, 'redirectToAuth']);
Route::get('/auth/callback', [AuthController::class, 'handleAuthCallBack']);



Route::post('/message/contact' , [ContactController::class, 'store_message'])->name('message.contact');