<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    

    public function addCategory()
    {
        $rules = [
            'name' => 'required|unique:categories|min:4'
        ];
        $validation = Validator::make(request()->all(), $rules);

        if ($validation->fails()) {
            return response()->json([$validation->errors()], 400);
        }
        if(Auth()->user()->type!='admin'){
            return response()->json('not allowed', 401);
            
        }

    $cat=Category::create(request()->all());
    return response()->json(['category'=>$cat],201);

       
    }

    public function categoryProducts($catId)
    {
        $cat=Category::find($catId);
        if(!$cat){
            return response()->json('not found', 400);
            
        }

        $catPro=$cat->products;
        return response()->json(["products "=>$catPro], 200);


    }

    
}
