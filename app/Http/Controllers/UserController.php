<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartProducts;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductComment;
use App\Models\User;
use App\Notifications\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Mockery\Expectation;

class UserController extends Controller
{

    public function addToCart($pid)
    {
        try {

            $product = Product::find($pid);
            if (!$product) {
                return response()->json(['prduct not found'], 404);
            }

            if (!request()->has('amount')) {
                return response()->json(['amount is required'], 400);
            }

            if (request()->amount > $product->quantity) {
                return response()->json(['amount is more than that in shop'], 404);
            }
            $userId = Auth()->id();
            $cart = Cart::where('user_id', Auth()->id())->first();

            if (!$cart) {
                $cart = Cart::create(['user_id' => $userId]);
            }

            $cartProduct = CartProducts::where('user_id', $userId)->where('product_id', $pid)->first();

            if (!$cartProduct) {
                $cartProduct = [];
                $cartProduct['user_id'] = $userId;
                $cartProduct['product_id'] = $pid;
                $cartProduct['cart_id'] = $cart->id;
                $cartProduct['amount'] = request()->amount;
                $cartProduct = CartProducts::create($cartProduct);

            } else {
                $cartProduct['amount'] += request()->amount;
                $cartProduct->save();
            }

            return response()->json(['added to cart' => $cartProduct], 200);

        } catch (\Exception$e) {
            return response()->json(['some error has occured' => $e->getMessage()], 500);

        }

    }

    public function orderCart()
    {
        if (!request()->cartId) {
            return response()->json(['enter the cartId'], 400);
        }

        try {
            $cart = Cart::find(request()->cartId);
            if (!$cart) {
                return response()->json(['cart was not found'], 404);
            }
            $order = Order::where('cart_id', request()->cartId)->where('user_id', Auth()->id())->first();
            if ($order) {
                return response()->json(['allready ordered' => $order], 400);
            }

           
            for ($i = 0; $i < count($cart->cart_products); $i++) {
                if ($cart->cart_products[$i]->amount > $cart->cart_products[$i]->product->quantity) {
                    return response()->json(
                        ['amount of this product is more than in the shop' => $cart->cart_products[$i]], 400);
                }
            }   

            $order = Order::create(['user_id' => Auth()->id(), 'cart_id' => request()->cartId]);
            $cart->state="ordered";
            $cart->save();
            return response()->json(['Ordered' => $order], 201);

        } catch (\Exception$e) {
            return response()->json(['some error has occured' => $e->getMessage()], 500);
        }

    }

    public function removeFromCart()
    {
        $reqData = request()->all();
        $rules = [
            'cartProductId' => 'required',
            'amount' => 'required',
        ];
        $validation = Validator::make($reqData, $rules);

        if ($validation->fails()) {
            return $validation = $validation->errors();
        }
        $cartProduct = CartProducts::find($reqData['cartProductId']);

        if (!$cartProduct) {

            return response()->json(['cartProduct not found'], 404);

        }

        if ($cartProduct->user_id != Auth()->id()) {
            return response()->json(['not allowed'], 405);
        }
        if ($reqData['amount'] > $cartProduct->amount) {

            return response()->json(['amount is more than that you have'], 400);
        }
        $cartProduct->amount -= $reqData['amount'];

        if ($cartProduct->amount == 0) {
            $cartProduct->delete();
            return response()->json(['removed'], 200);
        }

        $cartProduct->save();
        return response()->json(['removed'], 200);

    }

    public function cancelCart()
    {
        $reqData = request()->all();
        $rules = [
            'cartId' => 'required',
        ];
        $validation = Validator::make($reqData, $rules);

        if ($validation->fails()) {
            return $validation = $validation->errors();
        }
        try { $cart = Cart::find($reqData['cartId']);
            if (!$cart) {
                return response()->json(['cart not found'], 404);

            }

            if ($cart->user_id != Auth()->id()) {
                return response()->json(['not allowed'], 405);
            }
            //delete cartProducts


          CartProducts::where('cart_id',request()->cartId)->delete();

            $cart->delete();
            return response()->json(['removed'], 200);
        } catch (Expectation $e) {
            return response()->json($e, 400);

        }

    }

    public function getCart()
    {
        $userId = Auth()->id();

        $cart = Cart::where('user_id', $userId)->first();

        if (!$cart) {
            return response()->json(['there is no cart for you or cart not found'], 404);
        }
        $cart->cart_products;

        return response()->json(['cart ' => $cart], 200);
    }

    public function makeComment($pid)
    {

        $product = Product::find($pid);
        if (!$product) {
            return response()->json('product not found', 404);
        }

        if (!request()->has('content') || request()->content == null) {
            return response()->json('content cant be empty', 206);

        }

        $comData = request()->all();
        $comData['user_id'] = Auth()->id();
        $comData['product_id'] = $pid;
//$notifiableUser=Product::where('id',$pid)->get()[0]->user_id;
        $notifiableUser = Product::where('id', $pid)->with('user')->get()->pluck('user')[0];
        if (!$notifiableUser) {
            return response()->json(['cant create comment due to server error or user of product is not available'], 400);

        }
        $comment = ProductComment::create($comData);
        $notifiableUser->notify(new Comment(Auth()->user(), $product, $comment));

        return response()->json(['comment created' => $comment], 201);

    }

    public function search()
    {
        if (!request()->has('searchKey')) {
            return response()->json('searchKey cant be empty', 206);

        }

        $pre = request('searchKey');
        // $data=[];

        $data = DB::select(DB::raw("
        SELECT * FROM products
        where name LIKE '%$pre%' OR description LIKE '%$pre%' or
        id In(select id from categories where name like '%$pre%');"));

        return response()->json([$data], 200);

    }

    public function getNotifications()
    {
        $user = User::find(Auth()->id());
        $notifications = $user->unreadNotifications;
        return response()->json(["notifications" => $notifications]);

    }

    public function readNotification()
    {

    }
}
