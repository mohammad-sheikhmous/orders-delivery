<?php

use App\Http\Controllers\SearchController;
use App\Http\Controllers\User\Auth\LoginController;
use App\Http\Controllers\User\Auth\MobileController;
use App\Http\Controllers\User\Auth\RegisterController;
use App\Http\Controllers\User\FavoriteController;
use App\Http\Controllers\User\MainCategoriesController;
use App\Http\Controllers\User\OrdersController;
use App\Http\Controllers\User\ProductCategoriesController;
use App\Http\Controllers\User\ProductsController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\ShoppingCartController;
use App\Http\Controllers\User\VendorsController;
use Illuminate\Support\Facades\Route;


Route::prefix('user')->group(function () {

    #############      Authentication Routes    ############

    Route::post('login', [LoginController::class, 'login']);//->middleware('auth:sanctum');

    Route::post('register', [RegisterController::class, 'register']);

    Route::controller(MobileController::class)->group(function () {

        Route::post('verify-mobile', 'verifyMobile')->middleware(['throttle:auth']);//->middleware(['throttle:5,1']);

        Route::post('password/send-reset-code', 'sendResetCode');

        Route::post('password/reset-password', 'resetPassword')->middleware(['throttle:auth']);
    });

    #############       Authenticated User Routes       ############

    Route::group(['middleware' => ['auth:sanctum', 'ability:user', 'userLocale']], function () {

        Route::get('logout', [LoginController::class, 'logout']);

        ######          Profile Routes      ######

        Route::controller(ProfileController::class)->group(function () {

            Route::get('profile/show', 'show');

            Route::put('profile/update', 'update');

            Route::delete('profile/delete', 'destroy');
        });

        ######          Categories and Vendors Routes       ######

        Route::get('main_categories', [MainCategoriesController::class, 'index']);

        Route::get('{category}/vendors', [VendorsController::class, 'index']);

        Route::get('vendors/{vendor}', [VendorsController::class, 'show']);

        Route::get('{vendor}/product_categories', [ProductCategoriesController::class, 'index']);

        Route::get('{product_category}/products', [ProductsController::class, 'index']);

        Route::get('products/{product}', [ProductsController::class, 'show']);

        ######          Shopping Cart Routes        ######

        Route::controller(ShoppingCartController::class)->group(function () {

            Route::get('cart', 'show');

            Route::post('cart', 'store');

            Route::put('cart', 'update');

            Route::delete('cart', 'removeFromCart');
        });

        ######              Orders Routes             ######

        Route::controller(OrdersController::class)->group(function () {

            Route::get('orders', 'index');

            Route::post('orders', 'create');

            Route::put('orders/{id}', 'update');

            Route::delete('orders/{id}', 'destroy');
        });

        ###########            Search Routes           ##########

        Route::controller(SearchController::class)->group(function () {

            Route::post('vendors/search', 'searchByVendor');

            Route::post('products/search', 'searchByProduct');

            Route::post('search', 'searchInGeneral');
        });

        ###########         Favorites Routes         ###########

        Route::controller(FavoriteController::class)->group(function () {

            Route::get('favorites', 'show');

            Route::post('favorites', 'store');

            Route::delete('favorites', 'delete');
        });

        Route::get('switch_lang', \App\Http\Controllers\SwitchLangController::class);

        Route::post('fcm_tokens', \App\Http\Controllers\FcmTokensController::class);

        Route::get('notifications', [\App\Http\Controllers\User\NotificationsController::class, 'index']);
    });
});

