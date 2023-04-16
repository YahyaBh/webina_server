<?php

namespace App\Http\Controllers;

use App\Models\Announcements;
use App\Models\Blogs;
use App\Models\Founders;
use App\Models\Testimonials;
use Exception;
use Illuminate\Http\Request;

class MainController extends Controller
{




    public function getCategories()
    {
    }

    public function getTestimonialsFounders()
    {
        try {
            $founders = Founders::all();
            $testimonials = Testimonials::all();


            return response()->json([
                'testimonials' => $testimonials,
                'founders' => $founders
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getBlogs()
    {
        try {
            $blogs = Blogs::all();
            return response()->json([
                'blogs' => $blogs
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getAnnouncements()
    {
        try {
            $announcements = Announcements::all();
            return response()->json([
                'announcements' => $announcements
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
