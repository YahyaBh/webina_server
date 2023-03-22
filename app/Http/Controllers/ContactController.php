<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store_message(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'name' => 'required|min:8',
                'message' => 'required|max:400',
            ]);


            Contact::create([
                'email' => $request->email,
                'name' => $request->name,
                'message' => $request->message,
            ]);


            return response()->json([
                'status' => 'success',
                'message' => 'Message sent successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed With : ' . $e->getMessage(),
            ], 401);
        }
    }
}
