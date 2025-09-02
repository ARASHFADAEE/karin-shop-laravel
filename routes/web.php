<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Users\Index as UsersIndex;
use App\Livewire\Admin\Users\Create as UsersCreate;
use App\Livewire\Admin\Products\Index as ProductsIndex;
use App\Livewire\Admin\Orders\Index as OrdersIndex;
use App\Livewire\Auth\Login;

Route::get('/', function () {
    return view('welcome');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', Dashboard::class)->name('dashboard');
    
    // Users Management
    Route::get('/users', UsersIndex::class)->name('users.index');
    Route::get('/users/create', UsersCreate::class)->name('users.create');
    Route::get('/users/{user}/edit', function () {
        return 'Edit User Page';
    })->name('users.edit');
    
    // Products Management
    Route::get('/products', ProductsIndex::class)->name('products.index');
    Route::get('/products/create', function () {
        return 'Create Product Page';
    })->name('products.create');
    Route::get('/products/{product}', function () {
        return 'Show Product Page';
    })->name('products.show');
    Route::get('/products/{product}/edit', function () {
        return 'Edit Product Page';
    })->name('products.edit');
    
    // Categories Management
    Route::get('/categories', function () {
        return 'Categories Index Page';
    })->name('categories.index');
    
    // Orders Management
    Route::get('/orders', OrdersIndex::class)->name('orders.index');
    Route::get('/orders/{order}', function () {
        return 'Show Order Page';
    })->name('orders.show');
    Route::get('/orders/{order}/edit', function () {
        return 'Edit Order Page';
    })->name('orders.edit');
    
    // Coupons Management
    Route::get('/coupons', function () {
        return 'Coupons Index Page';
    })->name('coupons.index');
    
    // Reviews Management
    Route::get('/reviews', function () {
        return 'Reviews Index Page';
    })->name('reviews.index');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});
