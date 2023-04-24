<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use App\Models\Admin;
use App\Models\User;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Pusher\Pusher;

class ChatController extends Controller
{


    public function getAdminToChat(Request  $request)
    {

        $request->validate([
            'user_id' => 'required',
            'sender_id' => 'required',
        ]);

        $user = User::where('id', $request->user_id)->first();

        if ($user) {

            $messages = Message::where('sender_id', $user->id);


            if ($messages) {
                return response()->json(['messages' => $messages], 200);
            } else {

                $admin = User::where('role', 'admin')->random()->first();

                if ($admin) {
                    return response()->json([
                        'status' => 'success',
                        'reciever_id' => $admin->id
                    ], 200);
                } else {

                    return response()->json([
                        'status' => 'success',
                        'reciever_id' => $admin->id
                    ], 200);
                }
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'User not found'], 404);
        }
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required',
        ]);

        $user = User::where('id', Auth::user()->id)->first();

        $messages = Message::where('sender_id', $user->id)->first();

        $message = $request->input('message');

        if ($messages) {
            $admin = User::where('id', $messages->reciever_id)->first();
            if ($user && $admin) {
                Message::create([
                    'message' => $message,
                    'sender_id' => Auth::user()->id,
                    'reciever_id' => $messages->reciever_id,
                ]);

                $messages = Message::all();

                // Generate a new timestamp
                $timestamp = time();

                // Trigger a Pusher event with the new timestamp
                $pusher = new Pusher(
                    env('PUSHER_APP_KEY'),
                    env('PUSHER_APP_SECRET'),
                    env('PUSHER_APP_ID'),
                    [
                        'cluster' => env('PUSHER_APP_CLUSTER'),
                        'useTLS' => true
                    ]
                );
                $pusher->trigger('my-channel', 'my-event', [
                    'message' => $message,
                    'timestamp' => $timestamp,
                ]);

                return response()->json(['message' => $message], 200);
            } else {
                return response()->json(['message' => 'Unauthorized User'], 401);
            }
        } else {
            $admin = User::where([['role', 'admin'], ['disponible', 'yes']])->inRandomOrder()->first();
            if ($user && $admin) {
                Message::create([
                    'message' => $message,
                    'sender_id' => Auth::user()->id,
                    'reciever_id' => $admin->id
                ]);

                $admin->update(['disponible' => 'no']);

                $messages = Message::all();

                // Generate a new timestamp
                $timestamp = time();

                // Trigger a Pusher event with the new timestamp
                $pusher = new Pusher(
                    env('PUSHER_APP_KEY'),
                    env('PUSHER_APP_SECRET'),
                    env('PUSHER_APP_ID'),
                    [
                        'cluster' => env('PUSHER_APP_CLUSTER'),
                        'useTLS' => true
                    ]
                );
                $pusher->trigger('my-channel', 'my-event', [
                    'message' => $message,
                    'timestamp' => $timestamp,
                ]);

                return response()->json(['message' => $message], 200);
            } else {
                return response()->json(['message' => 'Unauthorized User'], 401);
            }
        }
    }



    public function messages()
    {

        $user = User::where('id', Auth::user()->id)->first();

        if ($user) {

            $messages = Message::find($user->id)->get();


            return response()->json(['messages' => $messages], 200);
        } else {
            return response()->json(['message' => 'Unauthorized User'], 401);
        }
    }
}
