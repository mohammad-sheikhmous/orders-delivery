<?php

use App\Http\Controllers\Admin\MainCategoriesController;
use App\Http\Controllers\Admin\ProductCategoriesController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\VendorsController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {

    ###########         Main Categories Routes       ###########

    Route::controller(MainCategoriesController::class)->group(function () {

        Route::get('main_categories', 'index');

        Route::post('main_categories', 'create');

        Route::put('main_categories/{main_category}', 'update');

        Route::delete('main_categories/{main_category}', 'destroy');
    });

    ###########         Vendors Routes            #############

    Route::controller(VendorsController::class)->group(function () {

        Route::get('vendors', 'index');

        Route::get('vendors/{vendor}', 'show');

        Route::post('vendors', 'create');

        Route::put('vendors/{vendor}', 'update');

        Route::delete('vendors/{vendor}', 'destroy');
    });

    ############        Product Categories Routes      #############

    Route::controller(ProductCategoriesController::class)->group(function () {

        Route::get('product_categories', 'index');

        Route::post('product_categories', 'create');

        Route::put('product_categories/{product_category}', 'update');

        Route::delete('product_categories/{product_category}', 'destroy');
    });

    ############        Products Routes             ############

    Route::controller(ProductsController::class)->group(function () {

        Route::get('products', 'index');

        Route::get('products/{product}', 'show');

        Route::post('products', 'create');

        Route::put('products/{product}', 'update');

        Route::delete('products/{product}', 'destroy');
    });
});
