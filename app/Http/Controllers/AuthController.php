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

    public function redirectToAuth(): JsonResponse
    {
        return response()->json([
            'url' => Socialite::driver('google')
                ->redirect()
                ->getTargetUrl(),
        ]);
    }


    public function handleAuthCallback()
    {
        /** @var SocialiteUser $socialiteUser */
        $socialiteUser = Socialite::driver('google')->user();


        /** @var User $user */
        // $user = User::query()
        //     ->firstOrCreate(
        //         [
        //             'email' => $socialiteUser->getEmail(),
        //         ],
        //         [
        //             'email_verified_at' => now(),
        //             'name' => $socialiteUser->getName(),
        //             'google_id' => $socialiteUser->getId(),
        //             'avatar' => $socialiteUser->getAvatar(),
        //         ]
        //     );

        return response()->json([
            'user' => $user,
            'access_token' => $user->createToken('google-token')->plainTextToken,
            'token_type' => 'Bearer',
        ]);

        dd($user);
    }
}
