<?php

namespace App\Http\Controllers;

use App\Models\Websites;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class WebsitesController extends Controller
{


    private $website_token;

    public function __construct()
    {
        $this->website_token = uniqid(base64_encode(Str::random(20)));
    }


    public function recent_websites()
    {

        $websites = Websites::orderBy('created_at', 'desc')->take(4)->get();


        if ($websites) {
            return response()->json([
                'status' => 'success',
                'websites' => $websites
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Error while getting Websites',
            ]);
        }
    }

    public function index()
    {
        $websites = Websites::get();
        try {
            return response()->json([
                'status' => 'success',
                'websites' => $websites
            ], 200);
        } catch (\Exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error while getting Websites',
            ]);
        }
    }

    public function show(Websites $websites)
    {


        try {




            return response()->json([
                'status' => 'success',
                'websites' => $websites
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {

        try {

            Websites::create([
                'website_name' => $request->website_name,
                'price' => $request->price,
                'category' => $request->category,
                'Developing_time' => $request->Developing_time,
                'image' => $request->image,
                'token' => $this->website_token
            ]);

            return response()->json([
                'message' => 'Product Created Successfully!!'
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'message' => 'Something goes wrong while creating a product!!' . $e
            ], 500);
        }
    }

    public function delete($token)
    {
        Websites::where('token', $token)->delete();


        return response()->json([
            'message' => 'Product Deleted Successfully!!'
        ]);
    }
}
