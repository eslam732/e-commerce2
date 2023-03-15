<?php

use App\Http\Controllers\UserController;
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

Route::post('addtocart/{pid}',[UserController::class,'addToCart']);
Route::post('ordercart',[UserController::class,'orderCart']);
Route::get('getcart',[UserController::class,'getCart']);
Route::post('removefromcart',[UserController::class,'removeFromCart']);
Route::post('cancelcart',[UserController::class,'cancelCart']);
Route::post('comment/{pid}',[UserController::class,'makeComment']);
Route::get('search',[UserController::class,'search']);
Route::get('getnotifications',[UserController::class,'getNotifications']);
Route::get('readnotification/{cid}',[UserController::class,'readNotification']);

