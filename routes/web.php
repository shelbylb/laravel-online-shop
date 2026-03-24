<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])
        ->name('orders.status.update');
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
