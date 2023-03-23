<?php

namespace App\Http\Controllers;

use App\Mail\AdminMail;
use App\Models\Orders;
use App\Models\Payment;
use App\Models\User;
use App\Models\Websites;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Srmklive\PayPal\Services\ExpressCheckout;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{


    public $westernunion = 10;
    public $moneygram = 10;


    private $payment_token;

    public function __construct()
    {
        $this->payment_token = uniqid(base64_encode(Str::random(40)));
    }

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

                $admins = User::where('role', 'admin')->get();

                foreach ($admins as $admin) {
                    Mail::to($admin->email)->send(new AdminMail('New Order Has Been Created', $response->id, $user));
                }



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


    public function paypalcheckout(Request $request)
    {

        $request->validate([
            'website_name' => 'required',
            'price' => 'required',
            'description' => 'required',
            'website_token' => 'required',
        ]);

        $data = [];
        $data['items'] = [
            [
                'name' => $request->website_name,
                'price' => $request->price,
                'description' => $request->description,

            ]
        ];

        $data['invoice_id'] = $request->website_token;
        $data['return_url'] = 'http://localhost:3000/payment/success';
        $data['cancel_return_url'] = 'http://localhost:3000/payment/failed';

        $data['total'] = $request->price;

        $provider = new ExpressCheckout;

        $response = $provider->setExpressCheckout($data);

        $response = $provider->setExpressCheckout($data, true);


        return response()->json([
            'paypal_link' => $response['paypal_link'],
            'message' => $response['message'],
        ], 200);
    }


    public function success(Request $request)
    {
        $provider = new ExpressCheckout;
        $response = $provider->getExpressCheckoutDetails($request->token);

        if (in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])) {
            dd('Your payment was successfully.');
        }

        dd('Please try again later.');
    }


    public function cashCheckout(Request $request)
    {

        $request->validate([
            'website_token' => 'required',
            'method' => 'required',
            'full_name' => 'required',
            'city' => 'required',
            'country' => 'required',
            'postal_code' => 'required',
            'phone' => 'required',
        ]);



        $website = Websites::where('token', $request->website_token);

        if ($website) {

            $payment = Payment::create([
                'payment_token' => $this->payment_token,
                'website_token' => $website->token,
                'website_name' => $website->website_name,
                'user_id' => auth()->user()->id,
                'user_full_name' => $request->full_name,
                'user_email' => $request->email,
                'user_country' => $request->country,
                'user_city' => $request->city,
                'user_postal_code' => $request->postal_code,
                'user_phone' => $request->phone,
                'amount' => $website->price + $request->method === 'westernunion' ? $this->westernunion : $this->moneygram,
                'method' => $request->method === 'westernunion' ? 'westernunion' : 'moneygram',
            ]);

            $admins = User::where('role', 'admin')->get();
            $user = User::where('id', auth()->user()->id)->first();

            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new AdminMail('New Order Has Been Created', auth()->user()->id, $user));
            }

            return response()->json([
                'status' => 'success',
                'payment_token' => $payment->payment_token,
                'message' => 'Payment successfully created, please send the specified amount to this credintals!',
            ], 200);
        } else {

            return response()->json([
                'status' => 'error',
                'message' => 'Website not found',
            ], 400);
        }
    }
}
