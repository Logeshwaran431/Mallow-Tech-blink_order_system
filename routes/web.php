<?php

use App\Http\Controllers\Web\CustomerController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', '/orders');

// Products (low-stock must be registered before any {product} route)
Route::get('products/low-stock', [ProductController::class, 'lowStock'])->name('products.low-stock');
Route::get('products', [ProductController::class, 'index'])->name('products.index');
Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
Route::post('products/{product}/restock', [ProductController::class, 'restock'])->name('products.restock');

// Customers
Route::resource('customers', CustomerController::class);

// Orders
Route::resource('orders', OrderController::class)->only(['index', 'create', 'store', 'show']);
