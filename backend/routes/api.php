<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Products routes
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/category/{category}', [ProductController::class, 'getByCategory']);
Route::get('/products/search/{query}', [ProductController::class, 'search']);
Route::get('/products/{product}', [ProductController::class, 'show']);

// Categories routes
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']); 