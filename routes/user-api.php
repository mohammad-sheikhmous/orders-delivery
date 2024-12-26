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

    Route::post('verify-mobile', [MobileController::class, 'verifyMobile']);

    Route::post('password/send-reset-code', [MobileController::class, 'sendResetCode']);

    Route::post('password/reset-password', [MobileController::class, 'resetPassword']);

    #############       Authenticated User Routes       ############

    Route::group(['middleware' => ['auth:sanctum', 'ability:user', 'userLocale']], function () {

        Route::get('logout', [LoginController::class, 'logout']);

        ######          Profile Routes      ######

        Route::get('profile/show', [ProfileController::class, 'show']);

        Route::put('profile/update', [ProfileController::class, 'update']);

        Route::delete('profile/delete', [ProfileController::class, 'destroy']);

        ######          Categories and Vendors Routes       ######

        Route::get('main_categories', [MainCategoriesController::class, 'index']);

        Route::get('{category}/vendors', [VendorsController::class, 'index']);

        Route::get('vendors/{vendor}', [VendorsController::class, 'show']);

        Route::get('{vendor}/product_categories', [ProductCategoriesController::class, 'index']);

        Route::get('{product_category}/products', [ProductsController::class, 'index']);

        Route::get('products/{product}', [ProductsController::class, 'show']);

        ######          Shopping Cart Routes        ######

        Route::get('cart', [ShoppingCartController::class, 'show']);

        Route::post('cart', [ShoppingCartController::class, 'store']);

        Route::put('cart', [ShoppingCartController::class, 'update']);

        Route::delete('cart', [ShoppingCartController::class, 'removeFromCart']);

        ######              Orders Routes             ######

        Route::get('orders', [OrdersController::class, 'index']);

        Route::post('orders', [OrdersController::class, 'create']);

        Route::put('orders/{id}', [OrdersController::class, 'update']);

        Route::delete('orders/{id}', [OrdersController::class, 'destroy']);

        Route::get('products/store', [ProductsController::class, 'store']);

        ###########            Search Routes           ##########

        Route::post('vendors/search', [SearchController::class, 'searchByVendor']);

        Route::post('products/search', [SearchController::class, 'searchByProduct']);

        Route::post('search', [SearchController::class, 'searchInGeneral']);

        ###########         Favorites Routes         ###########

        Route::get('favorites', [FavoriteController::class, 'show']);

        Route::post('favorites', [FavoriteController::class, 'store']);

        Route::delete('favorites', [FavoriteController::class, 'delete']);

        Route::get('switch_lang', \App\Http\Controllers\SwitchLangController::class);

        Route::post('fcm_tokens', \App\Http\Controllers\FcmTokensController::class);

    });
});

