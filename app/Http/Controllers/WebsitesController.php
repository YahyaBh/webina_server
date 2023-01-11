<?php

namespace App\Http\Controllers;

use App\Models\Website;
use App\Models\Websites;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebsitesController extends Controller
{
    public function index(Websites $website)
    {
        return Websites::select('website_name', 'price', 'token', 'category', 'Developing Time')->get();
    }

    public function show(Websites $product)
    {
        return response()->json([
            'product' => $product
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'website_name' => 'required',
            'token' => 'required',
            'price' => 'required',
            'category' => 'required',
            'Developing Time' => ''
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
