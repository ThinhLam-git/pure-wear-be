<?php

use App\Http\Controllers\admin\AuthController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\SizeController;
use App\Http\Controllers\admin\TempImageController;
use App\Http\Controllers\front\AccountController;
use App\Http\Controllers\front\FrontProductController;
use App\Http\Controllers\front\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/admin/login', [AuthController::class, 'authenticate']);

// Public routes (no authentication required)
Route::post('register', [AccountController::class, 'register'])->name('register');
Route::post('login', [AccountController::class, 'authenticate'])->name('login');

Route::group(['middleware' => ['auth:sanctum', 'checkUserRole']], function () {
    // Customer-authenticated endpoints
    Route::post('save-order', [OrderController::class, 'saveOrder']);
    Route::get('get-latest-products', [FrontProductController::class, 'latestProducts']);
    Route::get('get-featured-products', [FrontProductController::class, 'featuredProducts']);
    Route::get('get-categories', [FrontProductController::class, 'getCategories']);
    Route::get('get-brands', [FrontProductController::class, 'getBrands']);
    Route::get('get-products', [FrontProductController::class, 'getProducts']);
    Route::get('get-product/{id}', [FrontProductController::class, 'getProduct']);
    Route::get('get-sizes', [FrontProductController::class, 'getAllSizes']);
});

Route::group(['middleware' => ['auth:sanctum', 'checkAdminRole']], function () {
    Route::resource('brands', BrandController::class);
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('sizes', SizeController::class);

    // Route::get('get-latest-products', [FrontProductController::class, 'latestProducts']);
    // Route::get('get-featured-products', [FrontProductController::class, 'featuredProducts']);
    // Route::get('get-categories', [FrontProductController::class, 'getCategories']);
    // Route::get('get-brands', [FrontProductController::class, 'getBrands']);
    // Route::get('get-products', [FrontProductController::class, 'getProducts']);
    // Route::get('get-product/{id}', [FrontProductController::class, 'getProduct']);

    Route::post('temp-images', [TempImageController::class, 'store']);
    Route::post('save-product-image', [ProductController::class, 'saveProductImage']);
    Route::get('change-product-default-image', [ProductController::class, 'updateDefaultImage']);
    Route::delete('delete-product-image/{id}', [ProductController::class, 'deleteProductImage']);
});
