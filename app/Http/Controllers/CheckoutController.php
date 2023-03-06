<?php

namespace App\Http\Controllers;

use App\Mail\AdminMail;
use App\Models\Orders;
use App\Models\User;
use App\Models\Websites;
use Exception;
use Faker\Provider\uk_UA\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Stripe\Exception\CardException;
use Stripe\StripeClient;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CheckoutController extends Controller
{
    public function paymecntCheck(Request $request)
    {

        $user = User::where('email', $request->user_email)->first();

        if ($user) {
            try {
                $stripe = new \Stripe\StripeClient(
                    env('STRIPE_SECRET_KEY')
                );

                $res = $stripe->tokens->create([
                    'card' => [
                        'number' => $request->number,
                        'exp_month' => $request->exp_month,
                        'exp_year' => 20 . $request->exp_year,
                        'cvc' => $request->cvc,
                    ],
                ]);

                \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

                $response = $stripe->charges->create([
                    'amount' => $request->price * 100,
                    'currency' => 'USD',
                    'description' => $request->description,
                    'source' => $res->id,
                    'receipt_email' => $request->user_email,
                ]);

                $user->update([
                    'orders_total' => $user->orders_total + $request->price,
                ]);


                Orders::create([
                    'user_id' => $request->user_id,
                    'order_number' => $response->id,
                    'status' => 'pending',
                    'grand_total' => $request->price,
                    'item_count' => 1,
                    'is_paid' => true,
                    'payment_method' => 'credit_card',
                    'notes' => $request->description,
                    'website_token' => $request->website_token,
                ]);


                Mail::to('gamesy865@gmail.com')->send(new AdminMail('New Order Has Been Created', $response->id, $user));


                return response()->json([
                    'status' => 'success',
                    'message' => 'Payment successfully created , Thank you!',
                    'response' => $response,
                    'url' => 'http://localhost:3000/payment/success'
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                    'cancel_url' => 'http://localhost:3000/payment/failed',
                ], 400);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found , please try again',
                'cancel_url' => 'http://localhost:3000/payment/failed',
            ], 400);
        }
    }
}
