<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\User;
use App\Models\Websites;
use Exception;
use Faker\Provider\uk_UA\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\Exception\CardException;
use Stripe\StripeClient;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CheckoutController extends Controller
{
    public function paymecntCheck(Request $request)
    {
        try {
            $stripe = new \Stripe\StripeClient(
                env('STRIPE_SECRET_KEY')
            );

            $res = $stripe->tokens->create([
                'card' => [
                    'number' => $request->card_number,
                    'exp_month' => $request->exp_month,
                    'exp_year' => $request->exp_year,
                    'cvc' => $request->cvc,
                ],
            ]);

            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

            $response = $stripe->charges->create([
                'amount' => $request->price,
                'currency' => 'USD',
                'description' => $request->description,
                'source' => 'tok_visa',
            ]);


            return response()->json([$response], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
