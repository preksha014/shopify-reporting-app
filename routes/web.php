<?php

use App\Http\Controllers\Dashboard;
use Illuminate\Support\Facades\Route;

Route::get('/', [Dashboard::class, 'dashboard'])
    ->middleware(['verify.shopify'])
    ->name('home');
