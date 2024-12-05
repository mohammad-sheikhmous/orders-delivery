<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\MobileController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('user/login', [LoginController::class,'login']);//->middleware('auth:sanctum');

Route::post('user/register',[RegisterController::class,'register']);

Route::post('user/verify-mobile',[MobileController::class,'verifyMobile']);

Route::post('user/password/send-reset-code', [MobileController::class, 'sendResetCode']);

Route::post('user/password/reset-password', [MobileController::class, 'resetPassword']);

Route::group(['middleware' => 'auth:sanctum'],function (){
    Route::get('user/logout',[LoginController::class,'logout']);
});
