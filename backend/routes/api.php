<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\DeliveryAddressController;
use App\Http\Controllers\Api\OrderController;
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
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
});

// Routes protégées par Sanctum
Route::middleware('auth:sanctum')->group(function () {
    
    // Routes d'authentification protégées
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('logout-all', [AuthController::class, 'logoutAll']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('check-token', [AuthController::class, 'checkToken']);
        Route::put('change-password', [AuthController::class, 'changePassword']);
    });

    // Obtenir l'utilisateur authentifié
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Profile routes (notre système de profil)
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::put('/', [ProfileController::class, 'update']);
        Route::put('/password', [ProfileController::class, 'updatePassword']);
        Route::post('/avatar', [ProfileController::class, 'uploadAvatar']);
        Route::delete('/avatar', [ProfileController::class, 'deleteAvatar']);
    });
    
    // Delivery addresses routes (notre système d'adresses)
    Route::apiResource('delivery-addresses', DeliveryAddressController::class);
    Route::put('delivery-addresses/{address}/set-default', [DeliveryAddressController::class, 'setDefault']);
    
    // Orders routes (notre système de commandes)
    Route::apiResource('orders', OrderController::class)->only(['index', 'show']);
    Route::put('orders/{order}/cancel', [OrderController::class, 'cancel']);
    Route::get('orders/{order}/invoice', [OrderController::class, 'downloadInvoice']);

    // Routes de gestion du profil utilisateur (système alternatif)
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

        // Commandes
        Route::get('/orders', [UserController::class, 'getUserOrders']);

        // Favoris
        Route::get('/favorites', [UserController::class, 'getUserFavorites']);
    });
});

// Routes publiques des ressources
Route::apiResource('products', ProductController::class);
Route::apiResource('categories', CategoryController::class);