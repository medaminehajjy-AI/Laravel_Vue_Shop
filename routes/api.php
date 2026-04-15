<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\DebugController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use App\Http\Controllers\API\MessageController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/products/search', [ProductController::class, 'search']);
// Public route to submit form
Route::post('/contact', [MessageController::class, 'store']);


Route::get('/products', [ProductController::class, 'index']);
Route::get('/home-products', [ProductController::class, 'homeProducts']);
Route::get('/products/{product}', [ProductController::class, 'show']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);



Route::middleware('auth:sanctum')->group(function () {

    // Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{cart}', [CartController::class, 'update']);
    Route::delete('/cart/{cart}', [CartController::class, 'destroy']);
    Route::get('/cart/count', [CartController::class, 'count']);

    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    
    Route::post('/checkout', [OrderController::class, 'checkout']);

});
        // ADMIN Functionalities
        Route::middleware(['auth:sanctum', 'admin'])->group(function () {

    Route::get('/admin/stats', [AdminController::class, 'stats']);
    Route::get('/admin/orders', [AdminController::class, 'orders']);
    Route::put('/admin/orders/{order}', [AdminController::class, 'updateOrderStatus']);
    Route::get('/admin/orders/unread-count', [AdminController::class, 'unreadCount']);
    Route::post('/admin/orders/mark-as-read', [AdminController::class, 'markOrdersAsRead']);

    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);

    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

    Route::get('/admin/contacts', [ContactController::class, 'index']);
    Route::put('/admin/contacts/{contact}/read', [ContactController::class, 'markAsRead']);

    Route::get('/messages/unread-count', [MessageController::class, 'unreadCount']);
    Route::get('/messages', [MessageController::class, 'index']);
    Route::post('/messages/{id}/read', [MessageController::class, 'markAsRead']);

    Route::get('/admin/revenue-chart', [AdminController::class, 'revenueChart']);
    Route::get('/admin/recent-orders', [AdminController::class, 'recentOrders']);
    // this function will be available in the future
    Route::get('/admin/top-products', [AdminController::class, 'topProducts']);
});