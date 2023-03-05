<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Analyzer;
use App\Models\Orders;
use App\Models\User;
use App\Models\Websites;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{


    public function __construct()
    {
    }

    public function getOrders(Request $request)
    {

        $request->validate([
            'type' => 'required',
        ]);

        $users = User::all();
        $websites = Websites::all();

        if ($request->type == 'all') {
            $orders =  Orders::all();

            return response()->json([
                'message' => 'Success',
                'orders' => $orders,
                'users' => $users,
                'websites' => $websites,
                'request' => $request->type
            ], 200);
        } else {
            $orders = Orders::where('status', $request->type)->get();
            return response()->json([
                'message' => 'Success',
                'orders' => $orders,
                'users' => $users,
                'websites' => $websites,
                'request' => $request->type

            ], 200);
        }
    }


    public function getOrder(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $order = Orders::where('order_number', $request->id)->first();

        if ($order) {

            $user  = User::where('id', $order->user_id)->first();


            if ($user) {

                $website = Websites::where('token', $order->website_token)->first();

                if ($website) {

                    return response()->json([
                        'message' => 'Success',
                        'order' => $order,
                        'client' => $user,
                        'website' => $website,
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'Websites not found',
                    ], 404);
                }
            } else {
                return response()->json([
                    'message' => 'User not found',
                ], 404);
            }
        } else {

            return resposne()->json([
                'message' => 'Order not found',
            ], 404);
        }
    }

    public function index()
    {

        $users = User::count();
        $orders = Orders::count();

        $users_array = Analyzer::where('data_name', 'Users')->get();
        $orders_array = Analyzer::where('data_name', 'Orders')->get();


        $canceled_orders = Orders::where('status', 'canceled')->count();
        $pending_orders = Orders::where('status', 'pending')->count();

        $recenetly_orders = Orders::orderBy('created_at', 'desc')->limit('5')->get();
        $recenetly_users = User::orderBy('created_at', 'desc')->limit('5')->get();


        return response()->json([
            'users' => $users,
            'orders' => $orders,
            'users_array' => $users_array,
            'orders_array' => $orders_array,
            'recenetly_orders' => $recenetly_orders,
            'recenetly_users' => $recenetly_users,
            'canceled_orders' => $canceled_orders,
            'pending_orders' => $pending_orders,
        ], 200);
    }
}
