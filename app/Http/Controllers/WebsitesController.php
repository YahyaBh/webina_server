<?php

namespace App\Http\Controllers;

use App\Models\Websites;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class WebsitesController extends Controller
{


    private $access_token;

    public function __construct()
    {
        $this->access_token = uniqid(base64_encode(Str::random(40)));
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
        $request->validate([
            'website_name' => 'required',
            'token' => 'required',
            'price' => 'required',
            'category' => 'required',
            'Developing_Time' => 'required'
        ]);

        try {

            Websites::create($request->post());

            return response()->json([
                'message' => 'Product Created Successfully!!'
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'message' => 'Something goes wrong while creating a product!!'
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
