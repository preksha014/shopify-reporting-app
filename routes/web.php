<?php

use Illuminate\Support\Facades\Route;


Route::get('/', [\App\Http\Controllers\Dashboard::class, 'dashboard'])
    ->middleware(['verify.shopify'])
    ->name('home');