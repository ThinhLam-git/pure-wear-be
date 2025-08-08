<?php

use App\Http\Controllers\admin\AuthController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/admin/login', [AuthController::class,'authenticate']);

Route::group(['middleware'=>'auth:sanctum'], function(){
    // Route::get('categories', [CategoryController::class, 'index']);
    // Route::post('categories', [CategoryController::class, 'store']);
    // Route::delete('categories/{id}', [CategoryController::class, 'destroy']);
    // Route::get('categories/{id}', [CategoryController::class, 'show']);
    // Route::put('categories/{id}', [CategoryController::class, 'update']);

    Route::resource('categories', CategoryController::class);
    Route::resource('brands', BrandController::class);
});