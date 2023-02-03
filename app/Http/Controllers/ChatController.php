<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function message(Request $request) {
        
        event(new MessageEvent($request->input('username'), $request->input('message')));

        return [];
    }
}
