<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Users\Index as UsersIndex;
use App\Livewire\Admin\Users\Create as UsersCreate;
use App\Livewire\Admin\Users\Edit as UsersEdit;
use App\Livewire\Admin\Products\Index as ProductsIndex;
use App\Livewire\Admin\Products\Create as ProductsCreate;
use App\Livewire\Admin\Products\Edit as ProductsEdit;
use App\Livewire\Admin\Products\Show as ProductsShow;
use App\Livewire\Admin\Products\ImageUpload as ProductsImageUpload;
use App\Livewire\Admin\Categories\Index as CategoriesIndex;
use App\Livewire\Admin\Categories\Create as CategoriesCreate;
use App\Livewire\Admin\Categories\Edit as CategoriesEdit;
use App\Livewire\Admin\Orders\Index as OrdersIndex;
use App\Livewire\Admin\Orders\Show as OrdersShow;
use App\Livewire\Admin\Orders\Create as OrdersCreate;
use App\Livewire\Admin\Orders\Edit as OrdersEdit;
use App\Livewire\Admin\Coupons\Index as CouponsIndex;
use App\Livewire\Admin\Coupons\Create as CouponsCreate;
use App\Livewire\Admin\Coupons\Edit as CouponsEdit;
use App\Livewire\Admin\Reviews\Index as ReviewsIndex;
use App\Livewire\Admin\Settings;
use App\Livewire\Admin\MediaGallery;
use App\Livewire\Admin\Tickets\Index as TicketsIndex;
use App\Livewire\Admin\Tickets\Show as TicketsShow;
use App\Livewire\Admin\Tickets\Create as TicketsCreate;
use App\Livewire\Auth\Login;
use App\Http\Controllers\Admin\OrderPrintController;

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
    Route::get('/users/{user}/edit', UsersEdit::class)->name('users.edit');
    
    // Products Management
    Route::get('/products', ProductsIndex::class)->name('products.index');
    Route::get('/products/create', ProductsCreate::class)->name('products.create');
    Route::get('/products/{product}', ProductsShow::class)->name('products.show');
    Route::get('/products/{product}/edit', ProductsEdit::class)->name('products.edit');
    Route::get('/products/{product}/images', ProductsImageUpload::class)->name('products.images');
    
    // Categories Management
    Route::get('/categories', CategoriesIndex::class)->name('categories.index');
    Route::get('/categories/create', CategoriesCreate::class)->name('categories.create');
    Route::get('/categories/{category}', function () {
        return 'Show Category Page';
    })->name('categories.show');
    Route::get('/categories/{category}/edit', CategoriesEdit::class)->name('categories.edit');
    
    // Orders Management
    Route::get('/orders', OrdersIndex::class)->name('orders.index');
    Route::get('/orders/create', OrdersCreate::class)->name('orders.create');
    Route::get('/orders/{order}', OrdersShow::class)->name('orders.show');
    Route::get('/orders/{order}/edit', OrdersEdit::class)->name('orders.edit');
    
    // Order Print HTML Views
    Route::get('/orders/{order}/invoice-html', [OrderPrintController::class, 'invoiceHtml'])->name('orders.invoice-html');
    Route::get('/orders/{order}/shipping-label-html', [OrderPrintController::class, 'shippingLabelHtml'])->name('orders.shipping-label-html');
    
    // Coupons Management
    Route::get('/coupons', CouponsIndex::class)->name('coupons.index');
    Route::get('/coupons/create', CouponsCreate::class)->name('coupons.create');
    Route::get('/coupons/{coupon}/edit', CouponsEdit::class)->name('coupons.edit');
    
    // Reviews Management
    Route::get('/reviews', ReviewsIndex::class)->name('reviews.index');
    
    // Media Gallery
    Route::get('/media', MediaGallery::class)->name('media.index');
    
    // Support Tickets
    Route::get('/tickets', TicketsIndex::class)->name('tickets.index');
    Route::get('/tickets/create', TicketsCreate::class)->name('tickets.create');
    Route::get('/tickets/{ticket}', TicketsShow::class)->name('tickets.show');
    
    // Settings
    Route::get('/settings', Settings::class)->name('settings');
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
        return redirect('/');
    })->name('logout');
});
