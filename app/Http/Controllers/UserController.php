<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserController extends Controller
{


    private $access_token;

    public function __construct()
    {
        $this->access_token = uniqid(base64_encode(Str::random(40)));
    }



    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        // $hashedPassword = Hash::make($request->password);

        $user = User::where('email', $request->email)->first();

        // if (Hash::check($user->password, $hashedPassword)) {
        return response()->json([
            'message' => 'Login successful',
            'access_token' => $this->access_token,
            'user' => $user
        ]);
        // } else {
        //     return response()->json([
        //         'message' => 'Password does not match'
        //     ], 401);
        // }
    }



    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);


        $emailFound = User::where('email', $request->email)->first();

        if ($emailFound) {
            return response()->json([
                'message' => 'Email already exists'
            ], 400);
        } else {
            $postArray = $request->all();
            // $postArray['password'] = Hash::make($request->password);

            $user = User::create($postArray);


            return response()->json([
                'status' => 'success',
                'access_token' => $this->access_token,
                'user' => $postArray
            ]);
        }
    }
}
