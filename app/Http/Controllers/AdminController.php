<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function changeOrderState()
    {
        try {
            
       
        if (!request()->state || !request()->orderId) {
           return response()->json('please enter the state and the order id',400);
        }
        $order=Order::find(request()->orderId);
        if(!$order){
           return response()->json('order not found',404);

        }
        $arrayStates=['pending', 'processing', 'shipped', 'delivered'];
        $state=strtolower(request()->state);
        
        if(!in_array($state,$arrayStates)){
           return response()->json('please enter the right state',400);

        }
        $order->state=$state;
        $order->save();
        return response()->json(["changed the order"=>$order],200);
    
    } catch (\Exception $e) {
            return response()->json(['some error has ocured'=>$e->getMessage()],400);

        }


    }
}
