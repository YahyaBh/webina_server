<?php

use Illuminate\Support\Facades\Route;





Route::middleware(['api', 'check.frontend'])->group(function () {
    Route::get('/', function () {
    });
});


Route::get('/invalid_url' , function () {
    return view('invalid_url');
});

require __DIR__ . '/auth.php';
