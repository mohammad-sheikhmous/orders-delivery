<?php

use App\Http\Controllers\Admin\MainCategoriesController;
use App\Http\Controllers\Admin\ProductCategoriesController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\VendorsController;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Auth\Middleware\Authenticate;

define('PAGINATION_COUNT', 5);

Route::prefix('admin')->group(function () {

    Route::group(['middleware' => 'guest:admin'], function () {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('admin.showLoginForm');

        Route::post('login', [LoginController::class, 'login'])->name('admin.login');
    });

    Route::group(['middleware' => 'auth:admin'], function () {

        Route::get('/dashboard', DashboardController::class)->name('admin.dashboard');

        ########## Main Category Routes ############
        Route::group(['prefix' => 'main_categories'], function () {

            Route::get('/', [MainCategoriesController::class, 'index'])->name('admin.main_categories.index');

            Route::get('create', [MainCategoriesController::class, 'create'])->name('admin.main_categories.create');

            Route::post('store', [MainCategoriesController::class, 'store'])->name('admin.main_categories.store');

            Route::get('/{main_category}/edit', [MainCategoriesController::class, 'edit'])->name('admin.main_categories.edit');

            Route::put('{main_category}', [MainCategoriesController::class, 'update'])->name('admin.main_categories.update');

            Route::get('{main_category}', [MainCategoriesController::class, 'destroy'])->name('admin.main_categories.destroy');

            Route::get('/changeStatus/{main_category}', [MainCategoriesController::class, 'changeStatus'])->name('admin.main_categories.status');
        });

        ########## Vendor Routes ############

        Route::group(['prefix' => 'vendors'], function () {

            Route::get('/', [VendorsController::class, 'index'])->name('admin.vendors.index');

            Route::get('create', [VendorsController::class, 'create'])->name('admin.vendors.create');

            Route::post('', [VendorsController::class, 'store'])->name('admin.vendors.store');

            Route::get('/{vendor}/edit', [VendorsController::class, 'edit'])->name('admin.vendors.edit');

            Route::patch('{vendor}', [VendorsController::class, 'update'])->name('admin.vendors.update');

            Route::get('/{vendor}', [VendorsController::class, 'destroy'])->name('admin.vendors.destroy');

            Route::get('/changeStatus/{vendor}', [VendorsController::class, 'changeStatus'])->name('admin.vendors.status');
        });

        ############        Product Categories Routes      #############

        Route::group(['prefix' => 'product_categories'], function () {

            Route::get('/', [ProductCategoriesController::class, 'index'])->name('admin.product_categories.index');

            Route::get('create', [ProductCategoriesController::class, 'create'])->name('admin.product_categories.create');

            Route::post('', [ProductCategoriesController::class, 'store'])->name('admin.product_categories.store');

            Route::get('/{product_category}/edit', [ProductCategoriesController::class, 'edit'])->name('admin.product_categories.edit');

            Route::put('{product_category}', [ProductCategoriesController::class, 'update'])->name('admin.product_categories.update');

            Route::get('/{product_category}', [ProductCategoriesController::class, 'destroy'])->name('admin.product_categories.destroy');

            Route::get('/changeStatus/{product_category}', [ProductCategoriesController::class, 'changeStatus'])->name('admin.product_categories.status');
        });

        ############        Products Routes             ############

        Route::group(['prefix' => 'products'], function () {

            Route::get('/', [ProductsController::class, 'index'])->name('admin.products.index');

            Route::get('create', [ProductsController::class, 'create'])->name('admin.products.create');

            Route::post('', [ProductsController::class, 'store'])->name('admin.products.store');

            Route::get('/{product}/edit', [ProductsController::class, 'edit'])->name('admin.products.edit');

            Route::put('{product}', [ProductsController::class, 'update'])->name('admin.products.update');

            Route::get('/{product}', [ProductsController::class, 'destroy'])->name('admin.products.destroy');

            Route::get('/changeStatus/{product}', [ProductsController::class, 'changeStatus'])->name('admin.products.status');
        });

    });
});


Route::prefix('admin')->group(function () {

    ###########         Main Categories Routes       ###########
//
//    Route::controller(MainCategoriesController::class)->group(function () {
//
//        Route::get('main_categories', 'index');
//
//        Route::post('main_categories', 'create');
//
//        Route::put('main_categories/{main_category}', 'update');
//
//        Route::delete('main_categories/{main_category}', 'destroy');
//    });
//
//    ###########         Vendors Routes            #############
//
//    Route::controller(VendorsController::class)->group(function () {
//
//        Route::get('vendors', 'index');
//
//        Route::get('vendors/{vendor}', 'show');
//
//        Route::post('vendors', 'create');
//
//        Route::put('vendors/{vendor}', 'update');
//
//        Route::delete('vendors/{vendor}', 'destroy');
//    });
//
//    ############        Product Categories Routes      #############
//
//    Route::controller(ProductCategoriesController::class)->group(function () {
//
//        Route::get('product_categories', 'index');
//
//        Route::post('product_categories', 'create');
//
//        Route::put('product_categories/{product_category}', 'update');
//
//        Route::delete('product_categories/{product_category}', 'destroy');
//    });
//
//    ############        Products Routes             ############

//    Route::controller(ProductsController::class)->group(function () {
//
//        Route::get('products', 'index');
//
//        Route::get('products/{product}', 'show');
//
//        Route::post('products', 'create');
//
//        Route::put('products/{product}', 'update');
//
//        Route::delete('products/{product}', 'destroy');
//    });
});
