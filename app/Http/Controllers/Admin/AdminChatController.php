<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class AdminChatController extends Controller
{
    public function index(Request $request)
    {

        $request->validate([
            'admin_id' => 'required',
            'admin_token' => 'required',
        ]);

        $admin = Admin::where('id', $request->admin_id)->first();

        if ($admin && $admin->remember_token == $request->admin_token) {

            $messagesData = Message::where('receiver_token', $request->admin_id)->get();

            $ids_users_senders = [];

            foreach ($messagesData as $nameData) {
                array_push($ids_users_senders, $nameData->sender_id);
            }

            $ids_users_senders = array_unique($ids_users_senders);

            $users = User::whereIn('id', $ids_users_senders)->get();

            return response()->json([
                'status' => 'success',
                'chatNames' => $users
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }
    }


    public function user_messages(Request $request)
    {

        $request->validate([
            'admin_id' => 'required',
            'admin_token' => 'required',
            'sender_token' => 'required',
        ]);


        $admin = Admin::where('id', $request->admin_id)->first();

        if ($admin) {

            $messages = Message::where('sender_id', $request->sender_token)->get();


            if ($messages) {
                return response()->json([
                    'status' => 'success',
                    'messages' => $messages
                ], 200);
            } else {
                return response()->json([
                    'status' => 'success',
                    'messages' => 'No messages found'
                ], 200);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }
    }
}
