<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Orders;
use App\Models\User;
use App\Models\Websites;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrdersController extends Controller
{

    public function orders_all(Request $request)
    {

        $request->validate([
            'user_id' => 'required',
        ]);


        $user = User::where('id', $request->user_id)->first();

        if ($user) {


            $orders = Orders::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();

            return response()->json([
                'status' => 'success',
                'orders' => $orders,
            ], 200);

            if ($orders) {

                $websites = Websites::where('token', $request->get('website_id'))->first();

                return response()->json([
                    'status' => 'success',
                    'orders' => $orders,
                    'websites' => $websites,
                ], 200);
            } else {
                return response()->json([
                    'status' => 'empty',
                    'message' => 'No orders found'
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'User not found'
            ], 500);
        }
    }


    public function order_show(Request $request)
    {
        $request->validate([
            'order_token' => 'required',
            'user_id' => 'required',
        ]);

        $user = User::where('id', $request->user_id)->first();

        if ($user) {
            try {
                $order = Orders::where('order_number', $request->order_token)->first();

                $website = Websites::where('token', $order->website_token)->first();


                return response()->json([
                    'status' => 'success',
                    'order' => $order,
                    'website' => $website,
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'failed',
                    'message' => $e->getMessage(),
                ], 405);
            }
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Something went wrong'
            ], 401);
        }
    }


    public function order_download(Request $request)
    {


        $request->validate([
            'order_token' => 'required',
        ]);

        $order = Orders::where('order_number', $request->order_token)->first();;

        if ($order) {

            if ($order->status === 'completed') {

                if ($order->file) {
                    $file = public_path() . "/uploads/orders/files/" . $order->file;

                    return response()->download($file);
                } else {
                    return response()->json([
                        'message' => 'website files is not found'
                    ], 404);
                }
            } else {
                return response()->json([
                    'message' => 'Webiste order is not completed'
                ], 404);
            }
        } else {
            return response()->json([
                'message' => 'Unable to find order website'
            ], 404);
        }
    }
}
