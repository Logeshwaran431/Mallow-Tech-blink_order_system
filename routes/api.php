<?php

use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\CustomerOrderController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
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

// Route names are prefixed with "api." so they do not clash with web.php.
Route::name('api.')->group(function () {
    // Customers
    Route::apiResource('customers', CustomerController::class);
    Route::get('customers/{customer}/orders', [CustomerOrderController::class, 'index'])
        ->name('customers.orders.index');

    // Products (low-stock must be registered before {product})
    Route::get('products/low-stock', [ProductController::class, 'lowStock'])
        ->name('products.low-stock');
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::match(['put', 'patch'], 'products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::post('products/{product}/restock', [ProductController::class, 'restock'])->name('products.restock');

    // Orders
    Route::apiResource('orders', OrderController::class)->only(['index', 'store', 'show']);
});
