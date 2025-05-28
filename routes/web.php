<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard;

Route::get('/', [Dashboard::class, 'dashboard'])
    ->middleware(['verify.shopify'])
    ->name('home');