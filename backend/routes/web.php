<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;

Route::get('/', function () {
    return view('welcome');
});

Route::apiResource('products', ProductController::class);
Route::apiResource('categories', CategoryController::class);
