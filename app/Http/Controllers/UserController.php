<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
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


    public function profile(Request $request)
    {
        $request->validate([
            'remember_token' => 'required',
            'email' => 'required|email',
        ]);

        $user = User::where('remember_token', $request->remember_token)->first();

        if ($user !== null) {
            return response()->json([
                'status' => 'success',
                'access_token' => $this->access_token,
                'user' => $user,
            ], 200);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Something went wrong',
            ], 401);
        }
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
            $postArray['remember_token'] = $this->access_token;

            User::create($postArray);

            return response()->json([
                'status' => 'success',
                'message' => $request->name . ' Signed Up successfully',
                'access_token' => $this->access_token,
                'user' => $postArray
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



    public function logout()
    {
        Session::flush();

        return response()->json([
            'status' => 'success',
            'message' => 'Signed Out successfully',
        ], 200);
    }
}
