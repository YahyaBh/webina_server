<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Orders;
use App\Models\User;
use App\Models\Websites;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrdersController extends Controller
{

    public function orders_all(Request $request)
    {

        $request->validate([
            'user_token' => 'required',
        ]);

        $user = User::where('remember_token', 'NGRPbnVyU1AzNDJ2TGVCQzlJQ0VIZEhsU1liOTFBNWxXcWhjQlhyVQ==63e534dc5194e')->first();

        $orders = Orders::where('user_id', $user->id)->get();

        return response()->json([
            'status' => 'success',
            'orders' => $orders,
        ], 200);

        if ($orders) {

            $websites = Websites::where('token', $request->get('website_id'))->first();

            return response()->json([
                'status' => 'success',
                'orders' => $orders,
            ], 200);
        } else {
            return response()->json([
                'status' => 'empty',
                'message' => 'No orders found'
            ], 500);
        }
    }


    public function order_show(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'token' => 'required',
            'order_token' => 'required'
        ]);


        try {
            $order = Orders::where('token', $request->order_token)->where('user_id', $request->user_id)->first();

            return response()->json([
                'status' => 'success',
                'order' => $order,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Order not found'
            ]);
        }
    }
}
