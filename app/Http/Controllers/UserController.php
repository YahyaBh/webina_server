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

        $user = User::where('email', $request->email)->first();


        if ($user) {
            if (Hash::check($request->password, $user->password)) {

                return response()->json([
                    'status' => 'success',
                    'message' => $user->name . ' Signed In successfully',
                    'access_token' => $this->access_token,
                    'user' => $user,
                ], 200);
            } else if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Password does not match',
                ], 401);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Something went wrong',
                ], 401);
            }
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'User does not exist',
            ], 401);
        }
    }



    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password',
        ]);


        $emailFound = User::where('email', $request->email)->first();

        if (!$emailFound) {

            $postArray = $request->all();

            User::create($postArray);
            $userFounded = User::where('email', $request->email);

            return response()->json([
                'status' => 'success',
                'message' => $request->name . ' Signed Up successfully',
                'access_token' => $this->access_token,
                'user' => $userFounded
            ]);
        } else if ($emailFound) {
            return response()->json([
                'message' => 'Email already exists'
            ], 400);
        } else {
            return response()->json([
                'message' => 'Something went wrong'
            ], 403);
        }
    }
}
