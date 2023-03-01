<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use Illuminate\Http\Request;

class AdminOrdersController extends Controller
{
    public function index()
    {
        $orders = Orders::all();


        return response()->json(['orders' => $orders], 200);
    }
}
