<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('signup',[AuthController::class,'singup']);
Route::post('login',[AuthController::class,'login']);
Route::get('UnAuthorized',[AuthController::class,'UnAuthorized'])->name('UnAuthorized'); 
Route::post('forgotpassword',[AuthController::class,'forgotPassword']);
Route::post('checkcode',[AuthController::class,'checkCode']);
Route::post('resetpassword',[AuthController::class,'resetPAssword']);
