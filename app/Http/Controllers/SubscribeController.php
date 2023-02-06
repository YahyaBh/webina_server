<?php

namespace App\Http\Controllers;

use App\Models\Subscribe;
use Exception;
use Illuminate\Http\Request;

class SubscribeController extends Controller
{














    public function subscribe(Request $request)
    {

        $request->validate([
            'email' => 'required|email'
        ]);


        try {
            Subscribe::create([
                'email' => $request->email
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Thank you for subscribing'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Sorry, something went wrong' , $e
            ], 500);
        }
    }
}
