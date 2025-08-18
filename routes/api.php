<?php

use App\Http\Controllers\admin\AuthController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\front\FrontProductController;
use App\Http\Controllers\admin\SizeController;
use App\Http\Controllers\admin\TempImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/admin/login', [AuthController::class, 'authenticate']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    // Route::get('categories', [CategoryController::class, 'index']);
    // Route::post('categories', [CategoryController::class, 'store']);
    // Route::delete('categories/{id}', [CategoryController::class, 'destroy']);
    // Route::get('categories/{id}', [CategoryController::class, 'show']);
    // Route::put('categories/{id}', [CategoryController::class, 'update']);

    Route::resource('categories', CategoryController::class);
    Route::resource('brands', BrandController::class);
    Route::get('sizes', [SizeController::class, 'index']);
    Route::resource('products', ProductController::class);


    Route::get('get-latest-products', [FrontProductController::class, 'latestProducts']);
    Route::get('get-featured-products', [FrontProductController::class, 'featuredProducts']);
    Route::get('get-categories', [FrontProductController::class, 'getCategories']);
    Route::get('get-brands', [FrontProductController::class, 'getBrands']);
    Route::get('get-products', [FrontProductController::class, 'getProducts']);
    Route::get('get-product/{id}', [FrontProductController::class, 'getProduct']);
    
    Route::post('temp-images', [TempImageController::class, 'store']);
    Route::post('save-product-image', [ProductController::class, 'saveProductImage']);
    Route::get('change-product-default-image', [ProductController::class, 'updateDefaultImage']);
    Route::delete('delete-product-image/{id}', [ProductController::class, 'deleteProductImage']);

});
