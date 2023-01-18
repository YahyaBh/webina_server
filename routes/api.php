<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\WebsitesController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::get('/websites', [WebsitesController::class , 'index'])->name('home');
Route::post('/websites/create', [WebsitesController::class , 'store'])->name('create');
Route::delete('/website/{token}', [WebsitesController::class , 'delete'])->name('delete');




Route::post('/signin', [UserController::class, 'login'])->name('login');
Route::post('/signup', [UserController::class, 'register'])->name('register');

