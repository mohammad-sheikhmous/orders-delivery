<?php

use App\Http\Controllers\Admin\MainCategoriesController;
use App\Http\Controllers\Admin\ProductCategoriesController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\VendorsController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function (){

    ###########         Main Categories Routes       ###########

    Route::get('main_categories', [MainCategoriesController::class, 'index']);

    Route::post('main_categories', [MainCategoriesController::class, 'create']);

    Route::put('main_categories/{main_category}', [MainCategoriesController::class, 'update']);

    Route::delete('main_categories/{main_category}', [MainCategoriesController::class, 'destroy']);

    ###########         Vendors Routes            #############

    Route::get('vendors', [VendorsController::class, 'index']);

    Route::get('vendors/{vendor}', [VendorsController::class, 'show']);

    Route::post('vendors', [VendorsController::class, 'create']);

    Route::put('vendors/{vendor}', [VendorsController::class, 'update']);

    Route::delete('vendors/{vendor}', [VendorsController::class, 'destroy']);

    ############        Product Categories Routes      #############

    Route::get('product_categories', [ProductCategoriesController::class, 'index']);

    Route::post('product_categories', [ProductCategoriesController::class, 'create']);

    Route::put('product_categories/{product_category}', [ProductCategoriesController::class, 'update']);

    Route::delete('product_categories/{product_category}', [ProductCategoriesController::class, 'destroy']);

    ############        Products Routes             ############

    Route::get('products', [ProductsController::class, 'index']);

    Route::get('products/{product}', [ProductsController::class, 'show']);

    Route::post('products', [ProductsController::class, 'create']);

    Route::put('products/{product}', [ProductsController::class, 'update']);

    Route::delete('products/{product}', [ProductsController::class, 'destroy']);

});
