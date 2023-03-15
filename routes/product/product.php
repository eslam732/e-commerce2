<?php
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('addproduct',[ProductController::class,'addProduct']);
Route::get('getproducts',[ProductController::class,'getProducts']);
Route::get('getoneproduct/{pid}',[ProductController::class,'getOneProduct']);
Route::delete('deleteproduct/{pid}',[ProductController::class,'deleteProduct']);
Route::post('editproduct/{pid}',[ProductController::class,'editProduct']);
