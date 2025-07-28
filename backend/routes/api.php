<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Routes publiques d'authentification
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// Routes protégées par Sanctum
Route::middleware('auth:sanctum')->group(function () {

    // Routes d'authentification protégées
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/check-token', [AuthController::class, 'checkToken']);
        Route::put('/change-password', [AuthController::class, 'changePassword']);
    });

    // Obtenir l'utilisateur authentifié
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ]);
    });

    // Routes de gestion du profil utilisateur
    Route::prefix('user')->group(function () {
        Route::get('/profile', [UserController::class, 'getProfile']);
        Route::put('/profile', [UserController::class, 'updateProfile']);
        Route::put('/change-password', [UserController::class, 'changePassword']);
        Route::delete('/account', [UserController::class, 'deleteAccount']);
        Route::get('/stats', [UserController::class, 'getUserStats']);

        // Avatar
        Route::post('/avatar', [UserController::class, 'uploadAvatar']);
        Route::delete('/avatar', [UserController::class, 'removeAvatar']);

        // Adresses
        Route::get('/addresses', [UserController::class, 'getAddresses']);
        // Route::post('/addresses', [UserController::class, 'createAddress']);
        // Route::put('/addresses/{id}', [UserController::class, 'updateAddress']);
        // Route::delete('/addresses/{id}', [UserController::class, 'deleteAddress']);
        // Route::put('/addresses/{id}/set-default', [UserController::class, 'setDefaultAddress']);

        // Commandes
        Route::get('/orders', [UserController::class, 'getUserOrders']);
        // Route::get('/orders/recent', [OrderController::class, 'getRecentOrders']);
        // Route::get('/orders/search', [OrderController::class, 'searchOrders']);
        // Route::get('/orders/{id}', [OrderController::class, 'getOrderDetails']);
        // Route::put('/orders/{id}/cancel', [OrderController::class, 'cancelOrder']);
        // Route::get('/orders/{id}/invoice', [OrderController::class, 'downloadInvoice']);

        // Favoris
        Route::get('/favorites', [UserController::class, 'getUserFavorites']);
        // Route::post('/favorites/{productId}', [UserController::class, 'toggleFavorite']);
        // Route::delete('/favorites/{productId}', [UserController::class, 'removeFavorite']);
    });
});

// Routes publiques des ressources
Route::apiResource('products', ProductController::class);
Route::apiResource('categories', CategoryController::class);