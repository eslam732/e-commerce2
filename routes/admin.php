<?php

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;







Route::post('changestate',[AdminController::class,'changeOrderState']);
