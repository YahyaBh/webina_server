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

        if ($request->has('type') && $request->type == 'All') {

            $users = User::all();
            $websites = Websites::all();

            if ($request->type == 'All') {
                $orders =  Orders::all();
            } else {
                $orders = Orders::where('status', $request->type)->get();
            }


            return response()->json([
                'message' => 'Success',
                'orders' => $orders,
                'users' => $users,
                'websites' => $websites,
                '$request' => 'HH',
            ], 200);
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
