<?php

namespace App\Http\Controllers;

use App\Events\NotificationPusher as EventsNotificationPusher;
use App\Mail\EmailVerification;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use NotificationPusher;

class UserController extends Controller
{


    private $access_token;

    public function __construct()
    {
        $this->access_token = uniqid(base64_encode(Str::random(40)));
    }

    public function sendVerificationEmail(Request $request)
    {

        $data = [
            'email'  => $request->input('email'),
            'token' => $request->input('token'),
        ];

        $user = User::where('email', $data['email'])->first();


        if ($user) {

            Mail::to($user->email)->send(new EmailVerification($user, $data['token']));

            return response()->json(["message" => "Email sent successfully.", 'email' => $user->email], 200);
        } else {
            return response()->json(["message" => "Email didn't sent , please try again later."], 401);
        }
    }



    public function verifyEmail($email)
    {

        $user = User::where('email', $email)->first();
        $timeNow = Carbon::now();


        if ($user && $user->created_at < $timeNow) {
            if ($user) {

                $user->email_verified_at = Carbon::now();
                $user->save();


                if ($user->email_verified_at == null) {
                    return response()->json([
                        'status' => 'success',
                        "message" => "Email verified succefully.",
                        'access_token' => $user->remember_token,
                        'user' => $user,
                        'email' => $user->email
                    ], 200);
                } else if ($user->email_verified_at !== null) {
                    return response()->json([
                        'status' => 'success',
                        "message" => "Email already verified.",
                        'access_token' => $user->remember_token,
                        'user' => $user,
                        'email' => $user->email
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 'failed',
                        "message" => "Something went wrong. Please try again.",
                    ], 401);
                }
            } else {
                return response()->json([
                    'status' => 'failed',
                    "message" => "Something went wrong. Please try again.",
                ], 401);
            }
        } else {
            User::where('email', $email)->delete();

            return response()->json([
                'status' => 'failed',
                "message" => "The email link expired please try to register again.",
            ], 401);
        }
    }

    public function verifyEmailSign(Request $request)
    {

        $user = User::where('email', $request->email)->first();

        if ($user) {
            if ($user->email_verified_at !== null) {
                return response()->json([
                    'status' => 'success',
                    "message" => "Email verified succefully.",
                    'access_token' => $user->remember_token,
                    'user' => $user,
                    'email' => $user->email
                ], 200);
            } else {
                return response()->json([
                    'status' => 'failed',
                    "message" => "Email not verifid , please try again.",
                ], 401);
            }
        } else {
            return response()->json([
                'status' => 'failed',
                "message" => "User not found , please try again.",
            ], 401);
        }
    }

    public function checkVerify(Request $request)
    {

        $user = User::where('email', $request->input('email'))->first();



        if ($user && $user->email_verified_at && $user->remember_token == $request->input('token')) {
            return response()->json([
                'status' => 'success',
                "message" => "Email verified succefully.",
                'access_token' => $user->remember_token,
                'user' => $user
            ], 200);
        } else if ($user->email_verified_at !== null) {
            return response()->json([
                'status' => 'failed',
                "message" => "Email is not verified verified.",
            ], 401);
        }
    }


    public function verifyEmailget($email, $token)
    {
        $user = User::where('email', $email)->first();



        if ($user && $user->email_verified_at && $user->remember_token === $token) {

            return response()->json([
                'status' => 'success',
                "message" => "Email verified succefully.",
                'access_token' => $user->remember_token,
                'user' => $user
            ], 200);
        } else if ($user->email_verified_at === null || $user->remember_token !== $token) {
            return response()->json([
                'status' => 'failed',
                "message" => "Email is not verified verified.",
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
                'access_token' => $user->remember_token,
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
                if ($user->email_verified_at) {
                    return response()->json([
                        'status' => 'success',
                        'message' => $user->name . ' Signed In successfully',
                        'access_token' => $user->remember_token,
                        'user' => $user,
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Email is not verified',
                    ], 400);
                }
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
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password',
        ]);



        $emailFound = User::where('email', $request->email)->first();

        if (!$emailFound) {

            $postArray = $request->all();
            $postArray['full_name'] = $request->first_name . ' ' . $request->last_name;
            $postArray['remember_token'] = $this->access_token;

            $userFNfound = User::where('full_name', $postArray['full_name'])->first();

            if (!$userFNfound) {
                User::create($postArray);

                return response()->json([
                    'status' => 'success',
                    'message' => $request->name . 'Registred successfully',
                    'access_token' =>  $this->access_token,
                    'user' => $postArray,
                ]);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'First Name Or Last Name already exists',
                ], 401);
            }
        } else if ($emailFound) {
            return response()->json([
                'message' => 'Email already exists'
            ], 400);
        } else {
            return response()->json([
                'message' => 'Something went wrong , please try again'
            ], 403);
        }
    }


    public function update(Request $request)
    {

        $user = User::where('email', 'bohsineyahya@gmail.com')->first();




        if ($request->name || $request->email || $request->password) {
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            if (Hash::check($request->password, $user->password)) {
                $user->password = $request->input(Hash::make('new_password'));
            }
            $user->remember_token = $request->input('remember_token');
            $user->update([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('new_password')),
            ]);
        }




        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'user' => $user,
        ], 200);
    }

    public function updateAvatar(Request $request)
    {

        $request->validate([
            'avatar' => 'required|max:2048',
            'user_id' => 'required|numeric|exists:users,id',
        ]);


        $user = User::findOrFail($request->user_id);

        if ($user && $user->remember_token === $request->user_token) {
            if ($request->has('avatar')) {
                $image = $request->file('avatar');
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $image->move('uploads/users/', $filename);
                
                //save the image
                $user->update([
                    'avatar' => $filename
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Image updated successfully',
                ], 200);
            }
        }
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
