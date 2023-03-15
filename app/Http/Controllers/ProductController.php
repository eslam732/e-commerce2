<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function addProduct()
    {
        $request = request();
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'quantity' => 'required',
            'price' => 'required',
            'category_name' => 'required',
        ];
        $validation = Validator::make(request()->all(), $rules);

        if ($validation->fails()) {
            return response()->json([$validation->errors()], 400);
        }
        $data = request()->all();

      
        if (request()->hasFile('image')) {

         
        $path = request()->file('image')->store('productsImages');


            $data['image'] = $path;

        }
        $data['user_id'] = Auth()->id();
        $data['category_id'] = 1;

        $product = Product::create($data);
        return response()->json(['product' => $product], 201);
    }

    public function getProducts()
    {
        $products = Product::with('user')->orderBy('name', 'desc')->paginate(10);
        return response()->json(['products' => $products, 200]);

    }

    public function getOneProduct($pid)
    {
        $product = Product::find($pid);
        if (!$product) {
            return response()->json(['product not found'], 404);
        }

        $product->user;
        $product->product_comments;
        if (count($product->product_comments)) {
            foreach ($product->product_comments as $PDC) {
                $PDC->user;
            }

        }

        return response()->json(['product' => $product, 200]);

    }
    public function deleteProduct($pid)
    {
        $product = Product::find($pid);
        if (!$product) {
            return response()->json('product not found', 404);
        }

        if (Auth()->id() != $product['user_id'] && Auth()->user()->type == 'regular') {
            return response()->json('not allowed', 401);
        }
        $product->delete();

        return response()->json('deleted', 202);
    }

    public function editProduct($pid)
    {

        $product = Product::find($pid);
        if (!$product) {
            return response()->json('product not found', 404);
        }

        if (Auth()->id() != $product['user_id']) {
            return response()->json('not allowed', 401);
        }
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'quantity' => 'required',
            'price' => 'required',
        ];
        $validation = Validator::make(request()->all(), $rules);

        if ($validation->fails()) {
            return response()->json([$validation->errors()], 400);
        }

        $product->name = request('name');
        $product->description = request('description');
        $product->quantity = request('quantity');
        $product->save();

        return response()->json('updated', 202);
    }

    public function getPhoto()
    {
        if (!request()->image) {
            return response()->json('enter the image name ', 400);

        }

        $path = storage_path('app/' . request()->image);

        if (!File::exists($path)) {
            return response()->json('image not found ', 404);

        }

        $file = File::get($path);

        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return response()->stream(function () use ($path) {
            $file = fopen($path, 'rb');
            fpassthru($file);
            fclose($file);
        }, 200, [
            'Content-Type' => $type,
            'Content-Disposition' => 'inline; filename="'.basename($path).'"'
        ]);
    }


}
