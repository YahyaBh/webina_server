<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Pusher\Pusher;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required',
            'user_id' => 'required',
            'user_token' => 'required',
            'receiver_id' => 'required',
        ]);

        $user = User::where('id', $request->user_id)->first();

        if ($user && $user->remember_token === $request->user_token) {

            $message = $request->input('message');

            $message = new Message;
            $message->message = $request->message;
            $message->sender_id = $request->user_id;
            $message->receiver_id = $request->receiver_id;
            $message->save();

            event(new MessageEvent($message));

            return response()->json(['message' => $message], 200);
        } else {
            return response()->json(['message' => 'Unauthorized User'], 401);
        }
    }



    public function messages(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'user_token' => 'required',
        ]);

        $user = User::where('id', $request->user_id)->first();

        if ($user->remember_token === $request->user_token) {

            $messages = Message::where('sender_id' , $user->id)->orWhere('receiver_id' , $user->id)->get();

            if($messages) {
                return response()->json(['messages' => $messages], 200);
            } else {
                return response()->json(['messages' => 'empty'], 200);
            }

        } else {
            return response()->json(['message' => 'Unauthorized User '], 401);
        }
    }
}
