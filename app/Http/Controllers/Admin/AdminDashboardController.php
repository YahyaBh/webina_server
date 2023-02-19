<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    

    public function __construct() {

    }


    public function index(Request $request) {
    
        $users = User::count();
        $orders = Orders::count();

        return response()->json([
            'users' => $users,
            'orders' => $orders,
        ],200);

    }
}
