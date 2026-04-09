<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])
        ->name('orders.status.update');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});

require __DIR__.'/auth.php';

Route::view('/', 'layouts.main')->name('main');

Route::get('/products', [ProductController::class, 'index'])
    ->name('products.index');

Route::get('/products/{product}', [ProductController::class, 'show'])
    ->name('products.show');

Route::get('/categories', [CategoryController::class, 'index'])
    ->name('categories.index');

Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])
    ->name('categories.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

Route::post('/cart/items/{product}', [CartController::class, 'store'])->name('cart.items.store');

Route::patch('/cart/items/{product}', [CartController::class, 'update'])->name('cart.items.update');

Route::delete('/cart/items/{product}', [CartController::class, 'destroy'])->name('cart.items.destroy');

Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');

Route::post('/cart/items/{product}/increase', [CartController::class, 'increase'])->name('cart.items.increase');
Route::post('/cart/items/{product}/decrease', [CartController::class, 'decrease'])->name('cart.items.decrease');
Route::get('/cart/items/quantities', [CartController::class, 'quantities'])->name('cart.items.quantities');

Route::middleware(['auth', 'role:admin,manager'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('users', UserController::class);
        Route::resource('products', AdminProductController::class);
    });
