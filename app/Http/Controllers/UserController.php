<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class UserController extends Controller
{


    private $access_token;

    public function __construct()
    {
        $this->access_token = uniqid(base64_encode(Str::random(40)));
    }

    public function sendVerificationEmail(Request $request)
    {

        // $data = [
        //     'email'  => $request->input('email'),
        //     'token' => $request->input('token'),
        // ];

        // Mail::send($data['email'], ['data1' => $data], function ($m) {
        //     $m->to('gamesy865@gmail.com')->subject('Contact Form Mail!');
        // });


        return response()->json(["message" => "Email sent successfully."]);
    }


    public function verifyEmail($id, $token, $email)
    {

        $user = User::where('id', $id)->first();


        // if ($user->remember_token == $token) {
        if ($user->email == $email) {
            if ($user->email_verified_at) {
                return response()->json([
                    'status' => 'success',
                    "message" => "Email already verified.",
                    'access_token' => $user->remember_token,
                    'user' => $user
                ], 200);
            } else {
                $user->email_verified_at = Carbon::now();
                $user->save();

                return response()->json([
                    'status' => 'success',
                    "message" => "Email verified succefully.",
                    'access_token' => $user->remember_token,
                    'user' => $user
                ], 200);
            }
        } else {
            return response()->json([
                'status' => 'failed',
                "message" => "Something went wrong. Please try again.",
            ], 401);
        }
    }




    public function profile(Request $request)
    {
        $request->validate([
            'remember_token' => 'required',
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

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
            $user = User::create($postArray);
            event(new Registered($user));

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


    public function update(Request $request)
    {
        $user = User::where('email', $request->email)->first();;
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        if (Hash::check($request->password, $user->password)) {
            $user->password = $request->input(Hash::make('new_password'));
        }
        $user->remember_token = $request->input('remember_token');
        $user->update();

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'user' => $user,
            'token' => $user->remember_token,
            'password' => $user->password,
            'hashed_password' => $request->input(Hash::make('password'))
        ], 200);


        // if ($user) {

        //     if($request->hasFile('image')) {

        //     }

        //     $user->name = $request->name;
        //     $user->email = $request->email;
        //     $user->remember_token = $request->remember_token;
        //     dd($user);

        //     if (Hash::check($request->password, $user->password)) {

        //         dd($user);

        //     } else {
        //         return response()->json([
        //             'status' => 'failed',
        //             'message' => 'Password does not match',
        //         ], 402);
        //     }


        //     return response()->json([
        //         'status' => 'success',
        //         'message' => 'User updated successfully',
        //         'user' => $user,
        //         'token' => $user->remember_token,
        //     ], 200);
        // } else {
        //     return response()->json([
        //         'status' => 'failed',
        //         'message' => 'Something went wrong , while updating user',
        //     ], 402);
        // }
    }


    public function logout()
    {
        $cookie = Cookie::forget('token');
        try {
            return response()->json([
                'status' => 'success',
                'message' => 'Signed Out successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Something went wrong , while logging out',
            ], 402);
        }
    }


    public function delete(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password_check' => 'required',
            ''
        ]);


        $user = User::where('email', $request->email)->first();


        if ($user && Hash::check($request->password_check, $user->password)) {
            $user->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'User Deleted successfully',
            ], 200);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Password does not match our records',
            ], 401);
        }
    }
}
