<?php

namespace App\Http\Controllers;

use App\Models\Reviews;
use App\Models\User;
use App\Models\Websites;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class WebsitesController extends Controller
{


    private $website_token;

    public function __construct()
    {
        $this->website_token = uniqid(base64_encode(Str::random(20)));
    }


    public function download_website(Request $request)
    {
        $request->validate([
            'pdf_theme_name' => 'required',
        ]);

        $file = public_path() . "/uploads/websites/themes/$request->pdf_theme_name";

        return response()->download($file);
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

    public function show($token)
    {

        $website = Websites::where('token', $token)->first();

        $related_websites = Websites::where('category', $website->category)->get();

        $reviews = Reviews::where('website_token', $website->token)->get();

        $ratings = Reviews::where('website_token', $website->token)->get(['rating']);

        try {

            return response()->json([
                'status' => 'success',
                'website' => $website,
                'related_websites' => $related_websites,
                'reviews' => $reviews,
                'total_users' => User::all()->count(),
                'ratings' => $ratings
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
            'price' => 'required',
            'description' => 'required',
            'category' => 'required',
            'Developing_Time' => 'required',
            'image' => 'required|image',
            'stars' => 'required',
            'theme' => 'required',
            'specifications' => 'required',
            'website_old_price' => 'required'
        ]);

        try {

            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $image->move('uploads/websites/', $filename);

            $theme = $request->file('theme');
            $themeName = time() . '.' . $theme->getClientOriginalExtension();
            $theme->move('uploads/websites/themes', $themeName);

            Websites::create([
                'image' => $filename,
                'website_name' => $request->website_name,
                'description' => $request->description,
                'price' => $request->price,
                'old_price' => $request->old_price,
                'category' => $request->category,
                'developing_Time' => $request->Developing_Time,
                'stars' => $request->stars,
                'theme_document' => $themeName,
                'token' => $this->website_token,
                'specifications' => $request->specifications
            ]);

            return response()->json([
                'message' => 'Product Created Successfully!!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something goes wrong while creating a product!!' . $e->getMessage()
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
