<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ecommerce\ProductViewController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\GoogleController; 
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\ShipmentController;
use App\Http\Controllers\Api\OrdersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\SessionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
Route::middleware('web')->group(function () {

    // Product view routes
    Route::prefix('view')->name('view.')->group(function () {

        // Public: /view/carts
        Route::get('/carts', [ProductViewController::class, 'cart'])->name('products.cart');

        // All the following routes require login
        Route::middleware('auth')->group(function () {
            Route::get('/orderForm', [ProductViewController::class, 'orderForm'])->name('orderForm');
            Route::get('/orderForm/EditPage', [ProductViewController::class, 'EditPage'])->name('orderForm.EditPage');
            Route::get('/orderForm/EditPage/edit', [ProductViewController::class, 'edit'])->name('orderForm.edit');
            Route::get('/orderForm/EditPage/addNewAddress', [ProductViewController::class, 'addNewAddress'])->name('orderForm.addNewAddress');
            Route::get('/orderlist', [ProductViewController::class, 'order'])->name('products.order');
        });
    });

    // Public homepage and product filter
    Route::get('/', [ProductViewController::class, 'index'])->name('view.products.index');
    Route::get('/products/filter', [ProductViewController::class, 'filter'])->name('view.products.filter');
});

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('api')->middleware('api')->group(function () {

    // Session Management
    Route::post('/save-delivery-zip-session', [SessionController::class, 'saveDeliveryZipSession'])->name('save.delivery.zip.session');
    Route::post('/guest-cart-session',  [SessionController::class, 'guestCartSession'])->name('guest.cart.session');

    // Products
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
    Route::get('/products/filter', [ProductController::class, 'filter'])->name('products.filter');

    // Cart
    Route::get('/carts', [CartController::class, 'index'])->name('carts.index');
    Route::post('/carts/updateOrCreate', [CartController::class, 'updateOrCreateCart'])->name('carts.updateOrCreate');
    Route::post('/carts/deleteMultiple', [CartController::class, 'destroyMultiple'])->name('carts.destroyMultiple');
    Route::post('/carts/cartSummary', [CartController::class, 'cartSummary'])->name('carts.summary');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'getUserWishlist'])->name('wishlist.getUserWishlist');
    Route::post('/wishlist/add', [WishlistController::class, 'addToWishlist'])->name('wishlist.addToWishlist');

    // Shipment
    Route::get('/shipment', [ShipmentController::class, 'index'])->name('shipment.index');
    Route::post('/shipment/store', [ShipmentController::class, 'store'])->name('shipment.store');
    Route::post('/shipment/update/{shipment}', [ShipmentController::class, 'update'])->name('shipment.update');

    // Orders
    Route::get('/orders', [OrdersController::class, 'index'])->name('orders.index');
    Route::post('/orders/store', [OrdersController::class, 'store'])->name('orders.store');
});

// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::get('/register', function () {
        return Auth::check() ? redirect()->route('view.products.index') : view('auth.register');
    })->name('register');

    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', function () {
        return Auth::check() ? redirect()->route('view.products.index') : view('auth.login');
    })->name('login');

    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    // Google Authentication
    Route::get('/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('/google/callback', [GoogleController::class, 'handleGoogleCallback']);
});
 
    Route::fallback(function () {
        return response()->view('components.errors.404', [], 404);
    });