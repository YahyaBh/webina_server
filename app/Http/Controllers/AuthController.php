<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\User;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class AuthController extends Controller
{


    private $access_token;

    public function redirectToAuth(): JsonResponse
    {
        return response()->json([
            'url' => Socialite::driver('google')
                ->redirect()
                ->getTargetUrl(),
        ]);
    }


    public function handleAuthCallBack()
    {
        try {
            $user = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'success',
                'message' => 'Something went wrong while creating user, please try again' . $e->getMessage(),
            ], 200);
        }
        if (explode("@", $user->email)[1] !== 'company.com') {
            return response()->json([
                'status' => 'success',
                'message' => 'Something went wrong , please try again',
            ], 200);
        }
        $existingUser = User::where('email', $user->email)->first();
        if ($existingUser) {
            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully',
                'token'  => $this->access_token,
                'user' => $existingUser
            ], 200);
        } else {
            $newUser                  = new User;
            $newUser->name            = $user->name;
            $newUser->email           = $user->email;
            $newUser->google_id       = $user->id;
            $newUser->avatar          = $user->avatar;
            $newUser->avatar_original = $user->avatar_original;
            $newUser->remember_token = $this->access_token;
            User::create($newUser);

            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully',
                'token'  => $this->access_token,
                'user' => $newUser
            ], 200);
        }
    }
}
