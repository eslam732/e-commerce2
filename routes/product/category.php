<?php
use App\Http\Controllers\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('addcategory',[CategoryController::class,'addCategory']);
Route::get('getcategoryproducts/{catId}',[CategoryController::class,'categoryProducts']);
