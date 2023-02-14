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
        ]);



        $message = $request->input('message');

        event(new MessageEvent($message));

        return response()->json(['message' => $message], 200);
    }



    public function messages(Request $request)
    {
        $messages = Message::all();

        return response()->json(['messages' => $messages], 200);
    }
}
