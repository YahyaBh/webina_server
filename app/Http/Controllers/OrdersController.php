<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\User;
use App\Models\Websites;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrdersController extends Controller
{

    private $order_token;


    public function __construct()
    {
        $this->order_token = Str::random(24);
    }

    public function orders_all(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'token' => 'required',
        ]);

        $user = User::where('id', $request->user_id)->first();

        if ($user && $user->remember_token === $request->token) {
            $orders = Orders::where('user_id', $user->id)->get();

            $websites = Websites::where('token' , $orders->token)->get();

            return response()->json([
                'status' => 'success',
                'orders' => $orders,
                'websites' => $websites
            ], 200);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Unauthorized user access'
            ], 401);
        }
    }

    public function create_order(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'token' => 'required',
            'website_token' => 'required',
        ]);

        try {
            $postOrder = $request->all();
            $postOrder['token'] = $this->order_token;

            Orders::create($postOrder);

            return response()->json([
                'status' => 'success',
                'message' => 'Order created successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order creation failed'
            ]);
        };
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
